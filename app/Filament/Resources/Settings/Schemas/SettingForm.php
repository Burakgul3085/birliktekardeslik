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
                Textarea::make('privacy_policy_text')
                    ->label('Gizlilik Politikası Metni')
                    ->rows(8)
                    ->columnSpanFull(),
                TextInput::make('home_focus_1_title')
                    ->label('Ana sayfa odak kartı 1 başlık')
                    ->default('Acil Gıda Desteği'),
                Textarea::make('home_focus_1_text')
                    ->label('Ana sayfa odak kartı 1 metin')
                    ->rows(3)
                    ->default('Afrika’da açlık riski altındaki ailelere temel gıda kolileri ulaştırıyoruz.'),
                TextInput::make('home_focus_2_title')
                    ->label('Ana sayfa odak kartı 2 başlık')
                    ->default('Temiz Su Erişimi'),
                Textarea::make('home_focus_2_text')
                    ->label('Ana sayfa odak kartı 2 metin')
                    ->rows(3)
                    ->default('Susuzlukla mücadele eden bölgelerde temiz suya erişimi destekliyoruz.'),
                TextInput::make('home_focus_3_title')
                    ->label('Ana sayfa odak kartı 3 başlık')
                    ->default('Beslenme Dayanışması'),
                Textarea::make('home_focus_3_text')
                    ->label('Ana sayfa odak kartı 3 metin')
                    ->rows(3)
                    ->default('Yemek ve içme suyu odağında düzenli insani yardım çalışmaları yürütüyoruz.'),
                TextInput::make('home_about_badge')
                    ->label('Biz Kimiz alanı rozet metni')
                    ->default('Birlikte Kardeşlik Derneği'),
                TextInput::make('home_about_title')
                    ->label('Biz Kimiz alanı başlık')
                    ->default('Biz Kimiz!'),
                Textarea::make('home_about_intro')
                    ->label('Biz Kimiz kısa giriş metni')
                    ->rows(3)
                    ->columnSpanFull(),
                Textarea::make('home_about_body')
                    ->label('Biz Kimiz açıklama metni')
                    ->rows(5)
                    ->columnSpanFull(),
                Textarea::make('home_about_items')
                    ->label('Biz Kimiz madde listesi')
                    ->rows(4)
                    ->helperText('Her satıra bir madde yazın.')
                    ->columnSpanFull(),
                TextInput::make('home_about_button_text')
                    ->label('Biz Kimiz buton metni')
                    ->default('Hakkımızda'),
                FileUpload::make('home_about_image')
                    ->image()
                    ->disk('public')
                    ->directory('settings')
                    ->label('Biz Kimiz görseli (kare önerilir)'),
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
        ]);
    }
}
