<?php

namespace App\Filament\Resources\News\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class NewsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->label('Başlık')->required(),
            FileUpload::make('cover_image')
                ->label('Haber kapak görseli')
                ->disk('public')
                ->directory('news')
                ->image()
                ->imageEditor()
                ->helperText('Öneri: 1200x700 px, JPG/PNG/WebP')
                ->columnSpanFull(),
            Textarea::make('summary')
                ->label('Kısa özet')
                ->rows(3)
                ->helperText('Ana sayfada kart içinde kısa açıklama olarak görünür.')
                ->columnSpanFull(),
            RichEditor::make('content')->label('İçerik')->required()->columnSpanFull(),
            DateTimePicker::make('published_at')->label('Yayın Tarihi')->seconds(false),
            Toggle::make('is_active')->default(true)->label('Aktif'),
        ])->columns(2);
    }
}
