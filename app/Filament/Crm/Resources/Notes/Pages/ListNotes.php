<?php

namespace App\Filament\Crm\Resources\Notes\Pages;

use App\Filament\Crm\Resources\Notes\NoteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;

class ListNotes extends ListRecords
{
    protected static string $resource = NoteResource::class;

    public function getTable(): Table
    {
        return parent::getTable()
            ->persistFiltersInSession()
            ->persistSearchInSession()
            ->persistSortInSession();
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Yeni not'),
        ];
    }
}
