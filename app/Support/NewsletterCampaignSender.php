<?php

namespace App\Support;

use App\Models\NewsletterSubscriber;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use PHPMailer\PHPMailer\Exception as MailException;

class NewsletterCampaignSender
{
    /**
     * @return array{sent: int, failed: int}
     */
    public function sendToAllActive(string $subject, string $bodyHtmlRaw): array
    {
        $settings = Setting::current();
        $raw = trim($bodyHtmlRaw);
        $bodyHtml = preg_match('/<[^>]+>/', $raw) ? $raw : nl2br(e($raw));

        $htmlTemplate = view('emails.newsletter-broadcast', [
            'subject' => $subject,
            'bodyHtml' => $bodyHtml,
            'siteSettings' => $settings,
        ])->render();

        $fromNotify = $settings->mailer_notification_email
            ?: $settings->email
            ?: $settings->mailer_from_address
            ?: (string) env('PHPMAILER_FROM_ADDRESS');
        $sent = 0;
        $failed = 0;

        foreach (NewsletterSubscriber::query()->active()->cursor() as $subscriber) {
            try {
                Mailer::send(
                    $subscriber->email,
                    'Abone',
                    $subject,
                    $htmlTemplate,
                    $fromNotify ?: null
                );
                $sent++;
            } catch (MailException $e) {
                $failed++;
                Log::error('E-bülten gönderim hatası.', [
                    'email' => $subscriber->email,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        Log::info('E-bülten kampanya tamamlandı.', [
            'subject' => $subject,
            'basarili' => $sent,
            'hata' => $failed,
        ]);

        return ['sent' => $sent, 'failed' => $failed];
    }

    /**
     * @param  iterable<int, NewsletterSubscriber>  $subscribers
     * @return array{sent: int, failed: int}
     */
    public function sendToSubscribers(string $subject, string $bodyHtmlRaw, iterable $subscribers): array
    {
        $settings = Setting::current();
        $raw = trim($bodyHtmlRaw);
        $bodyHtml = preg_match('/<[^>]+>/', $raw) ? $raw : nl2br(e($raw));

        $htmlTemplate = view('emails.newsletter-broadcast', [
            'subject' => $subject,
            'bodyHtml' => $bodyHtml,
            'siteSettings' => $settings,
        ])->render();

        $fromNotify = $settings->mailer_notification_email
            ?: $settings->email
            ?: $settings->mailer_from_address
            ?: (string) env('PHPMAILER_FROM_ADDRESS');
        $sent = 0;
        $failed = 0;

        foreach ($subscribers as $subscriber) {
            if (! $subscriber->is_active) {
                continue;
            }
            try {
                Mailer::send(
                    $subscriber->email,
                    'Abone',
                    $subject,
                    $htmlTemplate,
                    $fromNotify ?: null
                );
                $sent++;
            } catch (MailException $e) {
                $failed++;
                Log::error('E-bülten gönderim hatası.', [
                    'email' => $subscriber->email,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        Log::info('E-bülten (seçili) tamamlandı.', [
            'subject' => $subject,
            'basarili' => $sent,
            'hata' => $failed,
        ]);

        return ['sent' => $sent, 'failed' => $failed];
    }
}
