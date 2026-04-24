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
                ->label('Slayt görseli')
                ->helperText('Her kayıt bir slayttır. Yalnızca görsel kullanılır; metin/buton gösterilmez. 5-6 görsel için 5-6 kayıt ekleyin. Maksimum 20MB (jpg/png/webp önerilir).'),
            TextInput::make('sort_order')->numeric()->default(0)->label('Sıralama'),
            Toggle::make('is_active')->default(true)->label('Aktif'),
        ]);
    }
}
