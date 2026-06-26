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

        $data['blade_view'] = match ($type) {
            DocumentTemplate::TYPE_RECEIPT => 'crm.documents.receipt',
            DocumentTemplate::TYPE_DONATION_POSTER => 'crm.documents.donation-poster',
            DocumentTemplate::TYPE_THANKS_POSTER => 'crm.documents.thanks-poster-overlay',
            default => 'crm.documents.receipt',
        };

        $data['sort_order'] = (int) DocumentTemplate::query()->where('type', $type)->max('sort_order') + 1;

        return $data;
    }
}
