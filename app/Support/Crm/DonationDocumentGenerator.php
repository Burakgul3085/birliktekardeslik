<?php

namespace App\Support\Crm;

use App\Models\DocumentTemplate;
use App\Models\Donation;
use App\Models\DonationDocument;
use App\Models\Setting;
use Dompdf\Dompdf;
use Dompdf\Options;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DonationDocumentGenerator
{
    public function generate(Donation $donation, string $type, bool $regenerate = false): DonationDocument
    {
        $donation->loadMissing(['donor', 'donationType', 'paymentMethod', 'project']);

        $template = DocumentTemplate::query()
            ->where('type', $type)
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('sort_order')
            ->first();

        if (! $template) {
            throw new \RuntimeException('Bu belge türü için aktif şablon bulunamadı.');
        }

        if (! $regenerate) {
            $existing = $donation->documents()
                ->where('type', $type)
                ->latest('generated_at')
                ->first();

            if ($existing && Storage::disk('public')->exists($existing->pdf_path)) {
                return $existing;
            }
        }

        $verificationCode = Str::upper(Str::random(12));
        $verifyUrl = route('crm.document.verify', $verificationCode);

        $viewData = $this->buildViewData($donation, $verificationCode, $verifyUrl, $template);
        $html = view($template->blade_view, $viewData)->render();

        $pdfBinary = $this->renderPdf($html, $template);
        $relativePath = 'crm/documents/' . $donation->id . '/' . $type . '-' . $verificationCode . '.pdf';

        Storage::disk('public')->put($relativePath, $pdfBinary);

        return DonationDocument::query()->create([
            'donation_id' => $donation->id,
            'document_template_id' => $template->id,
            'type' => $type,
            'verification_code' => $verificationCode,
            'pdf_path' => $relativePath,
            'generated_at' => now(),
            'meta' => [
                'donation_number' => $donation->donation_number,
                'donor_name' => $donation->donor?->full_name,
                'amount' => (string) $donation->amount,
                'currency' => $donation->currency,
            ],
        ]);
    }

    public function generateAll(Donation $donation, bool $regenerate = false): array
    {
        $documents = [];

        foreach (array_keys(DocumentTemplate::TYPES) as $type) {
            try {
                $documents[$type] = $this->generate($donation, $type, $regenerate);
            } catch (\Throwable) {
                // Şablon yoksa atla
            }
        }

        return $documents;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildViewData(Donation $donation, string $verificationCode, string $verifyUrl, DocumentTemplate $template): array
    {
        $settings = Setting::current();
        $donor = $donation->donor;

        $logoPath = $settings->logo ? public_path('storage/' . $settings->logo) : public_path('images/default-logo.svg');
        $logoDataUri = $this->fileToDataUri($logoPath);

        return [
            'settings' => $settings,
            'donation' => $donation,
            'donor' => $donor,
            'template' => $template,
            'verificationCode' => $verificationCode,
            'verifyUrl' => $verifyUrl,
            'qrDataUri' => $this->qrDataUri($verifyUrl),
            'logoDataUri' => $logoDataUri,
            'backgroundDataUri' => $template->background_image
                ? $this->fileToDataUri(public_path('storage/' . $template->background_image))
                : null,
            'placeholders' => [
                'ad' => $donor?->first_name ?? '',
                'soyad' => $donor?->last_name ?? '',
                'ad_soyad' => $donor?->full_name ?? '',
                'telefon' => $donor?->phone ?? '',
                'bagis_no' => $donation->donation_number,
                'makbuz_no' => $donation->receipt_number ?? $donation->donation_number,
                'bagis_turu' => $donation->donationType?->name ?? '',
                'bagis_tutari' => number_format((float) $donation->amount, 2, ',', '.'),
                'para_birimi' => $donation->currency,
                'tarih' => $donation->donated_at?->format('d.m.Y') ?? now()->format('d.m.Y'),
                'aciklama' => $donation->description ?? '',
            ],
        ];
    }

    private function qrDataUri(string $url): string
    {
        $writer = new PngWriter();
        $qrCode = new QrCode(data: $url, size: 180, margin: 6);
        $result = $writer->write($qrCode);

        return 'data:image/png;base64,' . base64_encode($result->getString());
    }

    private function fileToDataUri(?string $path): ?string
    {
        if (! $path || ! is_file($path)) {
            return null;
        }

        $mime = mime_content_type($path) ?: 'image/png';

        return 'data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($path));
    }

    private function renderPdf(string $html, DocumentTemplate $template): string
    {
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);

        $orientation = $template->type === DocumentTemplate::TYPE_THANKS_POSTER ? 'landscape' : 'portrait';
        $dompdf->setPaper('A4', $orientation);
        $dompdf->render();

        return (string) $dompdf->output();
    }
}
