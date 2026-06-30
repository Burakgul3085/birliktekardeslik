<?php

namespace App\Support\Crm;

use App\Models\Donation;
use App\Models\PosterTemplate;
use App\Models\Setting;

/**
 * Bir bağıştan afiş şablonlarında kullanılacak yer tutucu (placeholder) verisini üretir.
 */
class PosterDataResolver
{
    /**
     * Şablon tasarımcısında listelenecek kullanılabilir yer tutucular.
     *
     * @return array<string, string>
     */
    public static function availablePlaceholders(): array
    {
        return [
            'ad' => 'Bağışçı adı',
            'soyad' => 'Bağışçı soyadı',
            'ad_soyad' => 'Bağışçı ad soyad',
            'telefon' => 'Telefon',
            'tarih' => 'Bağış tarihi',
            'faaliyet' => 'Proje / Faaliyet',
            'bagis_turu' => 'Bağış türü',
            'bagis_tutari' => 'Bağış tutarı',
            'para_birimi' => 'Para birimi',
            'tutar_birimli' => 'Tutar + para birimi',
            'bagis_no' => 'Bağış no',
            'makbuz_no' => 'Makbuz no',
            'not' => 'Bağış notu (açıklama)',
            'tesekkur_metni' => 'Teşekkür metni (otomatik)',
            'dernek_adi' => 'Dernek adı',
        ];
    }

    /**
     * Bağış verisinden tüm yer tutucu değerlerini döndürür.
     *
     * @return array<string, string>
     */
    public function resolve(Donation $donation, ?PosterTemplate $template = null): array
    {
        $donation->loadMissing(['donor', 'donationType', 'project']);

        $settings = Setting::current();

        $amount = number_format((float) $donation->amount, 2, ',', '.');
        $currency = (string) ($donation->currency ?? 'TRY');
        $date = $donation->donated_at?->format('d.m.Y') ?? now()->format('d.m.Y');
        $faaliyet = $donation->project?->title ?? ($donation->donationType?->name ?? '');

        $data = [
            'ad' => $donation->donor?->first_name ?? '',
            'soyad' => $donation->donor?->last_name ?? '',
            'ad_soyad' => $donation->donor?->full_name ?? '',
            'telefon' => $donation->donor?->phone ?? '',
            'tarih' => $date,
            'faaliyet' => $faaliyet,
            'bagis_turu' => $donation->donationType?->name ?? '',
            'bagis_tutari' => $amount,
            'para_birimi' => $currency,
            'tutar_birimli' => trim($amount . ' ' . $currency),
            'bagis_no' => (string) ($donation->donation_number ?? ''),
            'makbuz_no' => (string) ($donation->receipt_number ?? $donation->donation_number ?? ''),
            'not' => (string) ($donation->description ?? ''),
            'dernek_adi' => (string) ($settings->site_title ?? 'Birlikte Kardeşlik Derneği'),
        ];

        $data['tesekkur_metni'] = $this->buildThanksText($template, $data);

        return $data;
    }

    /**
     * Teşekkür metnini şablondaki kalıptan veya varsayılandan üretir.
     *
     * @param  array<string, string>  $data
     */
    private function buildThanksText(?PosterTemplate $template, array $data): string
    {
        $tpl = $template?->thanks_text_template;

        if (! is_string($tpl) || trim($tpl) === '') {
            $tpl = $this->defaultThanksTemplate();
        }

        return $this->fill($tpl, $data);
    }

    public function defaultThanksTemplate(): string
    {
        return "Yapmış olduğunuz {faaliyet} bağışıyla, ihtiyaç sahibi ailelerin yüzünde bir tebessüme vesile oldunuz.\n\n"
            . "Gösterdiğiniz duyarlılık, umut, bereket ve kardeşlik köprülerinin daha da güçlenmesine katkı sağladı.\n\n"
            . "Destekleriniz için gönülden teşekkür ederiz.";
    }

    /**
     * Metindeki {anahtar} kalıplarını verilerle değiştirir.
     *
     * @param  array<string, string>  $data
     */
    public function fill(string $text, array $data): string
    {
        return preg_replace_callback('/\{(\w+)\}/', function (array $matches) use ($data): string {
            return $data[$matches[1]] ?? $matches[0];
        }, $text) ?? $text;
    }
}
