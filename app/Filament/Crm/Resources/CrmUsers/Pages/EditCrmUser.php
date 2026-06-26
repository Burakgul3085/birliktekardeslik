<?php

namespace App\Filament\Crm\Resources\CrmUsers\Pages;

use App\Filament\Crm\Resources\CrmUsers\CrmUserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCrmUser extends EditRecord
{
    protected static string $resource = CrmUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->label('Sil'),
        ];
    }
}
