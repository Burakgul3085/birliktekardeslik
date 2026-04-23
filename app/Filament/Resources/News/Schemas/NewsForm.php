<?php

namespace App\Filament\Resources\News\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class NewsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->label('Başlık')->required(),
            RichEditor::make('content')->label('İçerik')->required()->columnSpanFull(),
            DateTimePicker::make('published_at')->label('Yayın Tarihi')->seconds(false),
            Toggle::make('is_active')->default(true)->label('Aktif'),
        ]);
    }
}
