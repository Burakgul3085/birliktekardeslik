<?php

namespace App\Filament\Crm\Resources\DocumentTemplates\Schemas;

use App\Models\DocumentTemplate;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DocumentTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Genel')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('name')
                            ->label('Şablon Adı')
                            ->required()
                            ->maxLength(120),
                        Select::make('type')
                            ->label('Belge Türü')
                            ->options(DocumentTemplate::ACTIVE_TYPES)
                            ->default(DocumentTemplate::TYPE_RECEIPT)
                            ->disabled()
                            ->dehydrated(),
                        Toggle::make('is_active')->label('Aktif')->default(true),
                        Toggle::make('is_default')->label('Varsayılan şablon'),
                    ]),
                ]),
        ]);
    }
}
