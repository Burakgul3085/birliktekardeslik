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

    public static function thankYouMessage(Donation $donation): string
    {
        $name = self::displayName($donation);
        $date = $donation->donated_at?->format('d.m.Y') ?? now()->format('d.m.Y');
        $type = $donation->donationType?->name ?: 'bağış';
        $amount = number_format((float) $donation->amount, 2, ',', '.');
        $currency = $donation->currency;

        return "Sayın {$name}, {$date} tarihinde {$type} bağış türünden {$amount} {$currency} bağış yaptığınız için teşekkür ederiz.";
    }

    public static function salutation(Donation $donation): string
    {
        $name = self::displayName($donation);

        return filled($name) ? "Sayın {$name}" : '';
    }
}
