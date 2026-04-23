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
            ])->columns(2),
            Section::make('Sosyal Medya')->schema([
                TextInput::make('facebook_url')->url()->label('Facebook'),
                TextInput::make('instagram_url')->url()->label('Instagram'),
                TextInput::make('youtube_url')->url()->label('YouTube'),
                TextInput::make('tiktok_url')->url()->label('TikTok'),
                TextInput::make('x_url')->url()->label('X'),
                Toggle::make('is_active')->default(true)->label('Aktif'),
            ])->columns(2),
        ]);
    }
}
