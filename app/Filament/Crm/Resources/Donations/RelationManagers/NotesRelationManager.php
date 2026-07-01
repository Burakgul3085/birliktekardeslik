<?php

namespace App\Filament\Crm\Resources\Donations\RelationManagers;

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

    protected static ?string $title = 'Notlar';

    protected static ?string $modelLabel = 'not';

    public function form(Schema $schema): Schema
    {
        return NoteForm::configure($schema, 'donation');
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
                ->emptyStateDescription('Bu bağış için makbuz, iletişim veya operasyon notları ekleyebilirsiniz.'),
            compact: true,
        )->headerActions([
            CreateAction::make()
                ->label('Not ekle')
                ->visible(fn (): bool => auth('crm')->user()?->canWriteNotes() ?? false)
                ->mutateFormDataUsing(function (array $data): array {
                    return array_merge($data, [
                        'scope' => 'donation',
                        'donation_id' => $this->getOwnerRecord()->getKey(),
                        'donor_id' => null,
                        'crm_user_id' => auth('crm')->id(),
                    ]);
                }),
        ]);
    }
}
