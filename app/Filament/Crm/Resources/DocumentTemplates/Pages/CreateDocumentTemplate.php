<?php

namespace App\Filament\Crm\Resources\DocumentTemplates\Pages;

use App\Filament\Crm\Resources\DocumentTemplates\DocumentTemplateResource;
use App\Models\DocumentTemplate;
use App\Support\Crm\DocumentTemplateDefaults;
use Filament\Resources\Pages\CreateRecord;

class CreateDocumentTemplate extends CreateRecord
{
    protected static string $resource = DocumentTemplateResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $type = $data['type'] ?? DocumentTemplate::TYPE_THANKS_POSTER;
        $data['blade_view'] = ! empty($data['background_image']) ? 'crm.documents.overlay' : 'crm.documents.thanks-poster';
        $data['settings'] = DocumentTemplateDefaults::mergeSettings($data['settings'] ?? null, $type);
        $data['sort_order'] = (int) DocumentTemplate::query()->where('type', $type)->max('sort_order') + 1;

        return $data;
    }
}
