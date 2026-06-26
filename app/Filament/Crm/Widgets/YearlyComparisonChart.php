<?php

namespace App\Filament\Crm\Widgets;

use App\Models\Donation;
use Filament\Widgets\ChartWidget;

class YearlyComparisonChart extends ChartWidget
{
    protected static ?int $sort = 4;

    protected ?string $heading = 'Yıllık Karşılaştırma';

    protected ?string $description = 'Bu yıl ve geçen yıl (aylık)';

    protected ?string $maxHeight = '300px';

    protected int | string | array $columnSpan = 'full';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $labels = [];
        $thisYear = [];
        $lastYear = [];

        $currentYear = now()->year;
        $previousYear = $currentYear - 1;

        for ($month = 1; $month <= 12; $month++) {
            $labels[] = now()->month($month)->locale('tr')->translatedFormat('M');

            $thisYear[] = (float) Donation::query()
                ->whereYear('donated_at', $currentYear)
                ->whereMonth('donated_at', $month)
                ->sum('amount');

            $lastYear[] = (float) Donation::query()
                ->whereYear('donated_at', $previousYear)
                ->whereMonth('donated_at', $month)
                ->sum('amount');
        }

        return [
            'datasets' => [
                [
                    'label' => (string) $currentYear,
                    'data' => $thisYear,
                    'backgroundColor' => '#0d9488',
                ],
                [
                    'label' => (string) $previousYear,
                    'data' => $lastYear,
                    'backgroundColor' => '#94a3b8',
                ],
            ],
            'labels' => $labels,
        ];
    }
}
