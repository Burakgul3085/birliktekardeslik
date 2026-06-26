<?php

namespace App\Support\Crm\TemplateEngine;

class TemplateFieldNormalizer
{
    /**
     * @param  array<string, mixed>  $field
     * @return array<string, mixed>
     */
    public static function normalize(array $field): array
    {
        $type = (string) ($field['type'] ?? $field['field_type'] ?? 'text');
        $key = (string) ($field['key'] ?? $field['field_key'] ?? 'ad_soyad');

        $base = [
            'id' => (string) ($field['id'] ?? 'field_' . $key),
            'key' => $key,
            'label' => (string) ($field['label'] ?? (TemplateFieldDefaults::FIELD_LABELS[$key] ?? $key)),
            'type' => $type,
            'x' => max(0, (int) ($field['x'] ?? 0)),
            'y' => max(0, (int) ($field['y'] ?? 0)),
            'width' => max(1, (int) ($field['width'] ?? 100)),
            'height' => max(1, (int) ($field['height'] ?? 50)),
            'align' => (string) ($field['align'] ?? 'center'),
            'vertical_align' => (string) ($field['vertical_align'] ?? $field['valign'] ?? 'middle'),
        ];

        if ($type === 'qr') {
            return $base;
        }

        $isSingleLine = in_array($key, ['ad_soyad', 'bagis_turu', 'tarih', 'bagis_no'], true);

        return array_merge($base, [
            'font_family' => (string) ($field['font_family'] ?? 'DejaVuSans'),
            'font_size' => max(8, (int) ($field['font_size'] ?? 32)),
            'color' => (string) ($field['color'] ?? '#1B3A6B'),
            'line_height' => (float) ($field['line_height'] ?? 1.4),
            'max_lines' => max(1, (int) ($field['max_lines'] ?? ($isSingleLine ? 1 : 5))),
            'auto_resize' => array_key_exists('auto_resize', $field)
                ? (bool) $field['auto_resize']
                : (bool) ($field['auto_shrink'] ?? true),
            'word_wrap' => array_key_exists('word_wrap', $field)
                ? (bool) $field['word_wrap']
                : ! $isSingleLine,
        ]);
    }

    /**
     * @param  array<int, array<string, mixed>>  $fields
     * @return array<int, array<string, mixed>>
     */
    public static function normalizeAll(array $fields): array
    {
        return array_values(array_map([self::class, 'normalize'], $fields));
    }
}
