<?php

namespace App\Filament\Resources\Testimonials\Tables;

use App\Models\Testimonial;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TestimonialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('display_name')
                    ->label('Ad')
                    ->searchable(['name', 'display_name']),
                TextColumn::make('city')
                    ->label('Şehir')
                    ->searchable(),
                TextColumn::make('rating')
                    ->label('Puan')
                    ->formatStateUsing(fn (int $state): string => str_repeat('★', $state).str_repeat('☆', 5 - $state))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        Testimonial::STATUS_APPROVED => 'Onaylı',
                        Testimonial::STATUS_REJECTED => 'Reddedildi',
                        default => 'Bekliyor',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        Testimonial::STATUS_APPROVED => 'success',
                        Testimonial::STATUS_REJECTED => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('comment')
                    ->label('Yorum')
                    ->limit(70)
                    ->wrap()
                    ->action('goruntule'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        Testimonial::STATUS_PENDING => 'Bekliyor',
                        Testimonial::STATUS_APPROVED => 'Onaylı',
                        Testimonial::STATUS_REJECTED => 'Reddedildi',
                    ]),
            ])
            ->recordActions([
                Action::make('goruntule')
                    ->label('Görüntüle')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalHeading(fn (Testimonial $record): string => $record->display_name.' — Yorum Detayı')
                    ->modalWidth('2xl')
                    ->form([
                        TextInput::make('display_name')->label('Görünen Ad')->disabled(),
                        TextInput::make('city')->label('Şehir')->disabled(),
                        TextInput::make('email')->label('E-posta')->disabled(),
                        TextInput::make('rating')->label('Puan')->disabled(),
                        TextInput::make('flags')->label('Rozetler')->disabled(),
                        Textarea::make('comment')->label('Yorum')->rows(8)->disabled()->columnSpanFull(),
                    ])
                    ->fillForm(fn (Testimonial $record): array => [
                        'display_name' => $record->display_name,
                        'city' => $record->city,
                        'email' => $record->email,
                        'rating' => str_repeat('★', $record->rating),
                        'flags' => collect([
                            $record->is_volunteer ? 'Gönüllü' : null,
                            $record->is_donor ? 'Bağışçı' : null,
                            $record->is_anonymous ? 'İsim gizli' : null,
                        ])->filter()->implode(', ') ?: '-',
                        'comment' => $record->comment,
                    ])
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Kapat'),

                Action::make('onayla')
                    ->label('Onayla')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Testimonial $record): bool => $record->status !== Testimonial::STATUS_APPROVED)
                    ->requiresConfirmation()
                    ->action(function (Testimonial $record): void {
                        $record->approve();

                        Notification::make()
                            ->title('Yorum onaylandı')
                            ->success()
                            ->send();
                    }),

                Action::make('reddet')
                    ->label('Reddet')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Testimonial $record): bool => $record->status !== Testimonial::STATUS_REJECTED)
                    ->requiresConfirmation()
                    ->action(function (Testimonial $record): void {
                        $record->reject();

                        Notification::make()
                            ->title('Yorum reddedildi')
                            ->success()
                            ->send();
                    }),

                DeleteAction::make()->label('Sil'),
            ]);
    }
}
