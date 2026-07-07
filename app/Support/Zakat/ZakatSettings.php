<?php

namespace App\Support\Zakat;

use App\Models\ZakatSetting;

class ZakatSettings
{
    public static function forPage(): array
    {
        $record = ZakatSetting::current();

        return [
            'nisap_grams' => (float) ($record->nisap_grams ?? config('zakat.nisap_grams', 80)),
            'nisap_karat' => (int) ($record->nisap_karat ?? config('zakat.nisap_karat', 24)),
            'zakat_rate' => (float) ($record->rate ?? config('zakat.rate', 0.025)),
            'karat_factors' => config('zakat.karat_factors', []),
            'intro' => $record->localized('intro') ?? __('app.zakat.intro'),
            'legal_text' => $record->localized('legal_text') ?? __('app.zakat.legal_text'),
            'faq' => $record->localizedFaq(),
        ];
    }
}
