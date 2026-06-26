<?php

namespace App\Http\Controllers;

use App\Models\DonationDocument;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CrmDocumentController extends Controller
{
    public function verify(string $code): View
    {
        $document = DonationDocument::query()
            ->with(['donation.donor', 'donation.donationType', 'donation.paymentMethod'])
            ->where('verification_code', $code)
            ->firstOrFail();

        return view('crm.documents.verify', [
            'document' => $document,
            'donation' => $document->donation,
        ]);
    }

    public function download(DonationDocument $document): StreamedResponse
    {
        abort_unless(auth('crm')->check(), 403);

        abort_unless(Storage::disk('public')->exists($document->pdf_path), 404);

        $filename = $document->type . '-' . $document->verification_code . '.pdf';

        return Storage::disk('public')->download($document->pdf_path, $filename);
    }
}
