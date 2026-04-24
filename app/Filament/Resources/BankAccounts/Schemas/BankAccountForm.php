<?php

namespace App\Filament\Resources\BankAccounts\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BankAccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('recipient_name')->label('Hesap Adı')->required(),
            TextInput::make('bank_name')->label('Banka Adı')->required(),
            TextInput::make('branch_name')->label('Şube Adı'),
            TextInput::make('iban')->label('IBAN')->required()->unique(ignoreRecord: true),
            TextInput::make('account_number')->label('Hesap No'),
            FileUpload::make('qr_image')
                ->label('QR Görseli')
                ->image()
                ->disk('public')
                ->directory('bank-accounts/qr')
                ->imageEditor()
                ->maxSize(4096)
                ->helperText('Resmi Bilgiler sayfasında bu hesaba ait QR görseli olarak kullanılır.'),
            TextInput::make('currency')->label('Döviz')->default('TRY')->required(),
            TextInput::make('sort_order')->numeric()->default(0)->label('Sıralama'),
            Toggle::make('is_active')->default(true)->label('Aktif'),
        ])->columns(2);
    }
}
