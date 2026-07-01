<?php

namespace App\Filament\Crm\Resources\CrmProjects\Pages;

use App\Filament\Crm\Resources\CrmProjects\CrmProjectResource;
use App\Filament\Crm\Resources\CrmProjects\Schemas\CrmProjectForm;
use App\Models\Project;
use Filament\Resources\Pages\CreateRecord;

class CreateCrmProject extends CreateRecord
{
    protected static string $resource = CrmProjectResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['slug'] = CrmProjectForm::defaultSlug($data['title'] ?? 'proje');
        $data['status'] = 'devam-ediyor';
        $data['is_active'] = $data['is_active'] ?? true;

        if (! isset($data['sort_order']) || $data['sort_order'] === null) {
            $data['sort_order'] = ((int) Project::query()->max('sort_order')) + 1;
        }

        return $data;
    }
}
