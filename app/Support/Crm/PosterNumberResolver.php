<?php

namespace App\Support\Crm;

use App\Models\Donation;
use App\Models\PosterTemplate;

/**
 * Afiş numarasını bağış numarasından türetir.
 *
 * Bağış ve makbuz numaraları her kayıtta birlikte üretildiği için sıra
 * numaraları daima eşittir (BAG-2026-00042 / MKB-2026-00042). Afiş numarası da
 * aynı sıradan türetilerek bir bağışın tüm belgeleri aynı numarayı taşır.
 * Böylece ayrı bir sayaca ve veritabanı alanına ihtiyaç kalmaz; afiş yeniden
 * üretildiğinde de numara değişmez.
 */
class PosterNumberResolver
{
    private const PREFIXES = [
        PosterTemplate::TYPE_DONATION => 'BAF',
        PosterTemplate::TYPE_THANKS => 'TAF',
    ];

    public static function for(Donation $donation, ?string $posterType): string
    {
        $prefix = self::PREFIXES[$posterType] ?? self::PREFIXES[PosterTemplate::TYPE_DONATION];
        $donationNumber = trim((string) $donation->donation_number);

        if ($donationNumber === '') {
            return $prefix . '-' . $donation->getKey();
        }

        /* BAG-2026-00042 -> BAF-2026-00042: yalnızca baştaki harf öneki değişir */
        if (preg_match('/^[A-Za-z]+-(.+)$/', $donationNumber, $matches) === 1) {
            return $prefix . '-' . $matches[1];
        }

        return $prefix . '-' . $donationNumber;
    }
}
