<?php

namespace App\Filament\Resources\BankAccounts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BankAccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('bank_name')->label('Banka Adi')->required(),
            TextInput::make('recipient_name')->label('Alıcı Adi')->required(),
            TextInput::make('iban')->label('IBAN')->required()->unique(ignoreRecord: true),
            TextInput::make('currency')->label('Döviz')->default('TRY')->required(),
            TextInput::make('sort_order')->numeric()->default(0)->label('Sıralama'),
            Toggle::make('is_active')->default(true)->label('Aktif'),
        ]);
    }
}
