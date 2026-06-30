<?php

namespace App\Filament\Crm\Resources\Donations;

use App\Filament\Crm\Resources\Donations\Pages\CreateDonation;
use App\Filament\Crm\Resources\Donations\Pages\EditDonation;
use App\Filament\Crm\Resources\Donations\Pages\ListDonations;
use App\Filament\Crm\Resources\Donations\RelationManagers\DocumentsRelationManager;
use App\Filament\Crm\Resources\Donations\RelationManagers\PostersRelationManager;
use App\Filament\Crm\Resources\Donations\Schemas\DonationForm;
use App\Filament\Crm\Resources\Donations\Tables\DonationsTable;
use App\Models\CrmUser;
use App\Models\Donation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DonationResource extends Resource
{
    protected static ?string $model = Donation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = 'Bağışlar';

    protected static string|\UnitEnum|null $navigationGroup = 'Bağış Yönetimi';

    protected static ?string $modelLabel = 'Bağış';

    protected static ?string $pluralModelLabel = 'Bağışlar';

    protected static ?int $navigationSort = 2;

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
        return DonationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DonationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            DocumentsRelationManager::class,
            PostersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDonations::route('/'),
            'create' => CreateDonation::route('/create'),
            'edit' => EditDonation::route('/{record}/edit'),
        ];
    }
}
