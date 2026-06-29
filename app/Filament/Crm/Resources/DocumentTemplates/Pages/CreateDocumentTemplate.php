<?php

namespace App\Filament\Crm\Resources\DocumentTemplates\Pages;

use App\Filament\Crm\Resources\DocumentTemplates\DocumentTemplateResource;
use App\Models\DocumentTemplate;
use Filament\Resources\Pages\CreateRecord;

class CreateDocumentTemplate extends CreateRecord
{
    protected static string $resource = DocumentTemplateResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $type = $data['type'] ?? DocumentTemplate::TYPE_DONATION_POSTER;

        $data['blade_view'] = 'template_engine';
        $data['sort_order'] = (int) DocumentTemplate::query()->where('type', $type)->max('sort_order') + 1;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        if (filled($this->record->background_image)) {
            return DocumentTemplateResource::getUrl('design', ['record' => $this->record]);
        }

        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
