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
}
