<?php

namespace App\Support\Crm;

use App\Filament\Crm\Pages\CrmDashboard;

class DashboardFilterResolver
{
    public static function sessionKey(): string
    {
        return md5(CrmDashboard::class) . '_filters';
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return [
            'period' => 'this_month',
            'relative_amount' => 3,
            'relative_unit' => 'weeks',
            'from' => null,
            'until' => null,
            'project_id' => null,
        ];
    }

    /**
     * @param  array<string, mixed>|null  $filters
     * @return array<string, mixed>
     */
    public static function normalize(?array $filters): array
    {
        $filters = array_merge(self::defaults(), $filters ?? []);

        $filters['period'] = (string) ($filters['period'] ?? 'this_month');
        $filters['relative_amount'] = max(1, (int) ($filters['relative_amount'] ?? 1));
        $filters['relative_unit'] = (string) ($filters['relative_unit'] ?? 'weeks');

        $filters['project_id'] = filled($filters['project_id'] ?? null)
            ? (int) $filters['project_id']
            : null;

        if ($filters['period'] !== 'custom_range') {
            $filters['from'] = null;
            $filters['until'] = null;
        } else {
            $filters['from'] = filled($filters['from'] ?? null) ? (string) $filters['from'] : null;
            $filters['until'] = filled($filters['until'] ?? null) ? (string) $filters['until'] : null;
        }

        return $filters;
    }

    /**
     * @return array<string, mixed>
     */
    public static function get(): array
    {
        $stored = session(self::sessionKey());

        if (! is_array($stored) || $stored === []) {
            return self::defaults();
        }

        return self::normalize($stored);
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public static function store(array $filters): array
    {
        $filters = self::normalize($filters);
        session([self::sessionKey() => $filters]);

        return $filters;
    }
}
