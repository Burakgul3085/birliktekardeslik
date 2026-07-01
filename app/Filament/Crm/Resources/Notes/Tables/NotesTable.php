<?php

namespace App\Filament\Crm\Resources\Notes\Tables;

use App\Filament\Crm\Resources\Donations\DonationResource;
use App\Filament\Crm\Resources\Donors\DonorResource;
use App\Models\CrmNote;
use App\Models\CrmUser;
use App\Models\Donation;
use App\Models\Donor;
use App\Support\Crm\DonationDateFilter;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                    ->searchable()
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
                ! $compact ? TextColumn::make('updated_at')
                    ->label('Güncellenme')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true) : null,
            ]))
            ->filters($compact ? [] : self::filters())
            ->filtersFormColumns(3)
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

    /**
     * @return array<int, Filter|SelectFilter|TernaryFilter>
     */
    public static function filters(): array
    {
        return [
            Filter::make('content')
                ->label('Metin')
                ->form([
                    TextInput::make('title')
                        ->label('Başlık')
                        ->placeholder('Başlıkta ara...'),
                    TextInput::make('body')
                        ->label('Not içeriği')
                        ->placeholder('İçerikte ara...'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(filled($data['title'] ?? null), fn (Builder $q): Builder => $q->where(
                            'title',
                            'like',
                            '%' . $data['title'] . '%',
                        ))
                        ->when(filled($data['body'] ?? null), fn (Builder $q): Builder => $q->where(
                            'body',
                            'like',
                            '%' . $data['body'] . '%',
                        ));
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];

                    if (filled($data['title'] ?? null)) {
                        $indicators['title'] = 'Başlık: ' . $data['title'];
                    }

                    if (filled($data['body'] ?? null)) {
                        $indicators['body'] = 'İçerik: ' . $data['body'];
                    }

                    return $indicators;
                }),

            SelectFilter::make('scope')
                ->label('Tür')
                ->options(CrmNote::SCOPES)
                ->multiple(),

            SelectFilter::make('category')
                ->label('Kategori')
                ->options(CrmNote::CATEGORIES)
                ->multiple(),

            SelectFilter::make('visibility')
                ->label('Görünürlük')
                ->options(CrmNote::VISIBILITIES),

            SelectFilter::make('crm_user_id')
                ->label('Yazan')
                ->options(fn (): array => CrmUser::query()->orderBy('name')->pluck('name', 'id')->all())
                ->searchable()
                ->multiple(),

            TernaryFilter::make('is_pinned')
                ->label('Sabitleme')
                ->placeholder('Tümü')
                ->trueLabel('Sabitlenmiş')
                ->falseLabel('Sabitlenmemiş'),

            SelectFilter::make('donor_id')
                ->label('Bağışçı')
                ->searchable()
                ->getSearchResultsUsing(function (string $search): array {
                    return Donor::query()
                        ->where(function (Builder $query) use ($search): void {
                            $query->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        })
                        ->orderBy('last_name')
                        ->orderBy('first_name')
                        ->limit(50)
                        ->get()
                        ->mapWithKeys(fn (Donor $donor): array => [$donor->id => $donor->full_name])
                        ->all();
                })
                ->getOptionLabelUsing(fn ($value): ?string => Donor::query()->find($value)?->full_name),

            SelectFilter::make('donation_id')
                ->label('Bağış')
                ->searchable()
                ->getSearchResultsUsing(function (string $search): array {
                    return Donation::query()
                        ->with('donor')
                        ->where(function (Builder $query) use ($search): void {
                            $query->where('donation_number', 'like', "%{$search}%")
                                ->orWhere('receipt_number', 'like', "%{$search}%")
                                ->orWhereHas('donor', fn (Builder $donorQuery) => $donorQuery
                                    ->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%"));
                        })
                        ->latest('donated_at')
                        ->limit(50)
                        ->get()
                        ->mapWithKeys(fn (Donation $donation): array => [
                            $donation->id => trim($donation->donation_number . ' — ' . ($donation->donor?->full_name ?? '')),
                        ])
                        ->all();
                })
                ->getOptionLabelUsing(function ($value): ?string {
                    $donation = Donation::query()->with('donor')->find($value);

                    if (! $donation) {
                        return null;
                    }

                    return trim($donation->donation_number . ' — ' . ($donation->donor?->full_name ?? ''));
                }),

            Filter::make('donor_contact')
                ->label('Bağışçı bilgisi')
                ->form([
                    TextInput::make('first_name')->label('Ad'),
                    TextInput::make('last_name')->label('Soyad'),
                    TextInput::make('phone')->label('Telefon'),
                    TextInput::make('city')->label('Şehir'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    $hasCriteria = filled($data['first_name'] ?? null)
                        || filled($data['last_name'] ?? null)
                        || filled($data['phone'] ?? null)
                        || filled($data['city'] ?? null);

                    if (! $hasCriteria) {
                        return $query;
                    }

                    return $query->where(function (Builder $outer) use ($data): void {
                        $outer
                            ->whereHas('donor', function (Builder $donorQuery) use ($data): void {
                                $donorQuery
                                    ->when(filled($data['first_name'] ?? null), fn (Builder $q) => $q->where('first_name', 'like', '%' . $data['first_name'] . '%'))
                                    ->when(filled($data['last_name'] ?? null), fn (Builder $q) => $q->where('last_name', 'like', '%' . $data['last_name'] . '%'))
                                    ->when(filled($data['phone'] ?? null), fn (Builder $q) => $q->where('phone', 'like', '%' . $data['phone'] . '%'))
                                    ->when(filled($data['city'] ?? null), fn (Builder $q) => $q->where('city', 'like', '%' . $data['city'] . '%'));
                            })
                            ->orWhereHas('donation.donor', function (Builder $donorQuery) use ($data): void {
                                $donorQuery
                                    ->when(filled($data['first_name'] ?? null), fn (Builder $q) => $q->where('first_name', 'like', '%' . $data['first_name'] . '%'))
                                    ->when(filled($data['last_name'] ?? null), fn (Builder $q) => $q->where('last_name', 'like', '%' . $data['last_name'] . '%'))
                                    ->when(filled($data['phone'] ?? null), fn (Builder $q) => $q->where('phone', 'like', '%' . $data['phone'] . '%'))
                                    ->when(filled($data['city'] ?? null), fn (Builder $q) => $q->where('city', 'like', '%' . $data['city'] . '%'));
                            });
                    });
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];

                    if (filled($data['first_name'] ?? null)) {
                        $indicators[] = 'Ad: ' . $data['first_name'];
                    }

                    if (filled($data['last_name'] ?? null)) {
                        $indicators[] = 'Soyad: ' . $data['last_name'];
                    }

                    if (filled($data['phone'] ?? null)) {
                        $indicators[] = 'Telefon: ' . $data['phone'];
                    }

                    if (filled($data['city'] ?? null)) {
                        $indicators[] = 'Şehir: ' . $data['city'];
                    }

                    return $indicators;
                }),

            Filter::make('donation_number')
                ->label('Bağış no / Makbuz no')
                ->form([
                    TextInput::make('number')
                        ->label('Numara')
                        ->placeholder('BAG-2026-00001'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    if (! filled($data['number'] ?? null)) {
                        return $query;
                    }

                    $number = $data['number'];

                    return $query->whereHas('donation', fn (Builder $donationQuery) => $donationQuery
                        ->where('donation_number', 'like', "%{$number}%")
                        ->orWhere('receipt_number', 'like', "%{$number}%"));
                })
                ->indicateUsing(fn (array $data): array => filled($data['number'] ?? null)
                    ? ['number' => 'No: ' . $data['number']]
                    : []),

            Filter::make('created_at')
                ->label('Oluşturulma tarihi')
                ->form([
                    Select::make('preset')
                        ->label('Hızlı seçim')
                        ->options(DonationDateFilter::presets())
                        ->placeholder('Seçiniz'),
                    DatePicker::make('from')->label('Başlangıç'),
                    DatePicker::make('until')->label('Bitiş'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return DonationDateFilter::apply(
                        $query,
                        $data['preset'] ?? null,
                        $data['from'] ?? null,
                        $data['until'] ?? null,
                        'created_at',
                    );
                })
                ->indicateUsing(function (array $data): array {
                    if (filled($data['preset'] ?? null)) {
                        $label = DonationDateFilter::presets()[$data['preset']] ?? $data['preset'];

                        return ['preset' => 'Tarih: ' . $label];
                    }

                    $indicators = [];

                    if (filled($data['from'] ?? null)) {
                        $indicators[] = 'Başlangıç: ' . $data['from'];
                    }

                    if (filled($data['until'] ?? null)) {
                        $indicators[] = 'Bitiş: ' . $data['until'];
                    }

                    return $indicators;
                }),

            Filter::make('updated_at')
                ->label('Güncellenme tarihi')
                ->form([
                    DatePicker::make('from')->label('Başlangıç'),
                    DatePicker::make('until')->label('Bitiş'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(filled($data['from'] ?? null), fn (Builder $q) => $q->whereDate('updated_at', '>=', $data['from']))
                        ->when(filled($data['until'] ?? null), fn (Builder $q) => $q->whereDate('updated_at', '<=', $data['until']));
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];

                    if (filled($data['from'] ?? null)) {
                        $indicators[] = 'Günc. başlangıç: ' . $data['from'];
                    }

                    if (filled($data['until'] ?? null)) {
                        $indicators[] = 'Günc. bitiş: ' . $data['until'];
                    }

                    return $indicators;
                }),

            TernaryFilter::make('has_related_record')
                ->label('İlgili kayıt')
                ->placeholder('Tümü')
                ->trueLabel('Bağışçı veya bağışa bağlı')
                ->falseLabel('Yalnızca genel notlar')
                ->queries(
                    true: fn (Builder $query): Builder => $query->where(function (Builder $inner): void {
                        $inner->whereNotNull('donor_id')->orWhereNotNull('donation_id');
                    }),
                    false: fn (Builder $query): Builder => $query
                        ->where('scope', 'general')
                        ->whereNull('donor_id')
                        ->whereNull('donation_id'),
                    blank: fn (Builder $query): Builder => $query,
                ),

            Filter::make('authorship')
                ->label('Yazarlık')
                ->form([
                    Select::make('mode')
                        ->label('Seçenek')
                        ->options([
                            'mine' => 'Benim yazdıklarım',
                            'others' => 'Başkalarının yazdıkları',
                        ])
                        ->placeholder('Tümü'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    $userId = auth('crm')->id();

                    return match ($data['mode'] ?? null) {
                        'mine' => $query->where('crm_user_id', $userId),
                        'others' => $query->where('crm_user_id', '!=', $userId),
                        default => $query,
                    };
                })
                ->indicateUsing(fn (array $data): array => match ($data['mode'] ?? null) {
                    'mine' => ['mode' => 'Benim notlarım'],
                    'others' => ['mode' => 'Başkalarının notları'],
                    default => [],
                }),
        ];
    }
}
