<?php

namespace App\Filament\Crm\Resources\Donors\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DonorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Bağışçı Profili')
                ->schema([
                    Grid::make(['default' => 1, 'md' => 2, 'lg' => 3])->schema([
                        TextEntry::make('full_name')->label('Ad Soyad'),
                        TextEntry::make('phone')->label('Telefon')->placeholder('-'),
                        TextEntry::make('email')->label('E-posta')->placeholder('-'),
                        TextEntry::make('city')->label('Şehir')->placeholder('-'),
                        TextEntry::make('country')->label('Ülke'),
                        TextEntry::make('created_at')->label('Kayıt Tarihi')->dateTime('d.m.Y H:i'),
                    ]),
                    TextEntry::make('address')->label('Adres')->placeholder('-')->columnSpanFull(),
                    TextEntry::make('notes')->label('Notlar')->placeholder('-')->columnSpanFull(),
                ]),
            Section::make('Bağış Özeti')
                ->schema([
                    Grid::make(['default' => 1, 'sm' => 2, 'lg' => 4])->schema([
                        TextEntry::make('total_donation_count')->label('Bağış Sayısı'),
                        TextEntry::make('total_donation_amount')
                            ->label('Toplam Tutar')
                            ->formatStateUsing(fn ($state): string => number_format((float) $state, 2, ',', '.') . ' TRY'),
                        TextEntry::make('first_donation_at')
                            ->label('İlk Bağış')
                            ->formatStateUsing(fn (?string $state): string => $state ? \Carbon\Carbon::parse($state)->format('d.m.Y H:i') : '-'),
                        TextEntry::make('last_donation_at')
                            ->label('Son Bağış')
                            ->formatStateUsing(fn (?string $state): string => $state ? \Carbon\Carbon::parse($state)->format('d.m.Y H:i') : '-'),
                    ]),
                ]),
            Section::make('Desteklenen Projeler')
                ->schema([
                    TextEntry::make('supported_projects_summary')
                        ->label('Projeler')
                        ->placeholder('Henüz proje bağışı yok')
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
