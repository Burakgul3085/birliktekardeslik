<?php

namespace App\Support\Crm;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class DonationDateFilter
{
    public static function presets(): array
    {
        return [
            'today' => 'Bugün',
            'yesterday' => 'Dün',
            'this_week' => 'Bu hafta',
            'last_week' => 'Geçen hafta',
            'this_month' => 'Bu ay',
            'last_month' => 'Geçen ay',
            'this_year' => 'Bu yıl',
            'last_year' => 'Geçen yıl',
            'last_7_days' => 'Son 7 gün',
            'last_30_days' => 'Son 30 gün',
            'last_90_days' => 'Son 90 gün',
        ];
    }

    public static function apply(Builder $query, ?string $preset, ?string $from = null, ?string $until = null, string $column = 'donated_at'): Builder
    {
        if ($preset) {
            $now = Carbon::now();

            return match ($preset) {
                'today' => $query->whereDate($column, $now->toDateString()),
                'yesterday' => $query->whereDate($column, $now->copy()->subDay()->toDateString()),
                'this_week' => $query->whereBetween($column, [
                    $now->copy()->startOfWeek(),
                    $now->copy()->endOfWeek(),
                ]),
                'last_week' => $query->whereBetween($column, [
                    $now->copy()->subWeek()->startOfWeek(),
                    $now->copy()->subWeek()->endOfWeek(),
                ]),
                'this_month' => $query->whereBetween($column, [
                    $now->copy()->startOfMonth(),
                    $now->copy()->endOfMonth(),
                ]),
                'last_month' => $query->whereBetween($column, [
                    $now->copy()->subMonth()->startOfMonth(),
                    $now->copy()->subMonth()->endOfMonth(),
                ]),
                'this_year' => $query->whereBetween($column, [
                    $now->copy()->startOfYear(),
                    $now->copy()->endOfYear(),
                ]),
                'last_year' => $query->whereBetween($column, [
                    $now->copy()->subYear()->startOfYear(),
                    $now->copy()->subYear()->endOfYear(),
                ]),
                'last_7_days' => $query->where($column, '>=', $now->copy()->subDays(7)->startOfDay()),
                'last_30_days' => $query->where($column, '>=', $now->copy()->subDays(30)->startOfDay()),
                'last_90_days' => $query->where($column, '>=', $now->copy()->subDays(90)->startOfDay()),
                default => $query,
            };
        }

        return $query
            ->when($from, fn (Builder $q, $date) => $q->whereDate($column, '>=', $date))
            ->when($until, fn (Builder $q, $date) => $q->whereDate($column, '<=', $date));
    }
}
