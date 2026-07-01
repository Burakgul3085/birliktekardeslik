<?php

namespace App\Filament\Crm\Resources\Notes\Tables;

use App\Filament\Crm\Resources\Donations\DonationResource;
use App\Filament\Crm\Resources\Donors\DonorResource;
use App\Models\CrmNote;
use App\Models\CrmUser;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class NotesTable
{
    public static function configure(Table $table, bool $compact = false): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns(array_filter([
                IconColumn::make('is_pinned')
                    ->label('')
                    ->boolean()
                    ->trueIcon('heroicon-s-bookmark')
                    ->falseIcon('heroicon-o-bookmark')
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->width('40px'),
                TextColumn::make('display_title')
                    ->label('Başlık / Özet')
                    ->searchable(['title', 'body'])
                    ->wrap()
                    ->limit($compact ? 50 : 80),
                ! $compact ? TextColumn::make('scope')
                    ->label('Tür')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => CrmNote::SCOPES[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'general' => 'info',
                        'donor' => 'success',
                        'donation' => 'warning',
                        default => 'gray',
                    }) : null,
                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => CrmNote::CATEGORIES[$state] ?? $state)
                    ->color('gray'),
                ! $compact ? TextColumn::make('related_label')
                    ->label('İlgili kayıt')
                    ->placeholder('—')
                    ->url(function (CrmNote $record): ?string {
                        return match ($record->scope) {
                            'donor' => $record->donor_id
                                ? DonorResource::getUrl('edit', ['record' => $record->donor_id])
                                : null,
                            'donation' => $record->donation_id
                                ? DonationResource::getUrl('edit', ['record' => $record->donation_id])
                                : null,
                            default => null,
                        };
                    }) : null,
                TextColumn::make('author.name')
                    ->label('Yazan')
                    ->placeholder('—')
                    ->toggleable(! $compact),
                TextColumn::make('visibility')
                    ->label('Görünürlük')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => CrmNote::VISIBILITIES[$state] ?? $state)
                    ->color(fn (string $state): string => $state === 'private' ? 'warning' : 'gray')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ]))
            ->filters($compact ? [] : [
                SelectFilter::make('scope')
                    ->label('Tür')
                    ->options(CrmNote::SCOPES),
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->options(CrmNote::CATEGORIES),
                SelectFilter::make('visibility')
                    ->label('Görünürlük')
                    ->options(CrmNote::VISIBILITIES),
                SelectFilter::make('crm_user_id')
                    ->label('Yazan')
                    ->options(fn (): array => CrmUser::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable(),
                TernaryFilter::make('is_pinned')
                    ->label('Sabitleme')
                    ->placeholder('Tümü')
                    ->trueLabel('Sabitlenmiş')
                    ->falseLabel('Sabitlenmemiş'),
            ])
            ->recordActions([
                Action::make('togglePin')
                    ->label(fn (CrmNote $record): string => $record->is_pinned ? 'Sabiti kaldır' : 'Sabitle')
                    ->icon(fn (CrmNote $record): string => $record->is_pinned ? 'heroicon-o-bookmark-slash' : 'heroicon-o-bookmark')
                    ->color('warning')
                    ->visible(fn (): bool => auth('crm')->user()?->canWriteNotes() ?? false)
                    ->action(function (CrmNote $record): void {
                        $record->update(['is_pinned' => ! $record->is_pinned]);
                    }),
                EditAction::make()
                    ->label('Düzenle')
                    ->visible(fn (CrmNote $record): bool => auth('crm')->user()?->canEditNote($record) ?? false),
                DeleteAction::make()
                    ->label('Sil')
                    ->visible(fn (CrmNote $record): bool => auth('crm')->user()?->canDeleteNote($record) ?? false),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Seçilenleri sil')
                        ->visible(fn (): bool => auth('crm')->user()?->canDeleteRecords() ?? false),
                ]),
            ]);
    }
}
