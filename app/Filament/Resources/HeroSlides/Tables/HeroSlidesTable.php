<?php

namespace App\Filament\Resources\HeroSlides\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HeroSlidesTable
{
    public static function configure(Table $table): Table
    {
        return $table->defaultSort('sort_order')
            ->columns([
                TextColumn::make('headline')->label('Slogan')->searchable(),
                TextColumn::make('sort_order')->label('Sira'),
                IconColumn::make('is_active')->boolean()->label('Aktif'),
            ])->recordActions([EditAction::make()])
              ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
