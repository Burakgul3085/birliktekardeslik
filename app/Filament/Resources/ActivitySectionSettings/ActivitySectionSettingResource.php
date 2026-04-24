<?php

namespace App\Filament\Resources\ActivitySectionSettings;

use App\Filament\Resources\ActivitySectionSettings\Pages\CreateActivitySectionSetting;
use App\Filament\Resources\ActivitySectionSettings\Pages\EditActivitySectionSetting;
use App\Filament\Resources\ActivitySectionSettings\Pages\ListActivitySectionSettings;
use App\Filament\Resources\ActivitySectionSettings\Schemas\ActivitySectionSettingForm;
use App\Filament\Resources\ActivitySectionSettings\Tables\ActivitySectionSettingsTable;
use App\Models\ActivitySectionSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ActivitySectionSettingResource extends Resource
{
    protected static ?string $model = ActivitySectionSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedViewColumns;

    protected static ?string $navigationLabel = 'Faaliyetlerimiz Alanı';

    protected static string|\UnitEnum|null $navigationGroup = 'İçerik Yönetimi';

    protected static ?string $modelLabel = 'Faaliyetlerimiz Alanı';

    protected static ?string $pluralModelLabel = 'Faaliyetlerimiz Alanı';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        return auth()->check();
    }

    public static function canCreate(): bool
    {
        return auth()->check() && auth()->user()?->canManageContent();
    }

    public static function canEdit($record): bool
    {
        return auth()->check() && auth()->user()?->canManageContent();
    }

    public static function canDelete($record): bool
    {
        return auth()->check() && auth()->user()?->canManageContent();
    }

    public static function form(Schema $schema): Schema
    {
        return ActivitySectionSettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ActivitySectionSettingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActivitySectionSettings::route('/'),
            'create' => CreateActivitySectionSetting::route('/create'),
            'edit' => EditActivitySectionSetting::route('/{record}/edit'),
        ];
    }
}
