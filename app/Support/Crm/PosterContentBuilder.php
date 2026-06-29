<?php

namespace App\Support\Crm;

use App\Models\Donation;

class PosterContentBuilder
{
    public static function displayName(Donation $donation, bool $uppercase = false): string
    {
        $name = trim($donation->poster_name ?: $donation->donor?->full_name ?: '');

        return $uppercase ? mb_strtoupper($name, 'UTF-8') : $name;
    }

    public static function thankYouBody(Donation $donation, ?string $messageTemplate = null): string
    {
        if (filled($messageTemplate)) {
            return self::renderMessageTemplate($messageTemplate, $donation);
        }

        $date = $donation->donated_at?->format('d.m.Y') ?? now()->format('d.m.Y');
        $type = $donation->donationType?->name ?: 'bağış';
        $amount = number_format((float) $donation->amount, 2, ',', '.');
        $currency = $donation->currency;

        return "{$date} tarihinde {$type} bağış türünden {$amount} {$currency} bağış yaptığınız için teşekkür ederiz.";
    }

    public static function renderMessageTemplate(string $template, Donation $donation): string
    {
        $replacements = [
            '{ad_soyad}' => self::displayName($donation),
            '{tutar}' => number_format((float) $donation->amount, 2, ',', '.'),
            '{para_birimi}' => $donation->currency,
            '{bagis_turu}' => $donation->donationType?->name ?? '',
            '{tarih}' => $donation->donated_at?->format('d.m.Y') ?? now()->format('d.m.Y'),
            '{bagis_no}' => $donation->donation_number,
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    public static function thankYouMessage(Donation $donation, ?string $messageTemplate = null): string
    {
        return self::salutation($donation) . ' ' . self::thankYouBody($donation, $messageTemplate);
    }

    public static function salutation(Donation $donation): string
    {
        $name = self::displayName($donation);

        return filled($name) ? "Sayın {$name}" : '';
    }
}
