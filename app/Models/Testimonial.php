<?php

namespace App\Models;

use App\Support\TestimonialDisplayName;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'name',
        'display_name',
        'city',
        'email',
        'rating',
        'comment',
        'is_anonymous',
        'is_volunteer',
        'is_donor',
        'status',
        'ip_address',
        'approved_at',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_anonymous' => 'boolean',
        'is_volunteer' => 'boolean',
        'is_donor' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function approve(): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_at' => now(),
        ]);
    }

    public function reject(): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'approved_at' => null,
        ]);
    }

    /**
     * @return array{count: int, average: float}
     */
    public static function approvedStats(): array
    {
        return [
            'count' => static::query()->approved()->count(),
            'average' => round((float) static::query()->approved()->avg('rating'), 1),
        ];
    }

    public static function makeDisplayName(string $name, bool $anonymous): string
    {
        return TestimonialDisplayName::make($name, $anonymous);
    }
}
