<?php

namespace App\Filament\Resources\ZakatSettings\Pages;

use App\Filament\Resources\ZakatSettings\ZakatSettingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListZakatSettings extends ListRecords
{
    protected static string $resource = ZakatSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
