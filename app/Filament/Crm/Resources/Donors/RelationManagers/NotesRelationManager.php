<?php

namespace App\Filament\Crm\Resources\Donors\RelationManagers;

use App\Filament\Crm\Resources\Notes\NoteResource;
use App\Filament\Crm\Resources\Notes\Schemas\NoteForm;
use App\Filament\Crm\Resources\Notes\Tables\NotesTable;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NotesRelationManager extends RelationManager
{
    protected static string $relationship = 'crmNotes';

    protected static ?string $relatedResource = NoteResource::class;

    protected static ?string $title = 'Notlar';

    protected static ?string $modelLabel = 'not';

    public function form(Schema $schema): Schema
    {
        return NoteForm::configure($schema, 'donor');
    }

    public function table(Table $table): Table
    {
        return NotesTable::configure(
            $table
                ->modifyQueryUsing(fn (Builder $query): Builder => $query
                    ->with(['author'])
                    ->visibleTo(auth('crm')->user())
                    ->orderByDesc('is_pinned')
                    ->orderByDesc('created_at'))
                ->emptyStateHeading('Henüz not yok')
                ->emptyStateDescription('Bu bağışçı için toplantı, hatırlatma veya iletişim notları ekleyebilirsiniz.'),
            compact: true,
        )->headerActions([
            CreateAction::make()
                ->label('Not ekle')
                ->visible(fn (): bool => auth('crm')->user()?->canWriteNotes() ?? false)
                ->mutateFormDataUsing(function (array $data): array {
                    return array_merge($data, [
                        'scope' => 'donor',
                        'donor_id' => $this->getOwnerRecord()->getKey(),
                        'donation_id' => null,
                        'crm_user_id' => auth('crm')->id(),
                    ]);
                }),
        ]);
    }
}
