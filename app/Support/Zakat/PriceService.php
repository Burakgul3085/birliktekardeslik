<?php

namespace App\Support\Zakat;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;

class PriceService
{
    public function __construct(
        private readonly PriceFetcher $fetcher,
    ) {}

    public function snapshot(): array
    {
        $stored = $this->readStored();
        $now = now();

        $forexFetchedAt = (int) ($stored['forex_fetched_at'] ?? 0);
        $metalsFetchedAt = (int) ($stored['metals_fetched_at'] ?? 0);

        $forexStale = ($now->timestamp - $forexFetchedAt) >= config('zakat.cache.forex_ttl');
        $metalsStale = ($now->timestamp - $metalsFetchedAt) >= config('zakat.cache.metals_ttl');

        if ($forexStale) {
            $freshForex = $this->fetcher->tryFetchForex();
            if ($freshForex !== null) {
                $stored['forex'] = $freshForex['rates'];
                $stored['forex_trends'] = $freshForex['trends'];
                $stored['forex_fetched_at'] = $now->timestamp;
            }
        } elseif ($this->missingSupplementalForex($stored['forex'] ?? [])) {
            $freshSupplemental = $this->fetcher->tryFetchSupplementalForex();
            if ($freshSupplemental['rates'] !== []) {
                $stored['forex'] = array_merge($stored['forex'] ?? [], $freshSupplemental['rates']);
                $stored['forex_trends'] = array_merge($stored['forex_trends'] ?? [], $freshSupplemental['trends']);
            }
        }

        if ($metalsStale) {
            $freshMetals = $this->fetcher->tryFetchMetals();
            if ($freshMetals !== null) {
                $stored['metals'] = $freshMetals['prices'];
                $stored['metals_trends'] = $freshMetals['trends'];
                $stored['metals_fetched_at'] = $now->timestamp;
            }
        }

        if (! empty($stored)) {
            $this->writeStored($stored);
        }

        return $this->formatResponse($stored, $now);
    }

    private function readStored(): array
    {
        $path = $this->snapshotPath();

        if (! File::exists($path)) {
            return [];
        }

        $decoded = json_decode((string) File::get($path), true);

        return is_array($decoded) ? $decoded : [];
    }

