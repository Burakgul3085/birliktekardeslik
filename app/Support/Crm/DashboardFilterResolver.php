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
     * @return array<string, mixed>
     */
    public static function get(): array
    {
        $stored = session(self::sessionKey());

        if (! is_array($stored) || $stored === []) {
            return self::defaults();
        }

        return array_merge(self::defaults(), $stored);
    }
}
