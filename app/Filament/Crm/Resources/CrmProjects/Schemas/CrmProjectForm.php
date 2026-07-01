<?php

namespace App\Filament\Crm\Resources\CrmProjects\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CrmProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(['default' => 1, 'lg' => 2])->schema([
                TextInput::make('title')
                    ->label('Proje / Faaliyet Adı')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                TextInput::make('sort_order')
                    ->label('Sıra')
                    ->numeric()
                    ->default(0)
                    ->minValue(0),
                Toggle::make('is_active')
                    ->label('Aktif (bağış formunda listelenir)')
                    ->default(true),
            ]),
        ]);
    }

    public static function defaultSlug(string $title): string
    {
        $slug = Str::slug($title);
        $baseSlug = $slug !== '' ? $slug : 'proje';
        $suffix = 1;

        while (\App\Models\Project::query()->where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }
}
