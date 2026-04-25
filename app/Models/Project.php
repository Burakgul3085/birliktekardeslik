<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'donation_amount',
        'donation_currency',
        'goal_amount',
        'collected_amount',
        'content',
        'detail_item_1_title',
        'detail_item_1_text',
        'detail_item_2_title',
        'detail_item_2_text',
        'detail_item_3_title',
        'detail_item_3_text',
        'cover_image',
        'gallery_images',
        'gallery_videos',
        'status',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'gallery_images' => 'array',
        'gallery_videos' => 'array',
        'donation_amount' => 'decimal:2',
        'goal_amount' => 'decimal:2',
        'collected_amount' => 'decimal:2',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
