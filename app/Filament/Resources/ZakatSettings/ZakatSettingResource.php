<?php

namespace App\Filament\Resources\ZakatSettings;

use App\Filament\Resources\ZakatSettings\Pages\CreateZakatSetting;
use App\Filament\Resources\ZakatSettings\Pages\EditZakatSetting;
use App\Filament\Resources\ZakatSettings\Pages\ListZakatSettings;
use App\Filament\Resources\ZakatSettings\Schemas\ZakatSettingForm;
use App\Filament\Resources\ZakatSettings\Tables\ZakatSettingsTable;
use App\Models\ZakatSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ZakatSettingResource extends Resource
{
    protected static ?string $model = ZakatSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalculator;

    protected static ?string $navigationLabel = 'Zekât Ayarları';

    protected static string|\UnitEnum|null $navigationGroup = 'İçerik Yönetimi';

    protected static ?string $modelLabel = 'Zekât Ayarı';

    protected static ?string $pluralModelLabel = 'Zekât Ayarları';

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
        return ZakatSettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ZakatSettingsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListZakatSettings::route('/'),
            'create' => CreateZakatSetting::route('/create'),
            'edit' => EditZakatSetting::route('/{record}/edit'),
        ];
    }
}
