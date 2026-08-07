<?php

namespace App\Filament\Crm\Resources\Donors\Schemas;

use App\Models\Donor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class DonorForm
{
    /* Uyarı notunda en fazla kaç bağışçı adı listelenecek */
    protected const HINT_LIMIT = 3;

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(['default' => 1, 'lg' => 2])->schema([
                TextInput::make('first_name')
                    ->label('Ad')
                    ->required()
                    ->maxLength(120),
                TextInput::make('last_name')
                    ->label('Soyad')
                    ->required()
                    ->maxLength(120),
                TextInput::make('phone')
                    ->label('Telefon')
                    ->tel()
                    ->maxLength(30)
                    ->live(onBlur: true)
                    ->helperText(fn (?string $state, ?Model $record): ?HtmlString => self::duplicateHint('phone', $state, $record)),
                TextInput::make('email')
                    ->label('E-posta')
                    ->email()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->helperText(fn (?string $state, ?Model $record): ?HtmlString => self::duplicateHint('email', $state, $record)),
                TextInput::make('city')
                    ->label('Şehir')
                    ->maxLength(120),
                TextInput::make('country')
                    ->label('Ülke')
                    ->default('Türkiye')
                    ->maxLength(120),
            ]),
            Textarea::make('address')
                ->label('Adres')
                ->rows(3)
                ->columnSpanFull(),
            Textarea::make('notes')
                ->label('Notlar')
                ->rows(3)
                ->columnSpanFull(),
        ]);
    }

    /*
     * Aynı telefon veya e-posta ile kayıtlı bağışçıları bildirir.
     * Yalnızca bilgilendirir; kaydetmeyi engellemez.
     */
    protected static function duplicateHint(string $column, ?string $state, ?Model $record): ?HtmlString
    {
        $value = trim((string) $state);

        if ($value === '') {
            return null;
        }

        $matches = Donor::query()
            ->where($column, $value)
            ->when($record?->exists, fn ($query) => $query->whereKeyNot($record->getKey()))
            ->orderBy('id')
            ->limit(self::HINT_LIMIT + 1)
            ->get(['id', 'first_name', 'last_name']);

        if ($matches->isEmpty()) {
            return null;
        }

        $names = $matches
            ->take(self::HINT_LIMIT)
            ->map(fn (Donor $donor): string => e($donor->full_name))
            ->implode(', ');

        $suffix = $matches->count() > self::HINT_LIMIT ? ' ve diğerleri' : '';

        return new HtmlString(
            '<span class="font-medium text-warning-600 dark:text-warning-400">'
            . 'Bu bilgi şu bağışçılarda da kayıtlı: ' . $names . $suffix
            . '. Kayıt engellenmez.</span>'
        );
    }
}
