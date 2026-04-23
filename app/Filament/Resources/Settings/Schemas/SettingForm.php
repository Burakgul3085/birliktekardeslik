<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Genel Ayarlar')->schema([
                TextInput::make('site_title')->label('Site Başlığı')->required(),
                Textarea::make('site_description')->label('Açıklama')->rows(3),
                FileUpload::make('logo')->image()->disk('public')->directory('settings')->label('Logo'),
                FileUpload::make('favicon')->image()->disk('public')->directory('settings')->label('Favicon'),
                TextInput::make('phone')->label('Telefon'),
                TextInput::make('email')->email()->label('E-posta'),
                TextInput::make('address')->label('Adres'),
                TextInput::make('website_url')->url()->label('Web Site Linki'),
                TextInput::make('donation_page_url')
                    ->url()
                    ->label('Bağış Sayfası URL')
                    ->helperText('Boş bırakırsanız sistem otomatik olarak mevcut domaindeki /bagis-yap adresini kullanır.'),
                Textarea::make('volunteer_preferences')
                    ->label('Gönüllülük Tercihleri')
                    ->rows(4)
                    ->helperText('Her satıra bir tercih yazın. Örn: Sosyal Medya, Saha Görevlisi, Genel Gönüllü')
                    ->default("Sosyal Medya\nSaha Görevlisi\nGenel Gönüllü"),
                Textarea::make('kvkk_text')
                    ->label('KVKK Aydınlatma Metni')
                    ->rows(8)
                    ->columnSpanFull(),
                Textarea::make('volunteer_clarification_text')
                    ->label('Gönüllü Başvuru Aydınlatma Metni')
                    ->rows(8)
                    ->columnSpanFull(),
                Textarea::make('header_panel_volunteer_text')
                    ->label('Header hızlı panel: Gönüllü alanı metni')
                    ->rows(4)
                    ->helperText('Ana menüdeki kare ikonundan açılan panelde, gönüllülük bölümünde gösterilir.')
                    ->columnSpanFull(),
            ])->columns(2),
            Section::make('Sosyal Medya')->schema([
                TextInput::make('social_section_title')
                    ->label('Sosyal medya bölümü başlığı')
                    ->helperText('Header hızlı panelin alt kısmındaki başlık (boşsa varsayılan metin kullanılır).'),
                TextInput::make('facebook_url')->url()->label('Facebook'),
                TextInput::make('instagram_url')->url()->label('Instagram'),
                TextInput::make('youtube_url')->url()->label('YouTube'),
                TextInput::make('tiktok_url')->url()->label('TikTok'),
                TextInput::make('x_url')->url()->label('X'),
                TextInput::make('linkedin_url')->url()->label('LinkedIn'),
                TextInput::make('whatsapp_url')->url()->label('WhatsApp (tam link, örn. https://wa.me/9053...)'),
                TextInput::make('telegram_url')->url()->label('Telegram (tam link)'),
                Toggle::make('is_active')->default(true)->label('Aktif'),
            ])->columns(2),
            Section::make('Yasal bağlantılar (footer)')->schema([
                TextInput::make('legal_kvkk_url')
                    ->url()
                    ->label('KVKK sayfası URL')
                    ->helperText('Boş bırakılırsa bu bağlantı footer’da gizlenir.'),
                TextInput::make('legal_privacy_url')
                    ->url()
                    ->label('Gizlilik politikası URL'),
                TextInput::make('legal_terms_url')
                    ->url()
                    ->label('Şartlar ve koşullar URL'),
            ])->columns(2),
        ]);
    }
}
