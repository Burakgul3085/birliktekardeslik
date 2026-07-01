<?php

namespace App\Filament\Crm\Resources\CrmProjects\Pages;

use App\Filament\Crm\Resources\CrmProjects\CrmProjectResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCrmProjects extends ListRecords
{
    protected static string $resource = CrmProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Yeni proje / faaliyet'),
        ];
    }
}
