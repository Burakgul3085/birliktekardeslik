<?php

namespace App\Filament\Crm\Resources\CrmUsers;

use App\Filament\Crm\Resources\CrmUsers\Pages\CreateCrmUser;
use App\Filament\Crm\Resources\CrmUsers\Pages\EditCrmUser;
use App\Filament\Crm\Resources\CrmUsers\Pages\ListCrmUsers;
use App\Filament\Crm\Resources\CrmUsers\Schemas\CrmUserForm;
use App\Filament\Crm\Resources\CrmUsers\Tables\CrmUsersTable;
use App\Models\CrmUser;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CrmUserResource extends Resource
{
    protected static ?string $model = CrmUser::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = 'CRM Kullanıcıları';

    protected static string|\UnitEnum|null $navigationGroup = 'Sistem';

    protected static ?string $modelLabel = 'CRM Kullanıcısı';

    protected static ?string $pluralModelLabel = 'CRM Kullanıcıları';

    protected static ?int $navigationSort = 1;

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
        return CrmUserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CrmUsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCrmUsers::route('/'),
            'create' => CreateCrmUser::route('/create'),
            'edit' => EditCrmUser::route('/{record}/edit'),
        ];
    }
}
