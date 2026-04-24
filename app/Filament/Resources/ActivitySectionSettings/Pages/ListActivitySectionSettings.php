<?php

namespace App\Filament\Resources\ActivitySectionSettings\Pages;

use App\Filament\Resources\ActivitySectionSettings\ActivitySectionSettingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListActivitySectionSettings extends ListRecords
{
    protected static string $resource = ActivitySectionSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
