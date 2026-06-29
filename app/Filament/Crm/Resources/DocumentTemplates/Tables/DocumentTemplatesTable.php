<?php

namespace App\Filament\Crm\Resources\DocumentTemplates\Tables;

use App\Filament\Crm\Resources\DocumentTemplates\DocumentTemplateResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DocumentTemplatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                ImageColumn::make('background_image')
                    ->label('Önizleme')
                    ->disk('public')
                    ->square()
                    ->defaultImageUrl(fn (): string => asset('images/default-logo.svg')),
                TextColumn::make('name')->label('Şablon Adı')->searchable()->wrap(),
                TextColumn::make('type_label')->label('Tür'),
                IconColumn::make('is_default')->label('Varsayılan')->boolean(),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
            ])
            ->recordActions([
                Action::make('design')
                    ->label('Düzenleyici')
                    ->icon('heroicon-o-cursor-arrow-rays')
                    ->url(fn ($record): string => DocumentTemplateResource::getUrl('design', ['record' => $record]))
                    ->visible(fn ($record): bool => filled($record->background_image)),
                EditAction::make()->label('Düzenle'),
            ]);
    }
}
