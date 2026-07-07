<?php

namespace App\Filament\Resources\ZakatSettings\Pages;

use App\Filament\Resources\ZakatSettings\ZakatSettingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateZakatSetting extends CreateRecord
{
    protected static string $resource = ZakatSettingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->normalizeI18n($data);
    }

    private function normalizeI18n(array $data): array
    {
        $data['intro_i18n'] = ['tr' => $data['intro_tr'] ?? null];
        $data['legal_text_i18n'] = ['tr' => $data['legal_tr'] ?? null];
        $data['faq_i18n'] = ['tr' => $data['faq_tr'] ?? []];

        unset($data['intro_tr'], $data['legal_tr'], $data['faq_tr']);

        return $data;
    }
}
