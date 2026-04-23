<?php

namespace App\Filament\Resources\News\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NewsTable
{
    public static function configure(Table $table): Table
    {
        return $table->defaultSort('published_at', 'desc')
            ->columns([
                TextColumn::make('title')->searchable()->label('Başlık'),
                TextColumn::make('published_at')->dateTime('d.m.Y H:i')->label('Yayın Tarihi'),
                IconColumn::make('is_active')->boolean()->label('Aktif'),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
