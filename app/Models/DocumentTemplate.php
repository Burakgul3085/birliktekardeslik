<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentTemplate extends Model
{
    public const TYPE_RECEIPT = 'receipt';

    public const TYPES = [
        self::TYPE_RECEIPT => 'Makbuz',
    ];

    protected $fillable = [
        'name',
        'type',
        'blade_view',
        'settings',
        'is_default',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function documents(): HasMany
    {
        return $this->hasMany(DonationDocument::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function resolvedOrientation(): string
    {
        return $this->settings['orientation'] ?? 'portrait';
    }
}
