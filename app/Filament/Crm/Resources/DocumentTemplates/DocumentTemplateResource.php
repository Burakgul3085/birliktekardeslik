<?php

namespace App\Filament\Crm\Resources\DocumentTemplates;

use App\Filament\Crm\Resources\DocumentTemplates\Pages\CreateDocumentTemplate;
use App\Filament\Crm\Resources\DocumentTemplates\Pages\EditDocumentTemplate;
use App\Filament\Crm\Resources\DocumentTemplates\Pages\ListDocumentTemplates;
use App\Filament\Crm\Resources\DocumentTemplates\Schemas\DocumentTemplateForm;
use App\Models\DocumentTemplate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DocumentTemplateResource extends Resource
{
    protected static ?string $model = DocumentTemplate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentDuplicate;

    protected static ?string $navigationLabel = 'Belge Şablonları';

    protected static string|\UnitEnum|null $navigationGroup = 'Sistem';

    protected static ?string $modelLabel = 'Belge Şablonu';

    protected static ?string $pluralModelLabel = 'Belge Şablonları';

    protected static ?int $navigationSort = 50;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        return auth('crm')->user()?->canManageCrmUsers() ?? false;
    }

    public static function canCreate(): bool
    {
        return auth('crm')->user()?->canManageCrmUsers() ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth('crm')->user()?->canManageCrmUsers() ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth('crm')->user()?->canManageCrmUsers() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return DocumentTemplateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return \App\Filament\Crm\Resources\DocumentTemplates\Tables\DocumentTemplatesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDocumentTemplates::route('/'),
            'create' => CreateDocumentTemplate::route('/create'),
            'edit' => EditDocumentTemplate::route('/{record}/edit'),
        ];
    }
}
