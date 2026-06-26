<?php

namespace App\Filament\Crm\Resources\CrmUsers\Pages;

use App\Filament\Crm\Resources\CrmUsers\CrmUserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCrmUsers extends ListRecords
{
    protected static string $resource = CrmUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Yeni kullanıcı'),
        ];
    }
}
