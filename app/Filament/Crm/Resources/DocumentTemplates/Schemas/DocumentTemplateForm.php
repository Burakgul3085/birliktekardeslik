<?php

namespace App\Filament\Crm\Resources\DocumentTemplates\Schemas;

use App\Models\DocumentTemplate;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DocumentTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Genel')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('name')
                            ->label('Şablon Adı')
                            ->required()
                            ->maxLength(120),
                        Select::make('type')
                            ->label('Afiş Türü')
                            ->options([
                                DocumentTemplate::TYPE_DONATION_POSTER => 'Bağış Afişi',
                                DocumentTemplate::TYPE_THANKS_POSTER => 'Teşekkür Afişi',
                            ])
                            ->disabled(fn (?DocumentTemplate $record): bool => $record !== null)
                            ->dehydrated()
                            ->required(),
                        Toggle::make('is_active')->label('Aktif')->default(true),
                        Toggle::make('is_default')->label('Varsayılan şablon'),
                    ]),
                ]),
            Section::make('Boş Şablon Görseli')
                ->description('Metin alanları boş olan orijinal PNG dosyasını yükleyin. Kaydettikten sonra otomatik olarak Şablon Düzenleyici açılır — alanları hizalayıp Kaydet\'e basın.')
                ->schema([
                    FileUpload::make('background_image')
                        ->label('Boş afiş (PNG/JPG)')
                        ->disk('public')
                        ->directory('crm/templates')
                        ->image()
                        ->imageEditor()
                        ->required()
                        ->helperText('Dolu örnek değil, boş şablon yükleyin. Sistem ilk kurulumda geçici boş şablon üretir; kendi tasarımınızla değiştirebilirsiniz.'),
                ]),
            Section::make('Teşekkür Metni Şablonu')
                ->description('Boş bırakılırsa otomatik metin kullanılır. Placeholder: {ad_soyad}, {tutar}, {para_birimi}, {bagis_turu}, {tarih}, {bagis_no}')
                ->visible(fn (?DocumentTemplate $record): bool => $record?->type === DocumentTemplate::TYPE_THANKS_POSTER || request()->input('type') === DocumentTemplate::TYPE_THANKS_POSTER)
                ->schema([
                    Textarea::make('message_template')
                        ->label('Özel teşekkür metni')
                        ->rows(5)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
