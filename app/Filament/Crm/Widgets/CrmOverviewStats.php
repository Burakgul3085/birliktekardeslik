<?php

namespace App\Filament\Crm\Widgets;

use App\Models\Donation;
use App\Models\Donor;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CrmOverviewStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalAmount = (float) Donation::query()->sum('amount');
        $donorCount = Donor::query()->count();
        $donationCount = Donation::query()->count();
        $monthAmount = (float) Donation::query()
            ->whereBetween('donated_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('amount');

        return [
            Stat::make('Toplam Bağış', number_format($totalAmount, 2, ',', '.') . ' TRY')
                ->description('Tüm zamanlar')
                ->color('success'),
            Stat::make('Bu Ay', number_format($monthAmount, 2, ',', '.') . ' TRY')
                ->description('Aylık toplam')
                ->color('primary'),
            Stat::make('Bağışçı', (string) $donorCount)
                ->description($donationCount . ' bağış kaydı')
                ->color('warning'),
        ];
    }
}
