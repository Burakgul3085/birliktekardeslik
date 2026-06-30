<?php

namespace App\Filament\Crm\Resources\PosterTemplates\Pages;

use App\Filament\Crm\Resources\PosterTemplates\PosterTemplateResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePosterTemplate extends CreateRecord
{
    protected static string $resource = PosterTemplateResource::class;

    protected function getRedirectUrl(): string
    {
        return DesignPosterTemplate::getUrl(['record' => $this->record]);
    }
}
