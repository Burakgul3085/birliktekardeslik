<?php

namespace App\Filament\Crm\Widgets;

use App\Filament\Crm\Resources\Notes\NoteResource;
use App\Models\CrmNote;
use App\Models\CrmUser;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentNotesWidget extends TableWidget
{
    protected static ?int $sort = 6;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Son Notlar';

    public function table(Table $table): Table
    {
        /** @var CrmUser|null $user */
        $user = auth('crm')->user();

        return $table
            ->query(
                CrmNote::query()
                    ->with(['author', 'donor', 'donation.donor'])
                    ->visibleTo($user)
                    ->orderByDesc('is_pinned')
                    ->orderByDesc('created_at')
                    ->limit(6),
            )
            ->paginated(false)
            ->emptyStateHeading('Henüz not yok')
            ->emptyStateDescription('Notlar bölümünden genel, bağışçı veya bağış notları ekleyebilirsiniz.')
            ->columns([
                IconColumn::make('is_pinned')
                    ->label('')
                    ->boolean()
                    ->trueIcon('heroicon-s-bookmark')
                    ->falseIcon('heroicon-o-bookmark')
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->width('36px'),
                TextColumn::make('display_title')
                    ->label('Not')
                    ->wrap()
                    ->limit(60)
                    ->url(fn (CrmNote $record): string => NoteResource::getUrl('edit', ['record' => $record])),
                TextColumn::make('scope')
                    ->label('Tür')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => CrmNote::SCOPES[$state] ?? $state),
                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => CrmNote::CATEGORIES[$state] ?? $state)
                    ->color('gray'),
                TextColumn::make('author.name')
                    ->label('Yazan')
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i'),
            ])
            ->headerActions([
                \Filament\Actions\Action::make('allNotes')
                    ->label('Tüm notlar')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->url(NoteResource::getUrl('index')),
            ]);
    }
}
