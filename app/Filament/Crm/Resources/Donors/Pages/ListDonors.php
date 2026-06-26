<?php

namespace App\Filament\Crm\Resources\Donors\Pages;

use App\Filament\Crm\Resources\Donors\DonorResource;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ListRecords;

class ListDonors extends ListRecords
{
    protected static string $resource = DonorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Yeni bağışçı'),
        ];
    }
}
