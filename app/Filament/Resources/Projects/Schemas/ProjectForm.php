<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->label('Başlık')->required(),
            Textarea::make('description')->label('Aciklama')->rows(4),
            FileUpload::make('cover_image')->disk('public')->directory('projects')->image()->label('Kapak Gorseli'),
            Select::make('status')->label('Durum')->options([
                'devam-ediyor' => 'Devam Ediyor',
                'tamamlandi' => 'Tamamlandı',
            ])->required(),
            TextInput::make('sort_order')->numeric()->default(0)->label('Sıralama'),
            Toggle::make('is_active')->default(true)->label('Aktif'),
        ]);
    }
}
