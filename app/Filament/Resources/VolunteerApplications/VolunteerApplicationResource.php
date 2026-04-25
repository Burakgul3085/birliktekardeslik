<?php

namespace App\Filament\Resources\VolunteerApplications;

use App\Filament\Resources\VolunteerApplications\Pages\ListVolunteerApplications;
use App\Filament\Resources\VolunteerApplications\Tables\VolunteerApplicationsTable;
use App\Models\VolunteerApplication;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class VolunteerApplicationResource extends Resource
{
    protected static ?string $model = VolunteerApplication::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHeart;

    protected static string|\UnitEnum|null $navigationGroup = 'Bağış ve İletişim';

    protected static ?string $navigationLabel = 'Gönüllü Başvuruları';

    protected static ?string $modelLabel = 'Gönüllü Başvurusu';

    protected static ?string $pluralModelLabel = 'Gönüllü Başvuruları';

    protected static ?int $navigationSort = 4;

    public static function canViewAny(): bool
    {
        return auth()->check() && auth()->user()?->canManageContent();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return auth()->check() && auth()->user()?->canManageContent();
    }

    public static function canDelete($record): bool
    {
        return auth()->check() && auth()->user()?->canManageContent();
    }

    public static function getNavigationBadge(): ?string
    {
        $pendingCount = VolunteerApplication::query()
            ->where('is_replied', false)
            ->count();

        return $pendingCount > 0 ? (string) $pendingCount : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function table(Table $table): Table
    {
        return VolunteerApplicationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVolunteerApplications::route('/'),
        ];
    }
}

