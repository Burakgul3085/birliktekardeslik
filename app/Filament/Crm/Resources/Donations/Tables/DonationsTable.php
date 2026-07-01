<?php

namespace App\Filament\Crm\Resources\Donations\Tables;

use App\Models\Donation;
use App\Models\DonationType;
use App\Models\Donor;
use App\Models\PaymentMethod;
use App\Models\Project;
use App\Support\Crm\DonationDateFilter;
use App\Support\Crm\DonationSpreadsheetExporter;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class DonationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('donated_at', 'desc')
            ->filtersFormColumns(2)
            ->columns([
                TextColumn::make('donation_number')
                    ->label('Bağış No')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('receipt_number')
                    ->label('Makbuz No')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('donor.full_name')
                    ->label('Bağışçı')
                    ->searchable(query: fn (Builder $query, string $search): Builder => $query->whereHas(
                        'donor',
                        fn (Builder $donorQuery) => $donorQuery
                            ->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%"),
                    ))
                    ->sortable(query: fn (Builder $query, string $direction): Builder => $query->orderBy(
                        Donor::query()
                            ->select('last_name')
                            ->whereColumn('donors.id', 'donations.donor_id'),
                        $direction,
                    )),
                TextColumn::make('donor.phone')
                    ->label('Telefon')
                    ->searchable(query: fn (Builder $query, string $search): Builder => $query->whereHas(
                        'donor',
                        fn (Builder $donorQuery) => $donorQuery->where('phone', 'like', "%{$search}%"),
                    ))
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('donor.city')
                    ->label('Şehir')
                    ->searchable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('donationType.name')
                    ->label('Tür')
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('paymentMethod.name')
                    ->label('Ödeme')
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Tutar')
                    ->money(fn ($record) => $record->currency ?? 'TRY')
                    ->sortable(),
                TextColumn::make('donated_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                TextColumn::make('creator.name')
                    ->label('Kaydeden')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('donated_at')
                    ->label('Tarih')
                    ->form([
                        Select::make('preset')
                            ->label('Hızlı seçim')
                            ->options(DonationDateFilter::presets()),
                        DatePicker::make('from')->label('Başlangıç'),
                        DatePicker::make('until')->label('Bitiş'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return DonationDateFilter::apply(
                            $query,
                            $data['preset'] ?? null,
                            $data['from'] ?? null,
                            $data['until'] ?? null,
                        );
                    }),
                SelectFilter::make('donation_type_id')
                    ->label('Bağış Türü')
                    ->options(fn (): array => app(\App\Support\Crm\LookupDeletionGuard::class)->activeOptions(DonationType::class)),
                SelectFilter::make('project_id')
                    ->label('Proje / Faaliyet')
                    ->options(fn (): array => app(\App\Support\Crm\LookupDeletionGuard::class)->activeOptions(Project::class, 'title'))
                    ->searchable(),
                SelectFilter::make('payment_method_id')
                    ->label('Ödeme Türü')
                    ->options(fn (): array => app(\App\Support\Crm\LookupDeletionGuard::class)->activeOptions(PaymentMethod::class)),
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
                            ->limit(50)
                            ->get()
                            ->mapWithKeys(fn (Donor $donor): array => [$donor->id => $donor->full_name])
                            ->all();
                    })
                    ->getOptionLabelUsing(fn ($value): ?string => Donor::query()->find($value)?->full_name),
                Filter::make('donor_contact')
                    ->label('Bağışçı')
                    ->form([
                        TextInput::make('first_name')->label('Ad'),
                        TextInput::make('last_name')->label('Soyad'),
                        TextInput::make('phone')->label('Telefon'),
                        TextInput::make('city')->label('Şehir'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['first_name'] ?? null, fn (Builder $q, $name) => $q->whereHas(
                                'donor',
                                fn (Builder $donorQuery) => $donorQuery->where('first_name', 'like', "%{$name}%"),
                            ))
                            ->when($data['last_name'] ?? null, fn (Builder $q, $name) => $q->whereHas(
                                'donor',
                                fn (Builder $donorQuery) => $donorQuery->where('last_name', 'like', "%{$name}%"),
                            ))
                            ->when($data['phone'] ?? null, fn (Builder $q, $phone) => $q->whereHas(
                                'donor',
                                fn (Builder $donorQuery) => $donorQuery->where('phone', 'like', "%{$phone}%"),
                            ))
                            ->when($data['city'] ?? null, fn (Builder $q, $city) => $q->whereHas(
                                'donor',
                                fn (Builder $donorQuery) => $donorQuery->where('city', 'like', "%{$city}%"),
                            ));
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
                Filter::make('numbers')
                    ->label('Numaralar')
                    ->form([
                        TextInput::make('donation_number')->label('Bağış No'),
                        TextInput::make('receipt_number')->label('Makbuz No'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['donation_number'] ?? null, fn (Builder $q, $no) => $q->where('donation_number', 'like', "%{$no}%"))
                            ->when($data['receipt_number'] ?? null, fn (Builder $q, $no) => $q->where('receipt_number', 'like', "%{$no}%"));
                    }),
                Filter::make('amount_range')
                    ->label('Tutar Aralığı')
                    ->form([
                        TextInput::make('min')->label('Min')->numeric(),
                        TextInput::make('max')->label('Max')->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['min'] ?? null, fn (Builder $q, $min) => $q->where('amount', '>=', $min))
                            ->when($data['max'] ?? null, fn (Builder $q, $max) => $q->where('amount', '<=', $max));
                    }),
            ])
            ->recordActions([
                EditAction::make()->label('Düzenle'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('exportExcel')
                        ->label('Seçilenleri Excel\'e aktar')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function (Collection $records) {
                            if ($records->isEmpty()) {
                                Notification::make()
                                    ->title('Lütfen en az bir bağış seçin')
                                    ->warning()
                                    ->send();

                                return;
                            }

                            return DonationSpreadsheetExporter::download(
                                Donation::query()->whereIn('id', $records->pluck('id')),
                                'bagislar-secili-' . now()->format('Y-m-d_His') . '.xlsx',
                            );
                        }),
                    DeleteBulkAction::make()->label('Seçilenleri sil'),
                ]),
            ]);
    }
}
