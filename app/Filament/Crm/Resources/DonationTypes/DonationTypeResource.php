<?php

namespace App\Filament\Crm\Resources\DonationTypes;

use App\Filament\Crm\Resources\DonationTypes\Pages\CreateDonationType;
use App\Filament\Crm\Resources\DonationTypes\Pages\EditDonationType;
use App\Filament\Crm\Resources\DonationTypes\Pages\ListDonationTypes;
use App\Filament\Crm\Resources\DonationTypes\Schemas\DonationTypeForm;
use App\Filament\Crm\Resources\DonationTypes\Tables\DonationTypesTable;
use App\Models\CrmUser;
use App\Models\DonationType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DonationTypeResource extends Resource
{
    protected static ?string $model = DonationType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $navigationLabel = 'Bağış Türleri';

    protected static string|\UnitEnum|null $navigationGroup = 'Bağış Yönetimi';

    protected static ?string $modelLabel = 'Bağış Türü';

    protected static ?string $pluralModelLabel = 'Bağış Türleri';

    protected static ?int $navigationSort = 5;

    protected static function crmUser(): ?CrmUser
    {
        $user = auth('crm')->user();

        return $user instanceof CrmUser ? $user : null;
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
        return self::crmUser()?->canWriteDonations() ?? false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount('donations')
            ->withSum('donations', 'amount')
            ->orderBy('sort_order')
            ->orderBy('name');
    }

    public static function form(Schema $schema): Schema
    {
        return DonationTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DonationTypesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDonationTypes::route('/'),
            'create' => CreateDonationType::route('/create'),
            'edit' => EditDonationType::route('/{record}/edit'),
        ];
    }
}
