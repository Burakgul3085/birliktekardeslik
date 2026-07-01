<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmNote extends Model
{
    public const SCOPES = [
        'general' => 'Genel',
        'donor' => 'Bağışçı',
        'donation' => 'Bağış',
    ];

    public const CATEGORIES = [
        'meeting' => 'Toplantı',
        'reminder' => 'Hatırlatma',
        'operation' => 'Operasyon',
        'communication' => 'İletişim',
        'finance' => 'Mali',
        'other' => 'Diğer',
    ];

    public const VISIBILITIES = [
        'team' => 'Ekip notu',
        'private' => 'Kişisel not',
    ];

    protected $fillable = [
        'crm_user_id',
        'title',
        'body',
        'scope',
        'donor_id',
        'donation_id',
        'category',
        'is_pinned',
        'visibility',
    ];

    protected function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(CrmUser::class, 'crm_user_id');
    }

    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }

    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class);
    }

    /**
     * @param  Builder<CrmNote>  $query
     * @return Builder<CrmNote>
     */
    public function scopeVisibleTo(Builder $query, ?CrmUser $user): Builder
    {
        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function (Builder $inner) use ($user): void {
            $inner->where('visibility', 'team')
                ->orWhere(function (Builder $private) use ($user): void {
                    $private->where('visibility', 'private')
                        ->where('crm_user_id', $user->id);
                });
        });
    }

    public function getScopeLabelAttribute(): string
    {
        return self::SCOPES[$this->scope] ?? $this->scope;
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    public function getVisibilityLabelAttribute(): string
    {
        return self::VISIBILITIES[$this->visibility] ?? $this->visibility;
    }

    public function getDisplayTitleAttribute(): string
    {
        if (filled($this->title)) {
            return $this->title;
        }

        $preview = trim(preg_replace('/\s+/', ' ', strip_tags($this->body)) ?? '');

        if ($preview === '') {
            return 'Not';
        }

        return mb_strlen($preview) > 60 ? mb_substr($preview, 0, 57) . '...' : $preview;
    }

    public function getRelatedLabelAttribute(): ?string
    {
        return match ($this->scope) {
            'donor' => $this->donor?->full_name,
            'donation' => $this->donation
                ? trim(($this->donation->donation_number ?? '') . ' — ' . ($this->donation->donor?->full_name ?? ''))
                : null,
            default => null,
        };
    }
}