    private function writeStored(array $data): void
    {
        File::ensureDirectoryExists(dirname($this->snapshotPath()));
        File::put($this->snapshotPath(), json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    private function snapshotPath(): string
    {
        return storage_path('app/zakat/prices-snapshot.json');
    }

    private function formatResponse(array $stored, Carbon $now): array
    {
        $forex = $this->normalizeForexRates($stored['forex'] ?? []);
        $metals = $this->normalizeMetals($stored['metals'] ?? []);
        $forexTrends = is_array($stored['forex_trends'] ?? null) ? $stored['forex_trends'] : [];
        $metalsTrends = is_array($stored['metals_trends'] ?? null) ? $stored['metals_trends'] : [];

        $supplemental = is_array($stored['supplemental_forex'] ?? null) ? $stored['supplemental_forex'] : [];
        foreach (['CHF', 'SAR', 'AED'] as $code) {
            if (empty($forex[$code]) && ! empty($supplemental[$code])) {
                $forex[$code] = (float) $supplemental[$code];
            }
        }

        $pageSettings = ZakatSettings::forPage();
        $gold24 = (float) ($metals['gold_24_per_gram'] ?? 0);
        $nisapGrams = (float) ($pageSettings['nisap_grams'] ?? 80);

        $forexFetchedAt = isset($stored['forex_fetched_at'])
            ? Carbon::createFromTimestamp((int) $stored['forex_fetched_at'])->toIso8601String()
            : null;

        $metalsFetchedAt = isset($stored['metals_fetched_at'])
            ? Carbon::createFromTimestamp((int) $stored['metals_fetched_at'])->toIso8601String()
            : null;

        $forexAge = isset($stored['forex_fetched_at'])
            ? $now->timestamp - (int) $stored['forex_fetched_at']
            : null;

        $metalsAge = isset($stored['metals_fetched_at'])
            ? $now->timestamp - (int) $stored['metals_fetched_at']
            : null;

        $hasForex = ! empty($forex['USD']) && ! empty($forex['EUR']) && ! empty($forex['GBP']);
        $hasMetals = $gold24 > 0 && (float) ($metals['silver_per_gram'] ?? 0) > 0;

        $fetchedAt = $forexFetchedAt && $metalsFetchedAt
            ? (strtotime($forexFetchedAt) > strtotime($metalsFetchedAt) ? $forexFetchedAt : $metalsFetchedAt)
            : ($forexFetchedAt ?? $metalsFetchedAt);

        return [
            'gold_24_per_gram' => $gold24,
            'gold_22_per_gram' => (float) ($metals['gold_22_per_gram'] ?? 0),
            'gold_18_per_gram' => (float) ($metals['gold_18_per_gram'] ?? 0),
            'gold_14_per_gram' => (float) ($metals['gold_14_per_gram'] ?? 0),
            'silver_per_gram' => (float) ($metals['silver_per_gram'] ?? 0),
            'coin_quarter_try' => (float) ($metals['coin_quarter_try'] ?? 0),
            'coin_half_try' => (float) ($metals['coin_half_try'] ?? 0),
            'coin_full_try' => (float) ($metals['coin_full_try'] ?? 0),
            'coin_ata_try' => (float) ($metals['coin_ata_try'] ?? 0),
            'coin_cmr_try' => (float) ($metals['coin_cmr_try'] ?? 0),
            'usd_try' => (float) ($forex['USD'] ?? 0),
            'eur_try' => (float) ($forex['EUR'] ?? 0),
            'gbp_try' => (float) ($forex['GBP'] ?? 0),
            'chf_try' => (float) ($forex['CHF'] ?? 0),
            'sar_try' => (float) ($forex['SAR'] ?? 0),
            'aed_try' => (float) ($forex['AED'] ?? 0),
            'nisap_threshold_try' => $gold24 > 0 ? round($nisapGrams * $gold24, 2) : 0,
            'nisap_grams' => $nisapGrams,
            'zakat_rate' => (float) ($pageSettings['zakat_rate'] ?? 0.025),
            'trends' => array_merge($metalsTrends, $forexTrends),
            'sources' => [
                'genelpara' => [
                    'name' => 'GenelPara',
                    'label' => 'GenelPara (piyasa verisi, resmi kur değildir)',
                    'url' => 'https://www.genelpara.com',
                    'fetched_at' => $fetchedAt,
                    'forex_fetched_at' => $forexFetchedAt,
                    'metals_fetched_at' => $metalsFetchedAt,
                    'is_stale' => ($forexAge === null || $forexAge > config('zakat.cache.forex_ttl'))
                        || ($metalsAge === null || $metalsAge > config('zakat.cache.metals_ttl')),
                ],
            ],
            'has_forex' => $hasForex,
            'has_metals' => $hasMetals,
            'has_data' => $hasForex && $hasMetals,
        ];
    }

    /**
     * @param  array<string, mixed>  $raw
     * @return array<string, float>
     */
    private function normalizeForexRates(array $raw): array
    {
        if (isset($raw['rates']) && is_array($raw['rates'])) {
            return $raw['rates'];
        }

        $rates = [];
        foreach (['USD', 'EUR', 'GBP', 'CHF', 'SAR', 'AED'] as $code) {
            if (isset($raw[$code])) {
                $rates[$code] = (float) $raw[$code];
            }
        }

        return $rates;
    }

    /**
     * @param  array<string, mixed>  $raw
     * @return array<string, mixed>
     */
    private function normalizeMetals(array $raw): array
    {
        if (isset($raw['prices']) && is_array($raw['prices'])) {
            return $raw['prices'];
        }

        return $raw;
    }

    /**
     * @param  array<string, mixed>  $forex
     */
    private function missingSupplementalForex(array $forex): bool
    {
        foreach (['CHF', 'SAR', 'AED'] as $code) {
            if (empty($forex[$code])) {
                return true;
            }
        }

        return false;
    }
}
