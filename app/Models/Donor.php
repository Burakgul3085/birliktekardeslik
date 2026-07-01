<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Donor extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'city',
        'country',
        'address',
        'notes',
    ];

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function crmNotes(): HasMany
    {
        return $this->hasMany(CrmNote::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getTotalDonationAmountAttribute(): float
    {
        return (float) $this->donations()->sum('amount');
    }

    public function getTotalDonationCountAttribute(): int
    {
        return (int) $this->donations()->count();
    }

    public function getFirstDonationAtAttribute(): ?string
    {
        $date = $this->donations()->min('donated_at');

        return $date ? (string) $date : null;
    }

    public function getLastDonationAtAttribute(): ?string
    {
        $date = $this->donations()->max('donated_at');

        return $date ? (string) $date : null;
    }

    public function getSupportedProjectsSummaryAttribute(): string
    {
        $lines = $this->donations()
            ->whereNotNull('project_id')
            ->with('project')
            ->get()
            ->groupBy('project_id')
            ->map(function ($donations): string {
                $title = $donations->first()->project?->title ?? 'Bilinmeyen proje';
                $total = number_format((float) $donations->sum('amount'), 2, ',', '.');

                return $title . ' — ' . $total . ' TRY (' . $donations->count() . ' bağış)';
            })
            ->values();

        return $lines->isEmpty() ? 'Henüz proje bağışı yok' : $lines->implode("\n");
    }
}
