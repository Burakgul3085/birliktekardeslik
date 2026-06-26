<?php

namespace App\Support\Crm;

use App\Models\Donation;
use Carbon\Carbon;

class DonationStats
{
    public static function sumBetween(Carbon $from, Carbon $to): float
    {
        return (float) Donation::query()
            ->whereBetween('donated_at', [$from, $to])
            ->sum('amount');
    }

    public static function today(): float
    {
        return self::sumBetween(now()->startOfDay(), now()->endOfDay());
    }

    public static function thisWeek(): float
    {
        return self::sumBetween(now()->startOfWeek(), now()->endOfWeek());
    }

    public static function thisMonth(): float
    {
        return self::sumBetween(now()->startOfMonth(), now()->endOfMonth());
    }

    public static function thisYear(): float
    {
        return self::sumBetween(now()->startOfYear(), now()->endOfYear());
    }

    public static function total(): float
    {
        return (float) Donation::query()->sum('amount');
    }

    public static function formatMoney(float $amount): string
    {
        return number_format($amount, 2, ',', '.') . ' TRY';
    }
}
