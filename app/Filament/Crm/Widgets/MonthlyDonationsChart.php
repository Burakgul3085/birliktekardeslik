<?php

namespace App\Filament\Crm\Widgets;

use App\Models\Donation;
use Filament\Widgets\ChartWidget;

class MonthlyDonationsChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Aylık Bağış Grafiği';

    protected ?string $description = 'Son 12 ay';

    protected ?string $maxHeight = '300px';

    protected int | string | array $columnSpan = [
        'default' => 'full',
        'lg' => 2,
    ];

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $labels = [];
        $data = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $labels[] = $month->locale('tr')->translatedFormat('M Y');
            $data[] = (float) Donation::query()
                ->whereYear('donated_at', $month->year)
                ->whereMonth('donated_at', $month->month)
                ->sum('amount');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Bağış (TRY)',
                    'data' => $data,
                    'fill' => true,
                    'tension' => 0.3,
                    'borderColor' => '#0d9488',
                    'backgroundColor' => 'rgba(13, 148, 136, 0.12)',
                ],
            ],
            'labels' => $labels,
        ];
    }
}
