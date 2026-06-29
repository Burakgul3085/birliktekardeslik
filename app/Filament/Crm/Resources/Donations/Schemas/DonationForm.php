<?php

namespace App\Filament\Crm\Resources\Donations\Schemas;

use App\Models\DonationType;
use App\Models\Donor;
use App\Models\PaymentMethod;
use App\Models\Project;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class DonationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(2)->schema([
                Select::make('donor_id')
                    ->label('Bağışçı')
                    ->relationship('donor', 'first_name')
                    ->getOptionLabelFromRecordUsing(fn (Donor $record): string => $record->full_name . ($record->phone ? ' · ' . $record->phone : ''))
                    ->searchable(['first_name', 'last_name', 'phone', 'email'])
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        TextInput::make('first_name')->label('Ad')->required(),
                        TextInput::make('last_name')->label('Soyad')->required(),
                        TextInput::make('phone')->label('Telefon'),
                        TextInput::make('email')->label('E-posta')->email(),
                    ]),
                Select::make('donation_type_id')
                    ->label('Bağış Türü')
                    ->options(fn (): array => DonationType::query()->where('is_active', true)->orderBy('sort_order')->pluck('name', 'id')->all())
                    ->searchable()
                    ->preload(),
                Select::make('payment_method_id')
                    ->label('Ödeme Türü')
                    ->options(fn (): array => PaymentMethod::query()->where('is_active', true)->orderBy('sort_order')->pluck('name', 'id')->all())
                    ->searchable()
                    ->preload(),
                Select::make('project_id')
                    ->label('Proje / Faaliyet')
                    ->options(fn (): array => Project::query()->orderBy('title')->pluck('title', 'id')->all())
                    ->searchable()
                    ->preload(),
                TextInput::make('amount')
                    ->label('Tutar')
                    ->numeric()
                    ->required()
                    ->minValue(0.01)
                    ->step(0.01),
                Select::make('currency')
                    ->label('Para Birimi')
                    ->options([
                        'TRY' => 'TRY',
                        'USD' => 'USD',
                        'EUR' => 'EUR',
                    ])
                    ->default('TRY')
                    ->required(),
                DateTimePicker::make('donated_at')
                    ->label('Bağış Tarihi')
                    ->default(now())
                    ->required(),
                TextInput::make('poster_name')
                    ->label('Afişte Görünecek İsim')
                    ->maxLength(255)
                    ->placeholder('Boş bırakılırsa bağışçı adı kullanılır')
                    ->helperText('Çift isim: Açelya Zer & Kaan Zer'),
                TextInput::make('donation_number')
                    ->label('Bağış No')
                    ->disabled()
                    ->dehydrated(false)
                    ->visibleOn('edit'),
                TextInput::make('receipt_number')
                    ->label('Makbuz No')
                    ->maxLength(120)
                    ->unique(ignoreRecord: true),
            ]),
            Textarea::make('description')
                ->label('Bağış Afişi Notu')
                ->rows(3)
                ->helperText('Bağış afişinde görünecek açıklama metni.')
                ->columnSpanFull(),
            Textarea::make('notes')
                ->label('Not')
                ->rows(2)
                ->columnSpanFull(),
        ]);
    }
}
