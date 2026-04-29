<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = [
        'title',
        'title_i18n',
        'content',
        'content_i18n',
        'cover_image',
        'gallery_images',
        'gallery_videos',
        'summary',
        'summary_i18n',
        'published_at',
        'is_active',
    ];

    protected $casts = [
        'title_i18n' => 'array',
        'summary_i18n' => 'array',
        'content_i18n' => 'array',
        'gallery_images' => 'array',
        'gallery_videos' => 'array',
        'published_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getLocalized(string $field, ?string $fallback = null): ?string
    {
        $locale = app()->getLocale();
        $i18nField = $field . '_i18n';
        $translations = is_array($this->{$i18nField} ?? null) ? $this->{$i18nField} : [];

        $value = $translations[$locale] ?? $translations['tr'] ?? $this->{$field} ?? $fallback;
        $value = is_string($value) ? trim($value) : $value;

        return filled($value) ? (string) $value : $fallback;
    }
}
