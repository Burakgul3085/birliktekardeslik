<?php

namespace App\Filament\Crm\Resources\Notes;

use App\Filament\Crm\Resources\Notes\Pages\CreateNote;
use App\Filament\Crm\Resources\Notes\Pages\EditNote;
use App\Filament\Crm\Resources\Notes\Pages\ListNotes;
use App\Filament\Crm\Resources\Notes\Schemas\NoteForm;
use App\Filament\Crm\Resources\Notes\Tables\NotesTable;
use App\Models\CrmNote;
use App\Models\CrmUser;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NoteResource extends Resource
{
    protected static ?string $model = CrmNote::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel = 'Notlar';

    protected static string|\UnitEnum|null $navigationGroup = 'Bağış Yönetimi';

    protected static ?string $modelLabel = 'Not';

    protected static ?string $pluralModelLabel = 'Notlar';

    protected static ?int $navigationSort = 4;

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
        return self::crmUser()?->canWriteNotes() ?? false;
    }

    public static function canEdit($record): bool
    {
        return self::crmUser()?->canEditNote($record) ?? false;
    }

    public static function canDelete($record): bool
    {
        return self::crmUser()?->canDeleteNote($record) ?? false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['author', 'donor', 'donation.donor'])
            ->visibleTo(self::crmUser())
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at');
    }

    public static function form(Schema $schema): Schema
    {
        return NoteForm::configure($schema, 'resource');
    }

    public static function table(Table $table): Table
    {
        return NotesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNotes::route('/'),
            'create' => CreateNote::route('/create'),
            'edit' => EditNote::route('/{record}/edit'),
        ];
    }
}
