<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PosterDocument extends Model
{
    protected $fillable = [
        'donation_id',
        'poster_template_id',
        'type',
        'image_path',
        'pdf_path',
        'layout_snapshot',
        'generated_at',
    ];

    protected $casts = [
        'layout_snapshot' => 'array',
        'generated_at' => 'datetime',
    ];

    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(PosterTemplate::class, 'poster_template_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return PosterTemplate::TYPES[$this->type] ?? $this->type;
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path
            ? Storage::disk('public')->url($this->image_path)
            : null;
    }

    protected static function booted(): void
    {
        static::deleting(function (PosterDocument $document): void {
            foreach ([$document->image_path, $document->pdf_path] as $path) {
                if ($path && Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
        });
    }
}
