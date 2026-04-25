<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = ['title', 'content', 'cover_image', 'gallery_images', 'gallery_videos', 'summary', 'published_at', 'is_active'];

    protected $casts = [
        'gallery_images' => 'array',
        'gallery_videos' => 'array',
        'published_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
