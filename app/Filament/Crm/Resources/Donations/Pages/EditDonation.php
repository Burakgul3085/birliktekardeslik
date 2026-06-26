<?php

namespace App\Filament\Crm\Resources\Donations\Pages;

use App\Filament\Crm\Resources\Donations\DonationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDonation extends EditRecord
{
    protected static string $resource = DonationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->label('Sil'),
        ];
    }
}
