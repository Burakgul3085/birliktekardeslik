<?php

namespace App\Support\Crm\TemplateEngine;

use App\Models\DocumentTemplate;

class TemplateFieldDefaults
{
    public const FIELD_LABELS = [
        'ad_soyad' => 'Ad Soyad',
        'tesekkur_paragrafi' => 'Teşekkür Paragrafı',
        'bagis_aciklamasi' => 'Bağış Açıklaması',
        'bagis_turu' => 'Bağış Türü',
        'tarih' => 'Tarih',
        'bagis_no' => 'Bağış No',
        'qr_code' => 'QR Kod',
    ];

    private const REFERENCE_WIDTH = 2480;

    private const REFERENCE_HEIGHT = 3508;

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function forType(string $type, int $width, int $height): array
    {
        $base = match ($type) {
            DocumentTemplate::TYPE_THANKS_POSTER => self::thanksPosterFields(),
            DocumentTemplate::TYPE_DONATION_POSTER => self::donationPosterFields(),
            default => [],
        };

        return self::scaleFields($base, $width, $height);
    }

    /**
     * @param  array<int, array<string, mixed>>  $fields
     * @return array<int, array<string, mixed>>
     */
    public static function scaleFields(array $fields, int $width, int $height): array
    {
        $scaleX = $width / self::REFERENCE_WIDTH;
        $scaleY = $height / self::REFERENCE_HEIGHT;
        $scale = min($scaleX, $scaleY);

        return array_map(function (array $field) use ($scaleX, $scaleY, $scale): array {
            $field['x'] = (int) round($field['x'] * $scaleX);
            $field['y'] = (int) round($field['y'] * $scaleY);
            $field['width'] = (int) round($field['width'] * $scaleX);
            $field['height'] = (int) round($field['height'] * $scaleY);
            $field['font_size'] = max(12, (int) round($field['font_size'] * $scale));

            return $field;
        }, $fields);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private static function thanksPosterFields(): array
    {
        return [
            self::textField('ad_soyad', 'Ad Soyad', 310, 1165, 1860, 105, [
                'font_family' => 'DejaVuSerif-Bold',
                'font_size' => 52,
                'color' => '#1B3A6B',
                'max_lines' => 1,
                'word_wrap' => false,
                'auto_shrink' => true,
            ]),
            self::textField('tesekkur_paragrafi', 'Teşekkür Paragrafı', 350, 1385, 1780, 700, [
                'font_family' => 'DejaVuSerif',
                'font_size' => 34,
                'color' => '#1B3A6B',
                'line_height' => 1.65,
                'max_lines' => 12,
                'word_wrap' => true,
                'auto_shrink' => true,
            ]),
            self::qrField('qr_code', 'QR Kod', 2100, 3000, 220, 220),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private static function donationPosterFields(): array
    {
        return [
            self::textField('ad_soyad', 'Ad Soyad', 310, 920, 1860, 120, [
                'font_family' => 'DejaVuSerif-Bold',
                'font_size' => 56,
                'color' => '#111827',
                'max_lines' => 1,
                'word_wrap' => false,
                'auto_shrink' => true,
            ]),
            self::textField('bagis_aciklamasi', 'Bağış Açıklaması', 330, 1150, 1820, 520, [
                'font_family' => 'DejaVuSans-Bold',
                'font_size' => 30,
                'color' => '#B91C1C',
                'line_height' => 1.45,
                'max_lines' => 8,
                'word_wrap' => true,
                'auto_shrink' => true,
            ]),
            self::textField('bagis_turu', 'Bağış Türü', 310, 1780, 1860, 90, [
                'font_family' => 'DejaVuSans-Bold',
                'font_size' => 44,
                'color' => '#B91C1C',
                'max_lines' => 1,
                'word_wrap' => false,
                'auto_shrink' => true,
            ]),
            self::textField('tarih', 'Tarih', 310, 1940, 1860, 75, [
                'font_family' => 'DejaVuSans-Bold',
                'font_size' => 38,
                'color' => '#B91C1C',
                'max_lines' => 1,
                'word_wrap' => false,
                'auto_shrink' => true,
            ]),
        ];
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private static function textField(
        string $key,
        string $label,
        int $x,
        int $y,
        int $width,
        int $height,
        array $overrides = [],
    ): array {
        return array_merge([
            'id' => 'field_' . $key,
            'key' => $key,
            'label' => $label,
            'type' => 'text',
            'x' => $x,
            'y' => $y,
            'width' => $width,
            'height' => $height,
            'align' => 'center',
            'valign' => 'middle',
            'font_family' => 'DejaVuSans',
            'font_size' => 32,
            'color' => '#1B3A6B',
            'line_height' => 1.4,
            'letter_spacing' => 0,
            'max_lines' => 5,
            'auto_shrink' => true,
            'word_wrap' => true,
        ], $overrides);
    }

    /**
     * @return array<string, mixed>
     */
    private static function qrField(string $key, string $label, int $x, int $y, int $width, int $height): array
    {
        return [
            'id' => 'field_' . $key,
            'key' => $key,
            'label' => $label,
            'type' => 'qr',
            'x' => $x,
            'y' => $y,
            'width' => $width,
            'height' => $height,
            'align' => 'center',
            'valign' => 'middle',
        ];
    }
}
