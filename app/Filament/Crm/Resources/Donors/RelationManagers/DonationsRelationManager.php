<?php

namespace App\Filament\Crm\Resources\Donors\RelationManagers;

use App\Filament\Crm\Resources\Donations\DonationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DonationsRelationManager extends RelationManager
{
    protected static string $relationship = 'donations';

    protected static ?string $title = 'Bağış Geçmişi';

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('donated_at', 'desc')
            ->columns([
                TextColumn::make('donation_number')->label('Bağış No')->searchable(),
                TextColumn::make('donationType.name')->label('Tür')->placeholder('-'),
                TextColumn::make('amount')->label('Tutar')->money('TRY')->sortable(),
                TextColumn::make('paymentMethod.name')->label('Ödeme')->placeholder('-'),
                TextColumn::make('donated_at')->label('Tarih')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Yeni bağış')
                    ->url(fn (): string => DonationResource::getUrl('create', ['donor_id' => $this->getOwnerRecord()->getKey()])),
            ]);
    }
}
