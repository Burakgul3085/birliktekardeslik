<?php

namespace App\Filament\Resources\NewsletterSubscribers\Tables;

use App\Jobs\SendNewsletterToSubscribersJob;
use App\Support\NewsletterCampaignSender;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class NewsletterSubscribersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('subscribed_at', 'desc')
            ->columns([
                TextColumn::make('email')
                    ->label('E-posta')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('subscribed_at')
                    ->label('Kayıt')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->recordActions([
                DeleteAction::make()
                    ->label('Sil')
                    ->requiresConfirmation(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('sendNewsletter')
                        ->label('Seçilenlere bülten gönder')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('success')
                        ->form([
                            TextInput::make('subject')
                                ->label('E-posta konusu')
                                ->required()
                                ->maxLength(190),
                            Textarea::make('body_html')
                                ->label('İçerik (HTML veya düz metin)')
                                ->rows(12)
                                ->required()
                                ->columnSpanFull(),
                        ])
                        ->modalWidth('2xl')
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records, array $data): void {
                            $active = $records->filter(fn ($r) => $r->is_active);
                            if ($active->isEmpty()) {
                                Notification::make()
                                    ->title('Seçilenler arasında aktif abone yok.')
                                    ->warning()
                                    ->send();

                                return;
                            }

                            $subject = $data['subject'];
                            $body = trim($data['body_html']);

                            if (config('newsletter.async_campaign')) {
                                SendNewsletterToSubscribersJob::dispatch(
                                    $subject,
                                    $body,
                                    $active->pluck('id')->all()
                                );
                                Notification::make()
                                    ->title('Seçili gönderim kuyruğa alındı')
                                    ->body('Mailler arka planda gönderilecek (`queue:work`).')
                                    ->success()
                                    ->send();

                                return;
                            }

                            $result = app(NewsletterCampaignSender::class)->sendToSubscribers($subject, $body, $active);

                            Notification::make()
                                ->title('Gönderim tamamlandı')
                                ->body("Başarılı: {$result['sent']} · Hata: {$result['failed']}")
                                ->success()
                                ->send();
                        }),
                    DeleteBulkAction::make()->label('Seçilenleri sil'),
                ]),
            ]);
    }
}
