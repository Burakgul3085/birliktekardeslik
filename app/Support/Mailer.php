<?php

namespace App\Support;

use App\Models\Setting;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{
    /**
     * @throws Exception
     */
    public static function send(string $to, string $toName, string $subject, string $htmlBody, ?string $replyTo = null): void
    {
        $mail = new PHPMailer(true);
        $mail->CharSet = 'UTF-8';
        $mail->isSMTP();
        $mail->Host = (string) env('PHPMAILER_HOST');
        $mail->Port = (int) env('PHPMAILER_PORT', 587);
        $mail->SMTPAuth = true;
        $mail->Username = (string) env('PHPMAILER_USERNAME');
        $mail->Password = (string) env('PHPMAILER_PASSWORD');

        $encryption = strtolower((string) env('PHPMAILER_ENCRYPTION', 'tls'));
        if ($encryption === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } else {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }

        $fromAddress = (string) env('PHPMAILER_FROM_ADDRESS');
        $fromName = (string) env('PHPMAILER_FROM_NAME', config('app.name'));

        $mail->setFrom($fromAddress, $fromName);
        $mail->addAddress($to, $toName);

        if ($replyTo) {
            $mail->addReplyTo($replyTo);
        }

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $htmlBody;
        $mail->AltBody = strip_tags($htmlBody);

        $settings = Setting::current();
        if ($settings->logo) {
            $logoPath = storage_path('app/public/' . $settings->logo);
            if (is_file($logoPath)) {
                $mail->addEmbeddedImage($logoPath, 'bkd-logo');
            }
        }

        $mail->send();
    }
}

