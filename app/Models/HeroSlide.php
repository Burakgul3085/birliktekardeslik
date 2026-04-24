<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class HeroSlide extends Model
{
    protected $fillable = [
        'headline',
        'kicker',
        'accent_text',
        'subtext',
        'image_path',
        'background_image_path',
        'thumbnail_image_path',
        'button_text',
        'button_url',
        'show_site_logo',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_site_logo' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
