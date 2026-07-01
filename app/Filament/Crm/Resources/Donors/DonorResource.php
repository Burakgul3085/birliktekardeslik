<?php

namespace App\Filament\Crm\Resources\Donors;

use App\Filament\Crm\Resources\Donors\Pages\CreateDonor;
use App\Filament\Crm\Resources\Donors\Pages\EditDonor;
use App\Filament\Crm\Resources\Donors\Pages\ListDonors;
use App\Filament\Crm\Resources\Donors\Pages\ViewDonor;
use App\Filament\Crm\Resources\Donors\RelationManagers\DonationsRelationManager;
use App\Filament\Crm\Resources\Donors\RelationManagers\NotesRelationManager;
use App\Filament\Crm\Resources\Donors\Schemas\DonorForm;
use App\Filament\Crm\Resources\Donors\Schemas\DonorInfolist;
use App\Filament\Crm\Resources\Donors\Tables\DonorsTable;
use App\Models\CrmUser;
use App\Models\Donor;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DonorResource extends Resource
{
    protected static ?string $model = Donor::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = 'Bağışçılar';

    protected static string|\UnitEnum|null $navigationGroup = 'Bağış Yönetimi';

    protected static ?string $modelLabel = 'Bağışçı';

    protected static ?string $pluralModelLabel = 'Bağışçılar';

    protected static ?int $navigationSort = 1;

    protected static function crmUser(): ?CrmUser
    {
        return auth('crm')->user();
    }

    public static function canViewAny(): bool
    {
        return self::crmUser() !== null;
    }

    public static function canCreate(): bool
    {
        return self::crmUser()?->canWriteDonations() ?? false;
    }

    public static function canEdit($record): bool
    {
        return self::crmUser()?->canWriteDonations() ?? false;
    }

    public static function canDelete($record): bool
    {
        return self::crmUser()?->canDeleteRecords() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return DonorForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DonorInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DonorsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            DonationsRelationManager::class,
            NotesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDonors::route('/'),
            'create' => CreateDonor::route('/create'),
            'view' => ViewDonor::route('/{record}'),
            'edit' => EditDonor::route('/{record}/edit'),
        ];
    }
}
