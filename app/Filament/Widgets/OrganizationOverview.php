<?php

namespace App\Filament\Widgets;

use App\Models\BankAccount;
use App\Models\HeroSlide;
use App\Models\MenuItem;
use App\Models\News;
use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrganizationOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getColumns(): int
    {
        $user = auth()->user();

        if ($user?->isViewer()) {
            return 3;
        }

        return 5;
    }

    protected function getStats(): array
    {
        $user = auth()->user();

        if ($user?->isViewer()) {
            return [
                Stat::make('Aktif Proje', (string) Project::query()->active()->count())
                    ->description('Güncel proje sayısı')
                    ->color('success'),
                Stat::make('Yayınlanan Haber', (string) News::query()->active()->count())
                    ->description('Yayınlanan duyurular')
                    ->color('primary'),
                Stat::make('Bağış Hesabı', (string) BankAccount::query()->active()->count())
                    ->description('Aktif hesap bilgileri')
                    ->color('warning'),
            ];
        }

        return [
            Stat::make('Aktif Proje', (string) Project::query()->active()->count())
                ->description('Sahadaki devam eden çalışmalar')
                ->color('success'),
            Stat::make('Yayınlanan Haber', (string) News::query()->active()->count())
                ->description('Kamuoyu bilgilendirme içerikleri')
                ->color('primary'),
            Stat::make('Bağış Hesabı', (string) BankAccount::query()->active()->count())
                ->description('Sitede görünen aktif hesaplar')
                ->color('warning'),
            Stat::make('Hero Slide', (string) HeroSlide::query()->active()->count())
                ->description('Anasayfa slider içeriği')
                ->color('gray'),
            Stat::make('Menü Öğeleri', (string) MenuItem::query()->active()->count())
                ->description('Üst menü bağlantıları')
                ->color('gray'),
        ];
    }
}
