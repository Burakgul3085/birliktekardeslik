<?php

namespace App\Filament\Crm\Widgets;

use App\Filament\Crm\Widgets\Concerns\InteractsWithCrmDashboardFilters;
use App\Models\Project;
use App\Support\Crm\DonationDateFilter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class TopProjectsWidget extends TableWidget
{
    use InteractsWithCrmDashboardFilters;

    protected static ?int $sort = 6;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Faaliyet Özeti';

    public function table(Table $table): Table
    {
        $filters = $this->dashboardFilters();

        return $table
            ->query(
                Project::query()
                    ->when(
                        filled($filters['project_id'] ?? null),
                        fn (Builder $query) => $query->whereKey($filters['project_id']),
                    )
                    ->withCount(['donations' => fn (Builder $query) => DonationDateFilter::applyDashboardFilters($query, $filters)])
                    ->withSum(['donations' => fn (Builder $query) => DonationDateFilter::applyDashboardFilters($query, $filters)], 'amount')
                    ->having('donations_count', '>', 0)
                    ->orderByDesc('donations_sum_amount')
                    ->limit(8),
            )
            ->paginated(false)
            ->columns([
                TextColumn::make('title')->label('Proje / Faaliyet')->wrap(),
                TextColumn::make('donations_count')
                    ->label('Bağış Sayısı')
                    ->alignCenter(),
                TextColumn::make('donations_sum_amount')
                    ->label('Toplam Tutar')
                    ->formatStateUsing(fn ($state): string => number_format((float) $state, 2, ',', '.') . ' TRY')
                    ->alignEnd(),
            ]);
    }
}
