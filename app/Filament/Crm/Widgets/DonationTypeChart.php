<?php

namespace App\Filament\Crm\Widgets;

use App\Models\Donation;
use App\Models\DonationType;
use Filament\Widgets\ChartWidget;

class DonationTypeChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'Bağış Türlerine Göre Dağılım';

    protected ?string $maxHeight = '300px';

    protected int | string | array $columnSpan = [
        'default' => 'full',
        'lg' => 1,
    ];

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $types = DonationType::query()
            ->withSum('donations', 'amount')
            ->orderBy('sort_order')
            ->get();

        $labels = [];
        $data = [];
        $colors = [
            '#0d9488',
            '#14b8a6',
            '#2dd4bf',
            '#5eead4',
            '#0f766e',
            '#115e59',
            '#134e4a',
        ];

        foreach ($types as $index => $type) {
            $amount = (float) ($type->donations_sum_amount ?? 0);
            if ($amount <= 0) {
                continue;
            }

            $labels[] = $type->name;
            $data[] = $amount;
        }

        if ($labels === []) {
            $labels[] = 'Henüz bağış yok';
            $data[] = 1;
        }

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        $hasDonations = Donation::query()->exists();

        return [
            'plugins' => [
                'legend' => [
                    'display' => $hasDonations,
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
