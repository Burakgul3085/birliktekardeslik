<?php

namespace App\Support\Crm\TemplateEngine;

use App\Models\DocumentTemplate;

class TemplateFieldCatalog
{
    public const SIGNATURE_NAME = 'Oğuzhan Narin';

    public const SIGNATURE_TITLE = 'Birlikte Kardeşlik Derneği Başkanı';

    public const LABELS = [
        'ad_soyad' => 'Ad Soyad',
        'tesekkur_metni' => 'Teşekkür Metni',
        'tesekkur_paragrafi' => 'Teşekkür Metni (eski)',
        'imza_ad_soyad' => 'İmza Ad Soyad',
        'imza_unvan' => 'İmza Ünvan',
        'bagis_aciklamasi' => 'Bağış Açıklaması',
        'bagis_turu' => 'Bağış Türü',
        'tarih' => 'Tarih',
        'bagis_no' => 'Bağış No',
        'qr_code' => 'QR Kod',
    ];

    /**
     * @return array<string, string>
     */
    public static function labelsForType(string $type): array
    {
        $keys = self::keysForType($type);

        return array_intersect_key(self::LABELS, array_flip($keys));
    }

    /**
     * @return array<int, string>
     */
    public static function keysForType(string $type): array
    {
        return match ($type) {
            DocumentTemplate::TYPE_THANKS_POSTER => [
                'ad_soyad',
                'tesekkur_metni',
                'imza_ad_soyad',
                'imza_unvan',
                'qr_code',
            ],
            DocumentTemplate::TYPE_DONATION_POSTER => [
                'ad_soyad',
                'bagis_aciklamasi',
                'bagis_turu',
                'tarih',
                'qr_code',
            ],
            default => array_keys(self::LABELS),
        };
    }

    public static function isSingleLine(string $key): bool
    {
        return in_array($key, [
            'ad_soyad',
            'imza_ad_soyad',
            'imza_unvan',
            'bagis_turu',
            'tarih',
            'bagis_no',
        ], true);
    }
}
