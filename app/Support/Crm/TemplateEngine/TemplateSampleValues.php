<?php

namespace App\Support\Crm\TemplateEngine;

use App\Models\DocumentTemplate;

class TemplateSampleValues
{
    /**
     * @return array<string, string>
     */
    public static function forType(string $type): array
    {
        $thankYouBody = 'Kurban Bayramı vesilesiyle gerçekleştirdiğiniz bağışınız için teşekkür ederiz. '
            . 'Desteğiniz ihtiyaç sahiplerine ulaşmıştır. Destekleriniz için gönülden teşekkür ederiz.';

        $base = [
            'ad_soyad' => 'Sayın Açelya Zer',
            'tesekkur_metni' => $thankYouBody,
            'tesekkur_paragrafi' => $thankYouBody,
            'imza_ad_soyad' => TemplateFieldCatalog::SIGNATURE_NAME,
            'imza_unvan' => TemplateFieldCatalog::SIGNATURE_TITLE,
            'bagis_aciklamasi' => "AÇELYA'DAN OLMA KAAN ZER'İN ADAKLIK KURBANI SAĞLIKLA DOĞMASI VE ANNESİNİN DE SAĞLIKLI OLMASI ADINA ADAKLIK KURBAN",
            'bagis_turu' => 'YEMEKLİ BÜYÜK KOÇ',
            'tarih' => '01.06.2026',
            'bagis_no' => 'BGS-00001',
            'qr_code' => 'https://example.com/verify/SAMPLE',
        ];

        if ($type === DocumentTemplate::TYPE_DONATION_POSTER) {
            $base['ad_soyad'] = 'AÇELYA ZER & KAAN ZER';
        }

        return $base;
    }
}
