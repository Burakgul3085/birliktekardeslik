<?php

namespace App\Filament\Crm\Resources\Donors\Pages;

use App\Filament\Crm\Resources\Donors\DonorResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditDonor extends EditRecord
{
    protected static string $resource = DonorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()->label('Profil'),
            DeleteAction::make()->label('Sil'),
        ];
    }
}
