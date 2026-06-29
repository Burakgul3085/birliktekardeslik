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
        $data['type'] = DocumentTemplate::TYPE_RECEIPT;
        $data['blade_view'] = 'crm.documents.receipt';
        $data['sort_order'] = (int) DocumentTemplate::query()->where('type', DocumentTemplate::TYPE_RECEIPT)->max('sort_order') + 1;

        return $data;
    }
}
