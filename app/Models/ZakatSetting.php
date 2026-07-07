<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZakatSetting extends Model
{
    protected $fillable = [
        'nisap_grams',
        'nisap_karat',
        'rate',
        'intro_i18n',
        'legal_text_i18n',
        'faq_i18n',
        'is_active',
    ];

    protected $casts = [
        'nisap_grams' => 'decimal:2',
        'rate' => 'decimal:4',
        'intro_i18n' => 'array',
        'legal_text_i18n' => 'array',
        'faq_i18n' => 'array',
        'is_active' => 'boolean',
    ];

    public static function current(): self
    {
        return static::query()->where('is_active', true)->latest('id')->first() ?? new self([
            'nisap_grams' => config('zakat.nisap_grams', 80),
            'nisap_karat' => config('zakat.nisap_karat', 24),
            'rate' => config('zakat.rate', 0.025),
            'is_active' => true,
        ]);
    }

    public function localized(string $field): ?string
    {
        $locale = app()->getLocale();
        $i18nField = $field . '_i18n';
        $translations = is_array($this->{$i18nField} ?? null) ? $this->{$i18nField} : [];

        $value = $translations[$locale] ?? $translations['tr'] ?? null;

        return filled($value) ? (string) $value : null;
    }

    public function localizedFaq(): array
    {
        $locale = app()->getLocale();
        $faq = is_array($this->faq_i18n) ? $this->faq_i18n : [];
        $items = $faq[$locale] ?? $faq['tr'] ?? [];

        if (! is_array($items) || $items === []) {
            $fallback = __('app.zakat.faq_items');

            return is_array($fallback) ? $fallback : [];
        }

        return collect($items)
            ->filter(fn ($item): bool => is_array($item) && filled($item['question'] ?? null))
            ->map(fn (array $item): array => [
                'question' => (string) ($item['question'] ?? ''),
                'answer' => (string) ($item['answer'] ?? ''),
            ])
            ->values()
            ->all();
    }
}
