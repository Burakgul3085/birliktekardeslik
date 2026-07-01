<?php

namespace App\Filament\Crm\Resources\CrmProjects\Tables;

use App\Models\Project;
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

class CrmProjectsTable
{
    public static function configure(Table $table): Table
    {
        $guard = app(LookupDeletionGuard::class);

        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Proje / Faaliyet')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('donations_count')
                    ->label('Bağış Sayısı')
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('donations_sum_amount')
                    ->label('Toplam Tutar')
                    ->formatStateUsing(fn ($state): string => number_format((float) $state, 2, ',', '.') . ' TRY')
                    ->alignEnd()
                    ->sortable(),
                IconColumn::make('website_published')
                    ->label('Web')
                    ->boolean()
                    ->getStateUsing(fn (Project $record): bool => $guard->isPublishedWebsiteProject($record))
                    ->trueIcon('heroicon-o-globe-alt')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('info')
                    ->falseColor('gray')
                    ->tooltip(fn (Project $record): string => $guard->isPublishedWebsiteProject($record)
                        ? 'Web sitesinde içerik var'
                        : 'Yalnızca CRM kaydı'),
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
                    ->label(fn (Project $record): string => $record->is_active ? 'Pasife al' : 'Aktifleştir')
                    ->icon(fn (Project $record): string => $record->is_active ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->color(fn (Project $record): string => $record->is_active ? 'warning' : 'success')
                    ->visible(fn (): bool => auth('crm')->user()?->canWriteDonations() ?? false)
                    ->requiresConfirmation()
                    ->action(function (Project $record) use ($guard): void {
                        $wasActive = $record->is_active;
                        $guard->toggleActive($record, ! $wasActive);
                        Notification::make()
                            ->title(! $wasActive ? 'Faaliyet aktifleştirildi' : 'Faaliyet pasife alındı')
                            ->success()
                            ->send();
                    }),
                DeleteAction::make()
                    ->label('Sil')
                    ->visible(fn (): bool => auth('crm')->user()?->canWriteDonations() ?? false)
                    ->modalDescription(fn (Project $record): string => $guard->deleteWarning($record))
                    ->action(function (Project $record) use ($guard): void {
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
