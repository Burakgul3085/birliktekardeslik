<?php

namespace App\Support\Crm;

use App\Models\DonationDocument;
use App\Models\Setting;

class ReceiptWhatsAppLinkBuilder
{
    public function build(DonationDocument $document): ?string
    {
        $document->loadMissing('donation.donor');

        $donation = $document->donation;
        $donor = $donation?->donor;

        if (! $donor) {
            return null;
        }

        $phone = PhoneNumberNormalizer::toWhatsApp($donor->phone);

        if ($phone === null) {
            return null;
        }

        $orgName = Setting::current()->site_title ?? 'Birlikte Kardeşlik Derneği';
        $amount = number_format((float) $donation->amount, 2, ',', '.');
        $date = $donation->donated_at?->format('d.m.Y') ?? '-';

        $lines = [
            "Sayın {$donor->full_name},",
            '',
            "{$orgName} bağış makbuzunuz:",
            '',
            "Bağış No: {$donation->donation_number}",
        ];

        if (filled($donation->receipt_number)) {
            $lines[] = "Makbuz No: {$donation->receipt_number}";
        }

        $lines[] = "Tutar: {$amount} {$donation->currency}";
        $lines[] = "Tarih: {$date}";

        $description = trim((string) $donation->description);

        if ($description !== '') {
            $lines[] = '';
            $lines[] = "Açıklama: {$description}";
        }

        $lines[] = '';
        $lines[] = "Makbuzu görüntüleyin: {$document->verification_url}";
        $lines[] = "PDF indir: {$document->public_download_url}";
        $lines[] = '';
        $lines[] = 'Teşekkür ederiz.';

        return 'https://wa.me/' . $phone . '?text=' . rawurlencode(implode("\n", $lines));
    }

    public function donorHasPhone(DonationDocument $document): bool
    {
        $document->loadMissing('donation.donor');

        return PhoneNumberNormalizer::toWhatsApp($document->donation?->donor?->phone) !== null;
    }
}
