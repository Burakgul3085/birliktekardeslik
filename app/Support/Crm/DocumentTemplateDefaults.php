<?php

namespace App\Support\Crm;

use App\Models\DocumentTemplate;

class DocumentTemplateDefaults
{
    public const PLACEHOLDER_LABELS = [
        'ad' => 'Ad',
        'soyad' => 'Soyad',
        'ad_soyad' => 'Ad Soyad',
        'telefon' => 'Telefon',
        'bagis_no' => 'Bağış No',
        'makbuz_no' => 'Makbuz No',
        'bagis_turu' => 'Bağış Türü',
        'bagis_tutari' => 'Bağış Tutarı',
        'para_birimi' => 'Para Birimi',
        'tarih' => 'Tarih',
        'aciklama' => 'Açıklama',
    ];

    public static function settingsForType(string $type): array
    {
        return match ($type) {
            DocumentTemplate::TYPE_THANKS_POSTER => [
                'orientation' => 'landscape',
                'fields' => [
                    ['key' => 'ad_soyad', 'x' => 50, 'y' => 38, 'font_size' => 32, 'color' => '#ffffff', 'align' => 'center', 'font_weight' => 'bold'],
                    ['key' => 'bagis_tutari', 'x' => 50, 'y' => 52, 'font_size' => 24, 'color' => '#ffffff', 'align' => 'center', 'font_weight' => 'bold'],
                    ['key' => 'para_birimi', 'x' => 62, 'y' => 52, 'font_size' => 20, 'color' => '#ffffff', 'align' => 'left', 'font_weight' => 'normal'],
                    ['key' => 'bagis_turu', 'x' => 50, 'y' => 62, 'font_size' => 16, 'color' => '#f0fdfa', 'align' => 'center', 'font_weight' => 'normal'],
                    ['key' => 'tarih', 'x' => 50, 'y' => 72, 'font_size' => 14, 'color' => '#e2e8f0', 'align' => 'center', 'font_weight' => 'normal'],
                ],
                'qr' => ['enabled' => true, 'x' => 92, 'y' => 88, 'size' => 80],
            ],
            DocumentTemplate::TYPE_THANKS_LETTER => [
                'orientation' => 'portrait',
                'fields' => [
                    ['key' => 'ad_soyad', 'x' => 50, 'y' => 35, 'font_size' => 22, 'color' => '#0f766e', 'align' => 'center', 'font_weight' => 'bold'],
                    ['key' => 'bagis_tutari', 'x' => 50, 'y' => 48, 'font_size' => 18, 'color' => '#0f172a', 'align' => 'center', 'font_weight' => 'bold'],
                    ['key' => 'para_birimi', 'x' => 58, 'y' => 48, 'font_size' => 16, 'color' => '#0f172a', 'align' => 'left', 'font_weight' => 'normal'],
                    ['key' => 'bagis_turu', 'x' => 50, 'y' => 56, 'font_size' => 14, 'color' => '#334155', 'align' => 'center', 'font_weight' => 'normal'],
                    ['key' => 'tarih', 'x' => 50, 'y' => 64, 'font_size' => 13, 'color' => '#64748b', 'align' => 'center', 'font_weight' => 'normal'],
                    ['key' => 'aciklama', 'x' => 50, 'y' => 74, 'font_size' => 12, 'color' => '#475569', 'align' => 'center', 'font_weight' => 'normal'],
                ],
                'qr' => ['enabled' => true, 'x' => 15, 'y' => 90, 'size' => 70],
            ],
            DocumentTemplate::TYPE_CERTIFICATE => [
                'orientation' => 'portrait',
                'fields' => [
                    ['key' => 'ad_soyad', 'x' => 50, 'y' => 42, 'font_size' => 26, 'color' => '#0f766e', 'align' => 'center', 'font_weight' => 'bold'],
                    ['key' => 'bagis_tutari', 'x' => 50, 'y' => 55, 'font_size' => 20, 'color' => '#0f172a', 'align' => 'center', 'font_weight' => 'bold'],
                    ['key' => 'para_birimi', 'x' => 58, 'y' => 55, 'font_size' => 18, 'color' => '#0f172a', 'align' => 'left', 'font_weight' => 'normal'],
                    ['key' => 'tarih', 'x' => 50, 'y' => 65, 'font_size' => 14, 'color' => '#475569', 'align' => 'center', 'font_weight' => 'normal'],
                    ['key' => 'bagis_no', 'x' => 50, 'y' => 73, 'font_size' => 12, 'color' => '#64748b', 'align' => 'center', 'font_weight' => 'normal'],
                ],
                'qr' => ['enabled' => true, 'x' => 88, 'y' => 88, 'size' => 75],
            ],
            default => [
                'orientation' => 'portrait',
                'fields' => [
                    ['key' => 'ad_soyad', 'x' => 50, 'y' => 30, 'font_size' => 18, 'color' => '#0f172a', 'align' => 'center', 'font_weight' => 'bold'],
                    ['key' => 'bagis_tutari', 'x' => 50, 'y' => 42, 'font_size' => 16, 'color' => '#0f172a', 'align' => 'center', 'font_weight' => 'bold'],
                    ['key' => 'tarih', 'x' => 50, 'y' => 52, 'font_size' => 13, 'color' => '#475569', 'align' => 'center', 'font_weight' => 'normal'],
                    ['key' => 'bagis_no', 'x' => 50, 'y' => 60, 'font_size' => 12, 'color' => '#64748b', 'align' => 'center', 'font_weight' => 'normal'],
                ],
                'qr' => ['enabled' => true, 'x' => 88, 'y' => 88, 'size' => 70],
            ],
        };
    }

    public static function mergeSettings(?array $settings, string $type): array
    {
        $defaults = self::settingsForType($type);

        if (empty($settings)) {
            return $defaults;
        }

        return [
            'orientation' => $settings['orientation'] ?? $defaults['orientation'],
            'fields' => ! empty($settings['fields']) ? $settings['fields'] : $defaults['fields'],
            'qr' => array_merge($defaults['qr'], $settings['qr'] ?? []),
        ];
    }
}
