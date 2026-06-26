<?php

namespace App\Filament\Crm\Resources\Donors\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;

class DonorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('full_name')
                    ->label('Ad Soyad')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(['last_name', 'first_name']),
                TextColumn::make('phone')
                    ->label('Telefon')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('email')
                    ->label('E-posta')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('city')
                    ->label('Şehir')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('donations_count')
                    ->label('Bağış')
                    ->counts('donations')
                    ->sortable(),
                TextColumn::make('donations_sum_amount')
                    ->label('Toplam')
                    ->sum('donations', 'amount')
                    ->money('TRY')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Kayıt')
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('contact')
                    ->label('İletişim')
                    ->form([
                        TextInput::make('phone')->label('Telefon'),
                        TextInput::make('city')->label('Şehir'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['phone'] ?? null, fn (Builder $q, $phone) => $q->where('phone', 'like', "%{$phone}%"))
                            ->when($data['city'] ?? null, fn (Builder $q, $city) => $q->where('city', 'like', "%{$city}%"));
                    }),
            ])
            ->recordActions([
                ViewAction::make()->label('Profil'),
                EditAction::make()->label('Düzenle'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Seçilenleri sil'),
                ]),
            ]);
    }
}
