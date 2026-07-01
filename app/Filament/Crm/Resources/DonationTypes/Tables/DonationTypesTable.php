<?php

namespace App\Filament\Crm\Resources\DonationTypes\Tables;

use App\Models\DonationType;
use App\Support\Crm\LookupDeletionGuard;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class DonationTypesTable
{
    public static function configure(Table $table): Table
    {
        $guard = app(LookupDeletionGuard::class);

        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Bağış Türü')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('donations_count')
                    ->label('Bağış Sayısı')
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('donations_sum_amount')
                    ->label('Toplam Tutar')
                    ->formatStateUsing(fn ($state): string => number_format((float) $state, 2, ',', '.') . ' TRY')
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label('Sıra')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label('Güncellenme')
                    ->dateTime('d.m.Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Durum')
                    ->placeholder('Tümü')
                    ->trueLabel('Aktif')
                    ->falseLabel('Pasif'),
            ])
            ->recordActions([
                EditAction::make()->label('Düzenle'),
                Action::make('toggleActive')
                    ->label(fn (DonationType $record): string => $record->is_active ? 'Pasife al' : 'Aktifleştir')
                    ->icon(fn (DonationType $record): string => $record->is_active ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->color(fn (DonationType $record): string => $record->is_active ? 'warning' : 'success')
                    ->visible(fn (): bool => auth('crm')->user()?->canWriteDonations() ?? false)
                    ->requiresConfirmation()
                    ->action(function (DonationType $record) use ($guard): void {
                        $wasActive = $record->is_active;
                        $guard->toggleActive($record, ! $wasActive);
                        Notification::make()
                            ->title(! $wasActive ? 'Bağış türü aktifleştirildi' : 'Bağış türü pasife alındı')
                            ->success()
                            ->send();
                    }),
                DeleteAction::make()
                    ->label('Sil')
                    ->visible(fn (): bool => auth('crm')->user()?->canWriteDonations() ?? false)
                    ->modalDescription(fn (DonationType $record): string => $guard->deleteWarning($record))
                    ->action(function (DonationType $record) use ($guard): void {
                        $result = $guard->deleteOrDeactivate($record);
                        Notification::make()
                            ->title($result['success'] ? 'İşlem tamamlandı' : 'İşlem yapılamadı')
                            ->body($result['message'])
                            ->{$result['success'] ? 'success' : 'warning'}()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([]),
            ]);
    }
}
