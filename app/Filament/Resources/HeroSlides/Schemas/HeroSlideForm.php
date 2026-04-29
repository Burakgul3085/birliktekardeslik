<?php

namespace App\Filament\Resources\HeroSlides\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class HeroSlideForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Hidden::make('headline')
                ->default('Hero Slayt')
                ->dehydrated(true),
            FileUpload::make('image_path')
                ->disk('public')
                ->directory('hero')
                ->rules(['required', 'file', 'max:20480'])
                ->required()
                ->label('Masaüstü görseli')
                ->helperText('Desktop için önerilen boyut: 1920x900 px (16:9). Her kayıt bir slayttır, maksimum 20MB.'),
            FileUpload::make('image_path_tablet')
                ->disk('public')
                ->directory('hero')
                ->rules(['nullable', 'file', 'max:20480'])
                ->label('Tablet görseli')
                ->helperText('Tablet için önerilen boyut: 1400x1000 px (yaklaşık 4:3). Boş bırakılırsa masaüstü görseli kullanılır.'),
            FileUpload::make('image_path_mobile')
                ->disk('public')
                ->directory('hero')
                ->rules(['nullable', 'file', 'max:20480'])
                ->label('Telefon görseli')
                ->helperText('Mobil için önerilen boyut: 1080x1350 px (4:5). Boş bırakılırsa tablet/masaüstü görseli kullanılır.'),
            TextInput::make('sort_order')->numeric()->default(0)->label('Sıralama'),
            Toggle::make('is_active')->default(true)->label('Aktif'),
        ]);
    }
}
