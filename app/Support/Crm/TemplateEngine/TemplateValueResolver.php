<?php

namespace App\Support\Crm\TemplateEngine;

use App\Models\Donation;
use App\Support\Crm\PosterContentBuilder;

class TemplateValueResolver
{
    /**
     * @return array<string, string>
     */
    public static function forDonation(Donation $donation, string $templateType, ?string $verifyUrl = null): array
    {
        $donation->loadMissing(['donor', 'donationType']);

        $base = [
            'tesekkur_paragrafi' => PosterContentBuilder::thankYouBody($donation),
            'bagis_aciklamasi' => mb_strtoupper(trim($donation->description ?? ''), 'UTF-8'),
            'bagis_turu' => mb_strtoupper($donation->donationType?->name ?? '', 'UTF-8'),
            'tarih' => $donation->donated_at?->format('d.m.Y') ?? now()->format('d.m.Y'),
            'bagis_no' => $donation->donation_number,
            'qr_code' => $verifyUrl ?? '',
        ];

        $base['ad_soyad'] = $templateType === \App\Models\DocumentTemplate::TYPE_DONATION_POSTER
            ? PosterContentBuilder::displayName($donation, uppercase: true)
            : PosterContentBuilder::salutation($donation);

        return $base;
    }
}
