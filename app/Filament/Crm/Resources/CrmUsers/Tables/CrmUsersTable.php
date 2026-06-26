<?php

namespace App\Filament\Crm\Resources\CrmUsers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CrmUsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')->label('Ad Soyad')->searchable()->sortable(),
                TextColumn::make('email')->label('E-posta')->searchable(),
                TextColumn::make('role')
                    ->label('Rol')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'super_admin' => 'Süper Yönetici',
                        'manager' => 'Yönetici',
                        'staff' => 'Personel',
                        'viewer' => 'Görüntüleyici',
                        default => $state,
                    }),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
                TextColumn::make('created_at')->label('Oluşturulma')->dateTime('d.m.Y')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make()->label('Düzenle'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Seçilenleri sil'),
                ]),
            ]);
    }
}
