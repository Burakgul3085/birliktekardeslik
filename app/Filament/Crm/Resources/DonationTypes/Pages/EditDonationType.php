<?php

namespace App\Filament\Crm\Resources\DonationTypes\Pages;

use App\Filament\Crm\Resources\DonationTypes\DonationTypeResource;
use Filament\Resources\Pages\EditRecord;

class EditDonationType extends EditRecord
{
    protected static string $resource = DonationTypeResource::class;
}
