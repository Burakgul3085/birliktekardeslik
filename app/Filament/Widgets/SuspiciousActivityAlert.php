<?php

namespace App\Filament\Widgets;

use App\Models\AdminActivityLog;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SuspiciousActivityAlert extends StatsOverviewWidget
{
    protected static ?int $sort = 0;

    public static function canView(): bool
    {
        return auth()->check() && auth()->user()?->isSuperAdmin();
    }

    protected function getStats(): array
    {
        $windowStart = now()->subMinutes(15);

        $intenseUsers = AdminActivityLog::query()
            ->selectRaw('causer_id, COUNT(*) as total')
            ->whereNotNull('causer_id')
            ->where('created_at', '>=', $windowStart)
            ->groupBy('causer_id')
            ->having('total', '>=', 40)
            ->with('causer:id,name')
            ->get();

        $count = $intenseUsers->count();

        $topUsers = $intenseUsers
            ->sortByDesc('total')
            ->take(3)
            ->map(fn ($row) => (optional($row->causer)->name ?? 'Bilinmeyen') . ' (' . $row->total . ')')
            ->implode(', ');

        return [
            Stat::make('Şüpheli Hareket Uyarisi', $count > 0 ? (string) $count : '0')
                ->description($count > 0
                    ? 'Son 15 dk yuksek trafik: ' . $topUsers
                    : 'Son 15 dakikada supheli yogunluk algilanmadi')
                ->color($count > 0 ? 'danger' : 'success'),
        ];
    }
}
