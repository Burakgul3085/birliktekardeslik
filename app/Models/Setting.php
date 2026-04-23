<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'site_title',
        'site_description',
        'logo',
        'favicon',
        'phone',
        'email',
        'address',
        'website_url',
        'donation_page_url',
        'legal_kvkk_url',
        'legal_privacy_url',
        'legal_terms_url',
        'volunteer_preferences',
        'kvkk_text',
        'volunteer_clarification_text',
        'header_panel_volunteer_text',
        'social_section_title',
        'facebook_url',
        'instagram_url',
        'youtube_url',
        'tiktok_url',
        'x_url',
        'linkedin_url',
        'whatsapp_url',
        'telegram_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function current(): self
    {
        return static::query()->where('is_active', true)->latest('id')->first() ?? new self([
            'site_title' => 'Birlikte Kardeşlik Derneği',
            'site_description' => 'Birlikte iyiliği büyütüyoruz.',
            'volunteer_preferences' => "Sosyal Medya\nSaha Görevlisi\nGenel Gönüllü",
            'kvkk_text' => "Bu metin, 6698 sayılı Kişisel Verilerin Korunması Kanunu kapsamında gönüllü başvuru sürecinde toplanan kişisel verilerin işlenmesine ilişkin bilgilendirme amacı taşır.\n\nForm üzerinden paylaştığınız ad, soyad, e-posta, telefon ve başvuru içeriği; başvurunuzu değerlendirmek, sizinle iletişime geçmek, gönüllülük süreçlerini planlamak ve gerektiğinde yasal yükümlülükleri yerine getirmek amacıyla işlenir.\n\nKişisel verileriniz yalnızca yetkili dernek birimleri tarafından erişilebilir şekilde korunur, üçüncü kişilerle yalnızca hukuki zorunluluk veya açık rızanız bulunan hallerde paylaşılır.\n\nKVKK kapsamındaki erişim, düzeltme, silme, işleme itiraz ve benzeri taleplerinizi derneğimizin iletişim e-posta adresi üzerinden iletebilirsiniz.",
            'volunteer_clarification_text' => "Gönüllü başvuru formunu doldurarak paylaştığınız bilgilerin doğru ve güncel olduğunu kabul etmiş olursunuz.\n\nBaşvurunuz, dernek faaliyet alanları ve ihtiyaçları doğrultusunda değerlendirilir. Uygun görülen adaylarla e-posta veya telefon üzerinden iletişime geçilir.\n\nGönüllülük başvurusu bir istihdam taahhüdü niteliği taşımaz; başvuru sonucu, faaliyet takvimi ve kontenjan durumuna göre değişiklik gösterebilir.\n\nBaşvuru sürecinde paylaştığınız içerik yalnızca gönüllülük değerlendirmesi amacıyla kullanılır ve dernek gizlilik politikası çerçevesinde saklanır.",
            'header_panel_volunteer_text' => "Faaliyetlerimizde sizinle birlikte hareket etmek ister misiniz? Gönüllü olarak zamanınızı ve emeğinizi paylaşarak toplumsal faydaya katkı sağlayabilirsiniz. Başvuru formu üzerinden bize ulaşın, birlikte iyiliği büyütelim.",
            'social_section_title' => 'Sosyal medyada bizi takip edin',
        ]);
    }

    public function volunteerPreferenceOptions(): array
    {
        $raw = (string) ($this->volunteer_preferences ?? '');

        $items = collect(preg_split('/[\r\n,]+/', $raw))
            ->map(fn (string $item): string => trim($item))
            ->filter()
            ->values()
            ->all();

        if (empty($items)) {
            $items = ['Sosyal Medya', 'Saha Görevlisi', 'Genel Gönüllü'];
        }

        return $items;
    }
}
