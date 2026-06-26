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

    public const FIELDS_VERSION = 2;

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

        return TemplateFieldNormalizer::normalizeAll(
            array_map(fn (array $field): array => self::applyCanvasSize($field, $width, $height), $base),
        );
    }

    /**
     * @param  array<string, mixed>  $field
     * @return array<string, mixed>
     */
    public static function applyCanvasSize(array $field, int $width, int $height): array
    {
        if (isset($field['x_ratio'])) {
            $field['x'] = (int) round((float) $field['x_ratio'] * $width);
            $field['y'] = (int) round((float) $field['y_ratio'] * $height);
            $field['width'] = (int) round((float) $field['width_ratio'] * $width);
            $field['height'] = (int) round((float) $field['height_ratio'] * $height);
        }

        if (($field['type'] ?? 'text') === 'text' && isset($field['font_size'])) {
            $scale = min($width / 2480, $height / 3508);
            $field['font_size'] = max(10, (int) round((int) $field['font_size'] * $scale));
        }

        return $field;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private static function thanksPosterFields(): array
    {
        return [
            self::textField('ad_soyad', 'Ad Soyad', 0.125, 0.358, 0.75, 0.038, [
                'font_family' => 'DejaVuSerif-Bold',
                'font_size' => 48,
                'color' => '#1B3A6B',
                'max_lines' => 1,
                'word_wrap' => false,
                'auto_shrink' => true,
            ]),
            self::textField('tesekkur_paragrafi', 'Teşekkür Paragrafı', 0.135, 0.405, 0.73, 0.205, [
                'font_family' => 'DejaVuSerif',
                'font_size' => 32,
                'color' => '#1B3A6B',
                'line_height' => 1.65,
                'max_lines' => 14,
                'word_wrap' => true,
                'auto_shrink' => true,
            ]),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private static function donationPosterFields(): array
    {
        return [
            self::textField('ad_soyad', 'Ad Soyad', 0.125, 0.278, 0.75, 0.042, [
                'font_family' => 'DejaVuSerif-Bold',
                'font_size' => 52,
                'color' => '#111827',
                'max_lines' => 1,
                'word_wrap' => false,
                'auto_shrink' => true,
            ]),
            self::textField('bagis_aciklamasi', 'Bağış Açıklaması', 0.133, 0.335, 0.734, 0.175, [
                'font_family' => 'DejaVuSans-Bold',
                'font_size' => 28,
                'color' => '#B91C1C',
                'line_height' => 1.45,
                'max_lines' => 10,
                'word_wrap' => true,
                'auto_shrink' => true,
            ]),
            self::textField('bagis_turu', 'Bağış Türü', 0.125, 0.528, 0.75, 0.032, [
                'font_family' => 'DejaVuSans-Bold',
                'font_size' => 40,
                'color' => '#B91C1C',
                'max_lines' => 1,
                'word_wrap' => false,
                'auto_shrink' => true,
            ]),
            self::textField('tarih', 'Tarih', 0.125, 0.568, 0.75, 0.028, [
                'font_family' => 'DejaVuSans-Bold',
                'font_size' => 34,
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
        float $xRatio,
        float $yRatio,
        float $widthRatio,
        float $heightRatio,
        array $overrides = [],
    ): array {
        return array_merge([
            'id' => 'field_' . $key,
            'key' => $key,
            'label' => $label,
            'type' => 'text',
            'x_ratio' => $xRatio,
            'y_ratio' => $yRatio,
            'width_ratio' => $widthRatio,
            'height_ratio' => $heightRatio,
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
}
