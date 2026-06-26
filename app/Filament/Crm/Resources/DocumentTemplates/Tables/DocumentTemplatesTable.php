<?php

namespace App\Filament\Crm\Resources\DocumentTemplates\Tables;

use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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
                TextColumn::make('name')->label('Şablon Adı')->searchable(),
                TextColumn::make('type_label')->label('Tür'),
                TextColumn::make('blade_view')->label('Görünüm')->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_default')->label('Varsayılan')->boolean(),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Düzenle')
                    ->form([
                        TextInput::make('name')->label('Şablon Adı')->required(),
                        Toggle::make('is_active')->label('Aktif'),
                        Toggle::make('is_default')->label('Varsayılan'),
                    ]),
            ]);
    }
}
