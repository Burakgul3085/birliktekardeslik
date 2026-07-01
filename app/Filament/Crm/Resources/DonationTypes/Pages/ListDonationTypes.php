<?php

namespace App\Filament\Crm\Resources\DonationTypes\Pages;

use App\Filament\Crm\Resources\DonationTypes\DonationTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDonationTypes extends ListRecords
{
    protected static string $resource = DonationTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Yeni bağış türü'),
        ];
    }
}
