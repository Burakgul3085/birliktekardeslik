<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')
                ->label('Başlık')
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(function ($state, callable $set, callable $get): void {
                    if (filled($get('slug'))) {
                        return;
                    }
                    $set('slug', Str::slug((string) $state));
                }),
            TextInput::make('slug')
                ->label('Slug')
                ->required()
                ->unique(ignoreRecord: true),
            Textarea::make('description')->label('Kısa açıklama')->rows(3),
            TextInput::make('donation_amount')
                ->label('Bağış Tutarı')
                ->numeric()
                ->minValue(0)
                ->prefix('₺')
                ->placeholder('Örn: 1500'),
            TextInput::make('donation_currency')
                ->label('Para Birimi')
                ->default('TL')
                ->maxLength(10),
            Textarea::make('content')->label('Detay içerik')->rows(8)->columnSpanFull(),
            TextInput::make('detail_item_1_title')
                ->label('Detay açılır menü 1 başlık')
                ->default('Hızlı Müdahale'),
            Textarea::make('detail_item_1_text')
                ->label('Detay açılır menü 1 metin')
                ->rows(2)
                ->default('Kriz anlarında hızlı müdahale ederek ihtiyaç sahiplerine sıcak yemek ve temel gıda desteği sağlıyoruz.'),
            TextInput::make('detail_item_2_title')
                ->label('Detay açılır menü 2 başlık')
                ->default('Uzun Vadeli Çözümler'),
            Textarea::make('detail_item_2_text')
                ->label('Detay açılır menü 2 metin')
                ->rows(2)
                ->default('Bölgede kalıcı gıda güvenliği için sürdürülebilir dağıtım ve yerel işbirliği modelleri geliştiriyoruz.'),
            TextInput::make('detail_item_3_title')
                ->label('Detay açılır menü 3 başlık')
                ->default('Toplum Desteği'),
            Textarea::make('detail_item_3_text')
                ->label('Detay açılır menü 3 metin')
                ->rows(2)
                ->default('Ailelerin düzenli beslenme ihtiyacına katkı sağlayan insani yardım faaliyetleri yürütüyoruz.'),
            FileUpload::make('cover_image')
                ->disk('public')
                ->directory('projects')
                ->image()
                ->maxSize(51200)
                ->label('Kapak Gorseli')
                ->helperText('En fazla 50 MB.'),
            FileUpload::make('gallery_images')
                ->label('Faaliyet Galeri Fotoğrafları')
                ->disk('public')
                ->directory('projects/gallery-images')
                ->multiple()
                ->reorderable()
                ->image()
                ->imageEditor()
                ->maxSize(51200)
                ->helperText('Birden fazla fotoğraf ekleyebilirsiniz. Her dosya en fazla 50 MB.'),
            FileUpload::make('gallery_videos')
                ->label('Faaliyet Galeri Videoları')
                ->disk('public')
                ->directory('projects/gallery-videos')
                ->multiple()
                ->reorderable()
                ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/quicktime'])
                ->maxSize(51200)
                ->helperText('MP4/WEBM/MOV formatında birden fazla video ekleyebilirsiniz. Her dosya en fazla 50 MB.'),
            Select::make('status')->label('Durum')->options([
                'devam-ediyor' => 'Devam Ediyor',
                'tamamlandi' => 'Tamamlandı',
            ])->required(),
            TextInput::make('sort_order')->numeric()->default(0)->label('Sıralama'),
            Toggle::make('is_active')->default(true)->label('Aktif'),
        ])->columns(2);
    }
}
