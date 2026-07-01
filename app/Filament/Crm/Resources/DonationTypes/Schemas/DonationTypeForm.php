<?php

namespace App\Filament\Crm\Resources\DonationTypes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class DonationTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(['default' => 1, 'lg' => 2])->schema([
                TextInput::make('name')
                    ->label('Bağış Türü Adı')
                    ->required()
                    ->maxLength(120)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set, string $operation): void {
                        if ($operation !== 'create') {
                            return;
                        }
                        $code = Str::slug($state ?? '', '_');
                        if ($code !== '') {
                            $set('code', $code);
                        }
                    }),
                TextInput::make('code')
                    ->label('Kod')
                    ->required()
                    ->maxLength(80)
                    ->unique(ignoreRecord: true)
                    ->helperText('Sistem içi benzersiz kod (otomatik üretilebilir).'),
                TextInput::make('sort_order')
                    ->label('Sıra')
                    ->numeric()
                    ->default(0)
                    ->minValue(0),
                Toggle::make('is_active')
                    ->label('Aktif (bağış formunda listelenir)')
                    ->default(true),
            ]),
        ]);
    }
}
