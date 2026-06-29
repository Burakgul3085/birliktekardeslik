<?php

namespace App\Filament\Crm\Resources\DocumentTemplates\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DocumentTemplatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('name')->label('Şablon Adı')->searchable()->wrap(),
                TextColumn::make('type_label')->label('Tür'),
                IconColumn::make('is_default')->label('Varsayılan')->boolean(),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
            ])
            ->recordActions([
                EditAction::make()->label('Düzenle'),
            ]);
    }
}
