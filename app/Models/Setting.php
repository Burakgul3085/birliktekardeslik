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
        'google_maps_embed_url',
        'website_url',
        'donation_page_url',
        'legal_kvkk_url',
        'legal_privacy_url',
        'legal_terms_url',
        'volunteer_preferences',
        'kvkk_text',
        'volunteer_clarification_text',
        'privacy_policy_text',
        'home_focus_1_title',
        'home_focus_1_text',
        'home_focus_2_title',
        'home_focus_2_text',
        'home_focus_3_title',
        'home_focus_3_text',
        'home_about_badge',
        'home_about_title',
        'home_about_intro',
        'home_about_body',
        'home_about_items',
        'home_about_button_text',
        'home_about_image',
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
            'privacy_policy_text' => "Bu gizlilik politikası, derneğimizin web sitesi üzerinden toplanan kişisel verilerin hangi amaçlarla işlendiğini, nasıl korunduğunu ve ilgili kişilerin haklarını açıklar.\n\nToplanan veriler; iletişim taleplerini yanıtlamak, gönüllü başvurularını değerlendirmek, e-bülten gönderimlerini yönetmek ve yasal yükümlülükleri yerine getirmek amacıyla kullanılabilir.\n\nKişisel veriler, yalnızca yetkili kişiler tarafından erişilebilir şekilde korunur; açık rıza veya yasal zorunluluk bulunmadıkça üçüncü kişilerle paylaşılmaz.\n\nKişisel verilerinizle ilgili taleplerinizi derneğimizin iletişim e-posta adresi üzerinden bize iletebilirsiniz.",
            'home_focus_1_title' => 'Acil Gıda Desteği',
            'home_focus_1_text' => 'Afrika’da açlık riski altındaki ailelere temel gıda kolileri ulaştırıyoruz.',
            'home_focus_2_title' => 'Temiz Su Erişimi',
            'home_focus_2_text' => 'Susuzlukla mücadele eden bölgelerde temiz suya erişimi destekliyoruz.',
            'home_focus_3_title' => 'Beslenme Dayanışması',
            'home_focus_3_text' => 'Yemek ve içme suyu odağında düzenli insani yardım çalışmaları yürütüyoruz.',
            'home_about_badge' => 'Birlikte Kardeşlik Derneği',
            'home_about_title' => 'Biz Kimiz!',
            'home_about_intro' => 'Afrika\'da açlık ve susuzlukla mücadele eden kardeşlerimize gıda ve temiz su desteği sağlıyoruz.',
            'home_about_body' => 'Derneğimiz, Afrika bölgesinde yeme-içme ve temel insani ihtiyaçlar odağında çalışan gönüllü bir dayanışma hareketidir. Hedefimiz; sürdürülebilir gıda desteği, temiz suya erişim ve yerel topluluklarla güçlü bir yardımlaşma ağı kurmaktır.',
            'home_about_items' => "Acil gıda kolisi dağıtımları\nTemiz su erişimi ve hijyen desteği\nYerel mutfak ve yemek desteği\nSürdürülebilir beslenme projeleri",
            'home_about_button_text' => 'Hakkımızda',
            'header_panel_volunteer_text' => 'Faaliyetlerimizde sizinle birlikte hareket etmek ister misiniz? Gönüllü olarak zamanınızı ve emeğinizi paylaşarak toplumsal faydaya katkı sağlayabilirsiniz. Başvuru formu üzerinden bize ulaşın, birlikte iyiliği büyütelim.',
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
