<?php

namespace App\Support\Crm;

use App\Models\Project;
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
            'last_2_hours' => 'Son 2 saat',
            'last_6_hours' => 'Son 6 saat',
            'last_24_hours' => 'Son 24 saat',
            'last_7_days' => 'Son 7 gün',
            'last_30_days' => 'Son 30 gün',
            'last_90_days' => 'Son 90 gün',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function dashboardPeriodOptions(): array
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
            'last_2_hours' => 'Son 2 saat',
            'last_6_hours' => 'Son 6 saat',
            'last_24_hours' => 'Son 24 saat',
            'last_7_days' => 'Son 7 gün',
            'last_30_days' => 'Son 30 gün',
            'last_90_days' => 'Son 90 gün',
            'relative' => 'Son X süre (özel)',
            'custom_range' => 'Tarih aralığı',
            'all_time' => 'Tüm zamanlar',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function relativeUnitOptions(): array
    {
        return [
            'hours' => 'Saat',
            'days' => 'Gün',
            'weeks' => 'Hafta',
            'months' => 'Ay',
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public static function dashboardPeriodLabel(array $filters): string
    {
        $period = (string) ($filters['period'] ?? 'this_month');

        if ($period === 'relative') {
            $amount = max(1, (int) ($filters['relative_amount'] ?? 1));
            $unit = self::relativeUnitOptions()[(string) ($filters['relative_unit'] ?? 'days')] ?? 'Gün';

            return "Son {$amount} {$unit}";
        }

        if ($period === 'custom_range') {
            $from = filled($filters['from'] ?? null)
                ? Carbon::parse($filters['from'])->format('d.m.Y H:i')
                : '—';
            $until = filled($filters['until'] ?? null)
                ? Carbon::parse($filters['until'])->format('d.m.Y H:i')
                : now()->format('d.m.Y H:i');

            return "{$from} – {$until}";
        }

        $flat = self::dashboardPeriodOptions();

        return $flat[$period] ?? 'Bu ay';
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public static function projectLabel(array $filters): string
    {
        $projectId = $filters['project_id'] ?? null;

        if (! filled($projectId)) {
            return 'Tüm faaliyetler';
        }

        return Project::query()->whereKey($projectId)->value('title') ?? 'Seçili faaliyet';
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array{0: Carbon, 1: Carbon}|null
     */
    public static function resolveRange(string $period, array $filters = []): ?array
    {
        $now = Carbon::now();

        if ($period === 'all_time') {
            return null;
        }

        if ($period === 'relative') {
            $amount = max(1, (int) ($filters['relative_amount'] ?? 1));
            $unit = (string) ($filters['relative_unit'] ?? 'days');

            $from = match ($unit) {
                'hours' => $now->copy()->subHours($amount),
                'weeks' => $now->copy()->subWeeks($amount),
                'months' => $now->copy()->subMonths($amount),
                default => $now->copy()->subDays($amount),
            };

            return [$from, $now];
        }

        if ($period === 'custom_range') {
            if (! filled($filters['from'] ?? null)) {
                return null;
            }

            $from = Carbon::parse($filters['from']);
            $until = filled($filters['until'] ?? null)
                ? Carbon::parse($filters['until'])
                : $now;

            return [$from, $until];
        }

        return match ($period) {
            'today' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'yesterday' => [
                $now->copy()->subDay()->startOfDay(),
                $now->copy()->subDay()->endOfDay(),
            ],
            'this_week' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            'last_week' => [
                $now->copy()->subWeek()->startOfWeek(),
                $now->copy()->subWeek()->endOfWeek(),
            ],
            'this_month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'last_month' => [
                $now->copy()->subMonth()->startOfMonth(),
                $now->copy()->subMonth()->endOfMonth(),
            ],
            'this_year' => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            'last_year' => [
                $now->copy()->subYear()->startOfYear(),
                $now->copy()->subYear()->endOfYear(),
            ],
            'last_2_hours' => [$now->copy()->subHours(2), $now],
            'last_6_hours' => [$now->copy()->subHours(6), $now],
            'last_24_hours' => [$now->copy()->subHours(24), $now],
            'last_7_days' => [$now->copy()->subDays(7)->startOfDay(), $now],
            'last_30_days' => [$now->copy()->subDays(30)->startOfDay(), $now],
            'last_90_days' => [$now->copy()->subDays(90)->startOfDay(), $now],
            default => null,
        };
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public static function applyDashboardFilters(Builder $query, array $filters, string $column = 'donated_at'): Builder
    {
        $period = (string) ($filters['period'] ?? 'this_month');
        $range = self::resolveRange($period, $filters);

        if ($range !== null) {
            [$from, $to] = $range;
            $query->whereBetween($column, [$from, $to]);
        }

        if (filled($filters['project_id'] ?? null)) {
            $query->where('project_id', $filters['project_id']);
        }

        return $query;
    }

    public static function apply(Builder $query, ?string $preset, ?string $from = null, ?string $until = null, string $column = 'donated_at'): Builder
    {
        if ($preset) {
            $range = self::resolveRange($preset, [
                'from' => $from,
                'until' => $until,
            ]);

            if ($range !== null) {
                [$start, $end] = $range;

                return $query->whereBetween($column, [$start, $end]);
            }

            return $query;
        }

        return $query
            ->when($from, fn (Builder $q, $date) => $q->whereDate($column, '>=', $date))
            ->when($until, fn (Builder $q, $date) => $q->whereDate($column, '<=', $date));
    }
}
