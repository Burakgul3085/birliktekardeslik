<?php

namespace App\Support;

use App\Models\Setting;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Support\Facades\Storage;

class DonationQrService
{
    private const QR_PATH = 'qr/donations.svg';
    private const URL_META_PATH = 'qr/donations-url.txt';

    public function generate(bool $force = false): string
    {
        $targetUrl = $this->resolveDonationUrl();
        $disk = Storage::disk('public');

        $storedUrl = $disk->exists(self::URL_META_PATH)
            ? trim((string) $disk->get(self::URL_META_PATH))
            : null;

        $shouldGenerate = $force
            || ! $disk->exists(self::QR_PATH)
            || $storedUrl !== $targetUrl;

        if ($shouldGenerate) {
            $writer = new SvgWriter();
            $qrCode = new QrCode(
                data: $targetUrl,
                size: 220,
                margin: 8,
            );

            $result = $writer->write($qrCode);
            $disk->put(self::QR_PATH, $result->getString());
            $disk->put(self::URL_META_PATH, $targetUrl);
        }

        return self::QR_PATH;
    }

    private function resolveDonationUrl(): string
    {
        $settings = Setting::current();
        $manualUrl = trim((string) ($settings->donation_page_url ?? ''));

        if ($manualUrl !== '') {
            return $manualUrl;
        }

        return route('donations');
    }
}
