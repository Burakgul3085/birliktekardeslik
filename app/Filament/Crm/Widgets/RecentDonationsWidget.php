<?php

namespace App\Filament\Crm\Widgets;

use App\Filament\Crm\Resources\Donations\DonationResource;
use App\Filament\Crm\Widgets\Concerns\InteractsWithCrmDashboardFilters;
use App\Models\Donation;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentDonationsWidget extends TableWidget
{
    use InteractsWithCrmDashboardFilters;

    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Son Bağışlar';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                $this->filteredDonationsQuery()
                    ->with(['donor', 'donationType', 'paymentMethod', 'project'])
                    ->latest('donated_at')
                    ->limit(8),
            )
            ->paginated(false)
            ->columns([
                TextColumn::make('donation_number')
                    ->label('Bağış No')
                    ->url(fn (Donation $record): string => DonationResource::getUrl('edit', ['record' => $record])),
                TextColumn::make('donor.full_name')->label('Bağışçı'),
                TextColumn::make('project.title')->label('Faaliyet')->placeholder('-'),
                TextColumn::make('donationType.name')->label('Tür')->placeholder('-'),
                TextColumn::make('amount')->label('Tutar')->money(fn (Donation $record) => $record->currency ?? 'TRY'),
                TextColumn::make('donated_at')->label('Tarih')->dateTime('d.m.Y H:i'),
            ]);
    }
}
