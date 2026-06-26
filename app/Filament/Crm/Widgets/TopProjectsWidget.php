<?php

namespace App\Filament\Crm\Widgets;

use App\Models\Project;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class TopProjectsWidget extends TableWidget
{
    protected static ?int $sort = 6;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'En Çok Desteklenen Projeler';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Project::query()
                    ->withSum('donations', 'amount')
                    ->withCount('donations')
                    ->having('donations_count', '>', 0)
                    ->orderByDesc('donations_sum_amount')
                    ->limit(6),
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
