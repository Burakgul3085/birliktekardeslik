<?php

namespace App\Filament\Crm\Resources\DonationTypes\Pages;

use App\Filament\Crm\Resources\DonationTypes\DonationTypeResource;
use App\Models\DonationType;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateDonationType extends CreateRecord
{
    protected static string $resource = DonationTypeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (blank($data['code'] ?? null) && filled($data['name'] ?? null)) {
            $data['code'] = $this->uniqueCode(Str::slug($data['name'], '_') ?: 'type');
        }

        if (! isset($data['sort_order']) || $data['sort_order'] === null) {
            $data['sort_order'] = ((int) DonationType::query()->max('sort_order')) + 1;
        }

        $data['is_active'] = $data['is_active'] ?? true;

        return $data;
    }

    private function uniqueCode(string $base): string
    {
        $code = $base;
        $suffix = 1;

        while (DonationType::query()->where('code', $code)->exists()) {
            $code = $base . '_' . $suffix;
            $suffix++;
        }

        return $code;
    }
}
