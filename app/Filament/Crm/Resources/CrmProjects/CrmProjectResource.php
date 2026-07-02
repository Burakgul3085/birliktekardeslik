<?php

namespace App\Filament\Crm\Resources\CrmProjects;

use App\Filament\Crm\Resources\CrmProjects\Pages\CreateCrmProject;
use App\Filament\Crm\Resources\CrmProjects\Pages\EditCrmProject;
use App\Filament\Crm\Resources\CrmProjects\Pages\ListCrmProjects;
use App\Filament\Crm\Resources\CrmProjects\Schemas\CrmProjectForm;
use App\Filament\Crm\Resources\CrmProjects\Tables\CrmProjectsTable;
use App\Models\CrmUser;
use App\Models\Project;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CrmProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Proje / Faaliyetler';

    protected static string|\UnitEnum|null $navigationGroup = 'Liste Tanımları';

    protected static ?string $modelLabel = 'Proje / Faaliyet';

    protected static ?string $pluralModelLabel = 'Proje / Faaliyetler';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'faaliyetler';

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
            ->orderBy('title');
    }

    public static function form(Schema $schema): Schema
    {
        return CrmProjectForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CrmProjectsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCrmProjects::route('/'),
            'create' => CreateCrmProject::route('/create'),
            'edit' => EditCrmProject::route('/{record}/edit'),
        ];
    }
}
