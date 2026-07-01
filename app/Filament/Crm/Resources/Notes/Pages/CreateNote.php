<?php

namespace App\Filament\Crm\Resources\Notes\Pages;

use App\Filament\Crm\Resources\Notes\NoteResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateNote extends CreateRecord
{
    protected static string $resource = NoteResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['crm_user_id'] = auth('crm')->id();
        $data = $this->normalizeScopeRelations($data);

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function normalizeScopeRelations(array $data): array
    {
        return match ($data['scope'] ?? 'general') {
            'donor' => array_merge($data, [
                'donation_id' => null,
            ]),
            'donation' => array_merge($data, [
                'donor_id' => null,
            ]),
            default => array_merge($data, [
                'donor_id' => null,
                'donation_id' => null,
            ]),
        };
    }

    protected function beforeValidate(): void
    {
        $data = $this->form->getState();
        if (($data['scope'] ?? '') === 'general' && blank($data['title'] ?? null)) {
            throw ValidationException::withMessages([
                'title' => 'Genel notlar için başlık zorunludur.',
            ]);
        }
    }
}
