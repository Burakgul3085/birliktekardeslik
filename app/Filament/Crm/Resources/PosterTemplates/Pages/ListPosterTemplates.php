<?php

namespace App\Filament\Crm\Resources\PosterTemplates\Pages;

use App\Filament\Crm\Resources\PosterTemplates\PosterTemplateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPosterTemplates extends ListRecords
{
    protected static string $resource = PosterTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Yeni afiş şablonu'),
        ];
    }
}
