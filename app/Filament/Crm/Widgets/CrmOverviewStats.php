<?php

namespace App\Filament\Crm\Widgets;

use App\Filament\Crm\Widgets\Concerns\InteractsWithCrmDashboardFilters;
use App\Models\Donor;
use App\Support\Crm\DonationDateFilter;
use App\Support\Crm\DonationStats;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CrmOverviewStats extends StatsOverviewWidget
{
    use InteractsWithCrmDashboardFilters;

    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected function getColumns(): int | array
    {
        return [
            'default' => 1,
            'sm' => 2,
            'lg' => 4,
        ];
    }

    protected function getStats(): array
    {
        $filters = $this->dashboardFilters();
        $query = $this->filteredDonationsQuery();

        $totalAmount = (float) (clone $query)->sum('amount');
        $donationCount = (int) (clone $query)->count();
        $donorCount = (int) (clone $query)->distinct('donor_id')->count('donor_id');
        $periodLabel = DonationDateFilter::dashboardPeriodLabel($filters);
        $projectLabel = DonationDateFilter::projectLabel($filters);

        return [
            Stat::make('Toplam Tutar', DonationStats::formatMoney($totalAmount))
                ->description($periodLabel)
                ->color('success')
                ->icon('heroicon-o-banknotes'),
            Stat::make('Bağış Sayısı', (string) $donationCount)
                ->description($projectLabel)
                ->color('primary')
                ->icon('heroicon-o-document-text'),
            Stat::make('Bağışçı', (string) $donorCount)
                ->description('Filtredeki benzersiz bağışçı')
                ->color('info')
                ->icon('heroicon-o-user-group'),
            Stat::make('Kayıtlı Bağışçı', (string) Donor::query()->count())
                ->description('Tüm zamanlar')
                ->color('gray')
                ->icon('heroicon-o-users'),
        ];
    }
}
