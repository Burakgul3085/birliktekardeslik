<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentTemplate extends Model
{
    public const TYPE_RECEIPT = 'receipt';

    public const TYPE_THANKS_LETTER = 'thanks_letter';

    public const TYPE_THANKS_POSTER = 'thanks_poster';

    public const TYPE_CERTIFICATE = 'certificate';

    public const TYPES = [
        self::TYPE_RECEIPT => 'Makbuz',
        self::TYPE_THANKS_LETTER => 'Teşekkür Belgesi',
        self::TYPE_THANKS_POSTER => 'Teşekkür Afişi',
        self::TYPE_CERTIFICATE => 'Sertifika',
    ];

    protected $fillable = [
        'name',
        'type',
        'blade_view',
        'background_image',
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

    public function usesOverlay(): bool
    {
        return filled($this->background_image);
    }

    public function resolvedSettings(): array
    {
        return \App\Support\Crm\DocumentTemplateDefaults::mergeSettings($this->settings, $this->type);
    }
}
