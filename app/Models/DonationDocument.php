<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class DonationDocument extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_FINAL = 'final';

    protected $fillable = [
        'donation_id',
        'document_template_id',
        'type',
        'status',
        'verification_code',
        'pdf_path',
        'png_path',
        'generated_at',
        'meta',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'meta' => 'array',
    ];

    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(DocumentTemplate::class, 'document_template_id');
    }

    public function fieldOverrides(): HasMany
    {
        return $this->hasMany(DocumentFieldOverride::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return DocumentTemplate::TYPES[$this->type] ?? $this->type;
    }

    public function getVerificationUrlAttribute(): string
    {
        return route('crm.document.verify', $this->verification_code);
    }

    public function isPoster(): bool
    {
        return in_array($this->type, DocumentTemplate::POSTER_TYPES, true);
    }

    protected static function booted(): void
    {
        static::deleting(function (DonationDocument $document): void {
            foreach (['pdf_path', 'png_path'] as $pathKey) {
                $path = $document->{$pathKey};

                if ($path && Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
        });
    }
}
