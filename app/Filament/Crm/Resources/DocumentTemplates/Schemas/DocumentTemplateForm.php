<?php

namespace App\Filament\Crm\Resources\DocumentTemplates\Schemas;

use App\Models\DocumentTemplate;
use Filament\Forms\Components\FileUpload;
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
                            ->options(DocumentTemplate::ACTIVE_TYPES)
                            ->disabled(fn (?DocumentTemplate $record): bool => $record !== null)
                            ->dehydrated()
                            ->required(),
                        Toggle::make('is_active')->label('Aktif')->default(true),
                        Toggle::make('is_default')->label('Varsayılan şablon'),
                    ]),
                ]),
            Section::make('Şablon Görseli')
                ->description('Boş afiş veya belge görselinizi yükleyin. Bağış bilgileri sistem tarafından otomatik olarak bu görselin üzerine yazdırılır.')
                ->schema([
                    FileUpload::make('background_image')
                        ->label('Boş şablon (PNG/JPG)')
                        ->disk('public')
                        ->directory('crm/templates')
                        ->image()
                        ->imageEditor()
                        ->required(fn (?DocumentTemplate $record): bool => $record?->requiresBackground() ?? false)
                        ->helperText('Makbuz için isteğe bağlıdır. Bağış ve teşekkür afişleri için zorunludur. Öneri: dikey A4 — 2480×3508 px.'),
                ]),
        ]);
    }
}
