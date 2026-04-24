<?php

namespace App\Filament\Resources\MenuItems\Schemas;

use App\Models\MenuItem;
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
            Select::make('parent_id')
                ->label('Üst menü')
                ->options(fn () => MenuItem::query()->whereNull('parent_id')->orderBy('sort_order')->pluck('label', 'id'))
                ->searchable()
                ->placeholder('Ana başlık (üst seviye)')
                ->helperText('Seçerseniz bu kayıt alt başlık olur; seçmezseniz üst başlık olur.'),
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
