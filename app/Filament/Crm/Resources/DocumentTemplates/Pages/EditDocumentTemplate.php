<?php

namespace App\Filament\Crm\Resources\DocumentTemplates\Pages;

use App\Filament\Crm\Resources\DocumentTemplates\DocumentTemplateResource;
use App\Support\Crm\DocumentTemplateDefaults;
use Filament\Resources\Pages\EditRecord;

class EditDocumentTemplate extends EditRecord
{
    protected static string $resource = DocumentTemplateResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $type = $data['type'] ?? $this->record->type;
        $data['settings'] = DocumentTemplateDefaults::mergeSettings($data['settings'] ?? null, $type);

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (! empty($data['background_image'])) {
            $data['blade_view'] = 'crm.documents.overlay';
        }

        $data['settings'] = DocumentTemplateDefaults::mergeSettings($data['settings'] ?? null, $data['type'] ?? $this->record->type);

        return $data;
    }
}
