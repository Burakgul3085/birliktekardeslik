<?php

namespace App\Filament\Crm\Resources\Donors\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class DonorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(2)->schema([
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
                    ->unique(ignoreRecord: true),
                TextInput::make('email')
                    ->label('E-posta')
                    ->email()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
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
}
