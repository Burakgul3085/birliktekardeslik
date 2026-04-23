<?php

namespace App\Filament\Resources\HeroSlides\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class HeroSlideForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('headline')->label('Slogan')->required(),
            Textarea::make('subtext')->label('Alt Metin')->rows(3),
            FileUpload::make('image_path')->disk('public')->directory('hero')->image()->label('Gorsel'),
            TextInput::make('button_text')->label('Buton Metni'),
            TextInput::make('button_url')->label('Buton Linki')->url(),
            TextInput::make('sort_order')->numeric()->default(0)->label('Sıralama'),
            Toggle::make('is_active')->default(true)->label('Aktif'),
        ]);
    }
}
