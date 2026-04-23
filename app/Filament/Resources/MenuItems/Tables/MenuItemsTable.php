<?php

namespace App\Filament\Resources\MenuItems\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MenuItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table->defaultSort('sort_order')
            ->columns([
                TextColumn::make('label')->label('Başlık')->searchable(),
                TextColumn::make('url')->label('URL'),
                TextColumn::make('sort_order')->label('Sıra'),
                TextColumn::make('footer_group')
                    ->label('Footer')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'quick' => 'Hızlı erişim',
                        'activities' => 'Faaliyetlerimiz',
                        default => '—',
                    }),
                IconColumn::make('is_active')->boolean()->label('Aktif'),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
