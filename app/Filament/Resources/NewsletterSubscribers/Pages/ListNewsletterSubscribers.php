<?php

namespace App\Filament\Resources\NewsletterSubscribers\Pages;

use App\Filament\Resources\NewsletterSubscribers\NewsletterSubscriberResource;
use App\Jobs\SendNewsletterCampaignJob;
use App\Models\NewsletterSubscriber;
use App\Models\Setting;
use App\Support\NewsletterCampaignSender;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListNewsletterSubscribers extends ListRecords
{
    protected static string $resource = NewsletterSubscriberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sendBulk')
                ->label('Tüm abonelere bülten gönder')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->form([
                    TextInput::make('subject')
                        ->label('E-posta konusu')
                        ->required()
                        ->maxLength(190)
                        ->default(fn (): string => (Setting::current()->site_title ?? 'Birlikte Kardeşlik Derneği') . ' — Duyuru'),
                    Textarea::make('body_html')
                        ->label('İçerik (HTML veya düz metin; paragraflar için satırlar arasında boşluk bırakın)')
                        ->rows(16)
                        ->required()
                        ->columnSpanFull(),
                ])
                ->modalWidth('3xl')
                ->modalDescription($this->campaignModalDescription())
                ->action(function (array $data): void {
                    if (NewsletterSubscriber::query()->active()->doesntExist()) {
                        Notification::make()
                            ->title('Aktif abone bulunmuyor.')
                            ->warning()
                            ->send();

                        return;
                    }

                    $subject = $data['subject'];
                    $body = trim($data['body_html']);

                    if (config('newsletter.async_campaign')) {
                        SendNewsletterCampaignJob::dispatch($subject, $body);
                        Notification::make()
                            ->title('Kampanya kuyruğa alındı')
                            ->body('Mailler arka planda gönderilecek. Sunucuda `php artisan queue:work` sürekli çalışmalıdır.')
                            ->success()
                            ->send();

                        return;
                    }

                    $result = app(NewsletterCampaignSender::class)->sendToAllActive($subject, $body);

                    Notification::make()
                        ->title('Gönderim tamamlandı')
                        ->body("Başarılı: {$result['sent']} · Hata: {$result['failed']}")
                        ->success()
                        ->send();
                }),
        ];
    }

    private function campaignModalDescription(): string
    {
        if (config('newsletter.async_campaign')) {
            return 'Aktif tüm abonelere aynı e-posta kuyrukta gönderilir. `NEWSLETTER_ASYNC_CAMPAIGN=true` ve çalışan `queue:work` gerekir.';
        }

        return 'Aktif tüm abonelere aynı e-posta hemen gönderilir (kuyruk kullanılmaz). Çok sayıda abone varsa işlem bir sürebilir.';
    }
}
