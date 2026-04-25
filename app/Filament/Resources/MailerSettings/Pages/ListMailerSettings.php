<?php

namespace App\Filament\Resources\MailerSettings\Pages;

use App\Filament\Resources\MailerSettings\MailerSettingResource;
use Filament\Resources\Pages\ListRecords;

class ListMailerSettings extends ListRecords
{
    protected static string $resource = MailerSettingResource::class;
}
