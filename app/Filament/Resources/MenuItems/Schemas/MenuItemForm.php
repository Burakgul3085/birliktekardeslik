<?php

namespace App\Filament\Resources\MenuItems\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MenuItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('label')->label('Başlık')->required(),
            TextInput::make('url')->label('Link')->required(),
            Toggle::make('open_in_new_tab')->label('Yeni Sekmede A?'),
            TextInput::make('sort_order')->numeric()->default(0)->label('Sıralama'),
            Select::make('footer_group')
                ->label('Footer sütunu')
                ->options([
                    'quick' => 'Hızlı erişim sütununda listele (üst menüde de görünsün)',
                    'activities' => 'Faaliyetlerimiz sütununda listele (üst menüde de görünsün)',
                ])
                ->placeholder('Sadece üst menü (footer sütunlarına ekleme)')
                ->helperText('Üst menüdeki tüm kayıtlar aynen görünür. Footer’da ayrıca “Hızlı erişim” veya “Faaliyetlerimiz” sütununda listelemek istediklerinizi seçin.')
                ->native(false),
            Toggle::make('is_active')->default(true)->label('Aktif'),
        ]);
    }
}
