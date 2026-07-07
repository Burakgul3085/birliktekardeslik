<?php

namespace App\Filament\Resources\ZakatSettings\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ZakatSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nisap_grams')
                ->label('Nisap (gram)')
                ->numeric()
                ->required()
                ->default(80),
            TextInput::make('nisap_karat')
                ->label('Nisap ayarı')
                ->numeric()
                ->required()
                ->default(24),
            TextInput::make('rate')
                ->label('Zekât oranı (ör. 0.025)')
                ->numeric()
                ->required()
                ->default(0.025),
            Textarea::make('intro_tr')
                ->label('Giriş metni (TR)')
                ->rows(3)
                ->columnSpanFull(),
            Textarea::make('legal_tr')
                ->label('Yasal uyarı metni (TR)')
                ->rows(4)
                ->columnSpanFull(),
            Repeater::make('faq_tr')
                ->label('SSS (TR)')
                ->schema([
                    TextInput::make('question')->label('Soru')->required(),
                    Textarea::make('answer')->label('Cevap')->rows(3)->required(),
                ])
                ->columnSpanFull()
                ->defaultItems(0),
            Toggle::make('is_active')->label('Aktif')->default(true),
        ])->columns(3);
    }
}
