<?php

namespace App\Filament\Crm\Resources\DocumentTemplates\Schemas;

use App\Models\DocumentTemplate;
use App\Support\Crm\DocumentTemplateDefaults;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
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
                            ->label('Belge Türü')
                            ->options(DocumentTemplate::TYPES)
                            ->disabled(fn (?DocumentTemplate $record): bool => $record !== null)
                            ->dehydrated(),
                        Toggle::make('is_active')->label('Aktif')->default(true),
                        Toggle::make('is_default')->label('Varsayılan şablon'),
                    ]),
                ]),
            Section::make('Arka Plan Görseli')
                ->description('Yüklediğiniz afiş veya belge görseli. Metinler bu görselin üzerine yazdırılır.')
                ->schema([
                    FileUpload::make('background_image')
                        ->label('Şablon görseli (PNG/JPG)')
                        ->disk('public')
                        ->directory('crm/templates')
                        ->image()
                        ->imageEditor()
                        ->helperText('Öneri: A4 oranı — dikey 2480×3508 px, yatay 3508×2480 px. PNG şeffaf alan destekler.'),
                ]),
            Section::make('Sayfa Yönü')
                ->schema([
                    Select::make('settings.orientation')
                        ->label('Yönlendirme')
                        ->options([
                            'portrait' => 'Dikey (A4)',
                            'landscape' => 'Yatay (A4)',
                        ])
                        ->default('portrait'),
                ]),
            Section::make('Metin Alanları')
                ->description('Her satır, şablon üzerinde bir dinamik alanı temsil eder. X/Y yüzde konumudur (0=sol/üst, 100=sağ/alt).')
                ->schema([
                    Repeater::make('settings.fields')
                        ->label('Alanlar')
                        ->default(fn (?DocumentTemplate $record): array => DocumentTemplateDefaults::settingsForType($record?->type ?? DocumentTemplate::TYPE_THANKS_POSTER)['fields'])
                        ->schema([
                            Select::make('key')
                                ->label('Alan')
                                ->options(DocumentTemplateDefaults::PLACEHOLDER_LABELS)
                                ->required()
                                ->searchable(),
                            Grid::make(4)->schema([
                                TextInput::make('x')->label('X (%)')->numeric()->minValue(0)->maxValue(100)->default(50)->required(),
                                TextInput::make('y')->label('Y (%)')->numeric()->minValue(0)->maxValue(100)->default(50)->required(),
                                TextInput::make('font_size')->label('Boyut (px)')->numeric()->minValue(8)->maxValue(72)->default(16)->required(),
                                ColorPicker::make('color')->label('Renk')->default('#0f172a'),
                            ]),
                            Grid::make(2)->schema([
                                Select::make('align')
                                    ->label('Hizalama')
                                    ->options([
                                        'left' => 'Sol',
                                        'center' => 'Orta',
                                        'right' => 'Sağ',
                                    ])
                                    ->default('center'),
                                Select::make('font_weight')
                                    ->label('Kalınlık')
                                    ->options([
                                        'normal' => 'Normal',
                                        'bold' => 'Kalın',
                                    ])
                                    ->default('normal'),
                            ]),
                        ])
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => DocumentTemplateDefaults::PLACEHOLDER_LABELS[$state['key'] ?? ''] ?? 'Alan')
                        ->columnSpanFull(),
                ]),
            Section::make('QR Kod')
                ->schema([
                    Grid::make(4)->schema([
                        Toggle::make('settings.qr.enabled')->label('QR göster')->default(true),
                        TextInput::make('settings.qr.x')->label('X (%)')->numeric()->default(88),
                        TextInput::make('settings.qr.y')->label('Y (%)')->numeric()->default(88),
                        TextInput::make('settings.qr.size')->label('Boyut (px)')->numeric()->default(70),
                    ]),
                ]),
        ]);
    }
}
