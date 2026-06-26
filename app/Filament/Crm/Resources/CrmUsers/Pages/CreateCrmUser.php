<?php

namespace App\Filament\Crm\Resources\CrmUsers\Pages;

use App\Filament\Crm\Resources\CrmUsers\CrmUserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCrmUser extends CreateRecord
{
    protected static string $resource = CrmUserResource::class;
}
