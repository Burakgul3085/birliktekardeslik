<?php

namespace App\Filament\Crm\Widgets;

use App\Models\Donation;
use App\Models\Donor;
use App\Support\Crm\DonationStats;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CrmOverviewStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected function getColumns(): int
    {
        return 3;
    }

    protected function getStats(): array
    {
        $donorCount = Donor::query()->count();
        $donationCount = Donation::query()->count();

        return [
            Stat::make('Bugün', DonationStats::formatMoney(DonationStats::today()))
                ->description('Günlük bağış')
                ->color('success')
                ->icon('heroicon-o-sun'),
            Stat::make('Bu Hafta', DonationStats::formatMoney(DonationStats::thisWeek()))
                ->description('Haftalık toplam')
                ->color('primary')
                ->icon('heroicon-o-calendar-days'),
            Stat::make('Bu Ay', DonationStats::formatMoney(DonationStats::thisMonth()))
                ->description('Aylık toplam')
                ->color('info')
                ->icon('heroicon-o-calendar'),
            Stat::make('Bu Yıl', DonationStats::formatMoney(DonationStats::thisYear()))
                ->description('Yıllık toplam')
                ->color('warning')
                ->icon('heroicon-o-chart-bar'),
            Stat::make('Toplam Bağış', DonationStats::formatMoney(DonationStats::total()))
                ->description($donationCount . ' kayıt')
                ->color('success')
                ->icon('heroicon-o-banknotes'),
            Stat::make('Bağışçı', (string) $donorCount)
                ->description('Kayıtlı bağışçı')
                ->color('gray')
                ->icon('heroicon-o-user-group'),
        ];
    }
}
