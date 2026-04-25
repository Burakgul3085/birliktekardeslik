<?php

namespace App\Filament\Resources\AdminActivityLogs;

use App\Filament\Resources\AdminActivityLogs\Pages\ListAdminActivityLogs;
use App\Filament\Resources\AdminActivityLogs\Tables\AdminActivityLogsTable;
use App\Models\AdminActivityLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AdminActivityLogResource extends Resource
{
    protected static ?string $model = AdminActivityLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static string|\UnitEnum|null $navigationGroup = 'Yönetim';

    protected static ?string $navigationLabel = 'Admin Log Kayıtları';

    protected static ?string $modelLabel = 'Admin Log';

    protected static ?string $pluralModelLabel = 'Admin Log Kayıtları';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->check() && auth()->user()?->isSuperAdmin();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return auth()->check() && auth()->user()?->isSuperAdmin();
    }

    public static function canDeleteAny(): bool
    {
        return auth()->check() && auth()->user()?->isSuperAdmin();
    }

    public static function table(Table $table): Table
    {
        return AdminActivityLogsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdminActivityLogs::route('/'),
        ];
    }
}
