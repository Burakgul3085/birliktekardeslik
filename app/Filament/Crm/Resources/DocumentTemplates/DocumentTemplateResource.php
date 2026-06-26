<?php

namespace App\Filament\Crm\Resources\DocumentTemplates;

use App\Filament\Crm\Resources\DocumentTemplates\Pages\ListDocumentTemplates;
use App\Filament\Crm\Resources\DocumentTemplates\Tables\DocumentTemplatesTable;
use App\Models\DocumentTemplate;
use BackedEnum;
use Filament\Resources\Resource;
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
        return auth('crm')->user()?->canManageCrmUsers() ?? false;
    }

    public static function canViewAny(): bool
    {
        return auth('crm')->user()?->canManageCrmUsers() ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return auth('crm')->user()?->canManageCrmUsers() ?? false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return DocumentTemplatesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDocumentTemplates::route('/'),
        ];
    }
}
