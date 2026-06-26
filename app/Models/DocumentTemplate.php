<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentTemplate extends Model
{
    public const TYPE_RECEIPT = 'receipt';

    public const TYPE_DONATION_POSTER = 'donation_poster';

    public const TYPE_THANKS_POSTER = 'thanks_poster';

    /** @deprecated Artık üretilmiyor */
    public const TYPE_THANKS_LETTER = 'thanks_letter';

    /** @deprecated Artık üretilmiyor */
    public const TYPE_CERTIFICATE = 'certificate';

    public const ACTIVE_TYPES = [
        self::TYPE_RECEIPT => 'Makbuz',
        self::TYPE_DONATION_POSTER => 'Bağış Afişi',
        self::TYPE_THANKS_POSTER => 'Teşekkür Afişi',
    ];

    public const TYPES = [
        ...self::ACTIVE_TYPES,
        self::TYPE_THANKS_LETTER => 'Teşekkür Belgesi',
        self::TYPE_CERTIFICATE => 'Sertifika',
    ];

    public const GENERATABLE_TYPES = [
        self::TYPE_RECEIPT,
        self::TYPE_DONATION_POSTER,
        self::TYPE_THANKS_POSTER,
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

    public function requiresBackground(): bool
    {
        return in_array($this->type, [self::TYPE_DONATION_POSTER, self::TYPE_THANKS_POSTER], true);
    }

    public function resolvedOrientation(): string
    {
        return match ($this->type) {
            self::TYPE_DONATION_POSTER, self::TYPE_THANKS_POSTER => 'portrait',
            default => $this->settings['orientation'] ?? 'portrait',
        };
    }
}
