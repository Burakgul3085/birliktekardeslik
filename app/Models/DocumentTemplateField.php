<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentTemplateField extends Model
{
    protected $fillable = [
        'document_template_id',
        'field_key',
        'label',
        'field_type',
        'x',
        'y',
        'width',
        'height',
        'font_family',
        'font_size',
        'color',
        'align',
        'vertical_align',
        'max_lines',
        'auto_resize',
        'word_wrap',
        'line_height',
        'sort_order',
    ];

    protected $casts = [
        'auto_resize' => 'boolean',
        'word_wrap' => 'boolean',
        'line_height' => 'float',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(DocumentTemplate::class, 'document_template_id');
    }

    /**
     * @return array<string, mixed>
     */
    public function toRenderDefinition(): array
    {
        return [
            'id' => 'field_' . $this->field_key . '_' . $this->id,
            'key' => $this->field_key,
            'label' => $this->label,
            'type' => $this->field_type,
            'x' => $this->x,
            'y' => $this->y,
            'width' => $this->width,
            'height' => $this->height,
            'font_family' => $this->font_family,
            'font_size' => $this->font_size,
            'color' => $this->color,
            'align' => $this->align,
            'vertical_align' => $this->vertical_align,
            'max_lines' => $this->max_lines,
            'auto_resize' => $this->auto_resize,
            'word_wrap' => $this->word_wrap,
            'line_height' => $this->line_height,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromLegacyArray(int $templateId, array $data, int $sortOrder): array
    {
        return [
            'document_template_id' => $templateId,
            'field_key' => (string) ($data['key'] ?? 'ad_soyad'),
            'label' => (string) ($data['label'] ?? 'Alan'),
            'field_type' => (string) ($data['type'] ?? 'text'),
            'x' => max(0, (int) ($data['x'] ?? 0)),
            'y' => max(0, (int) ($data['y'] ?? 0)),
            'width' => max(1, (int) ($data['width'] ?? 100)),
            'height' => max(1, (int) ($data['height'] ?? 50)),
            'font_family' => (string) ($data['font_family'] ?? 'DejaVuSans'),
            'font_size' => max(8, (int) ($data['font_size'] ?? 32)),
            'color' => (string) ($data['color'] ?? '#1B3A6B'),
            'align' => (string) ($data['align'] ?? 'center'),
            'vertical_align' => (string) ($data['vertical_align'] ?? $data['valign'] ?? 'middle'),
            'max_lines' => max(1, (int) ($data['max_lines'] ?? 5)),
            'auto_resize' => array_key_exists('auto_resize', $data)
                ? (bool) $data['auto_resize']
                : (bool) ($data['auto_shrink'] ?? true),
            'word_wrap' => array_key_exists('word_wrap', $data)
                ? (bool) $data['word_wrap']
                : ((string) ($data['key'] ?? '') !== 'ad_soyad'),
            'line_height' => (float) ($data['line_height'] ?? 1.4),
            'sort_order' => $sortOrder,
        ];
    }
}
