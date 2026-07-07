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
                $stored['forex'] = $freshForex;
                $stored['forex_fetched_at'] = $now->timestamp;
            }
        }

        if ($metalsStale) {
            $freshMetals = $this->fetcher->tryFetchMetals();
            if ($freshMetals !== null) {
                $stored['metals'] = $freshMetals;
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
        $forex = is_array($stored['forex'] ?? null) ? $stored['forex'] : [];
        $metals = is_array($stored['metals'] ?? null) ? $stored['metals'] : [];

        $gold24 = (float) ($metals['gold_24_per_gram'] ?? 0);
        $nisapGrams = (float) config('zakat.nisap_grams', 80);

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

        return [
            'gold_24_per_gram' => $gold24,
            'gold_22_per_gram' => (float) ($metals['gold_22_per_gram'] ?? 0),
            'gold_18_per_gram' => (float) ($metals['gold_18_per_gram'] ?? 0),
            'gold_14_per_gram' => (float) ($metals['gold_14_per_gram'] ?? 0),
            'silver_per_gram' => (float) ($metals['silver_per_gram'] ?? 0),
            'usd_try' => (float) ($forex['USD'] ?? 0),
            'eur_try' => (float) ($forex['EUR'] ?? 0),
            'gbp_try' => (float) ($forex['GBP'] ?? 0),
            'nisap_threshold_try' => $gold24 > 0 ? round($nisapGrams * $gold24, 2) : 0,
            'nisap_grams' => $nisapGrams,
            'zakat_rate' => (float) config('zakat.rate', 0.025),
            'sources' => [
                'forex' => [
                    'name' => 'TCMB',
                    'label' => 'Türkiye Cumhuriyet Merkez Bankası (TCMB)',
                    'url' => 'https://www.tcmb.gov.tr',
                    'fetched_at' => $forexFetchedAt,
                    'is_stale' => $forexAge === null || $forexAge > config('zakat.cache.forex_ttl'),
                ],
                'metals' => [
                    'name' => 'GenelPara',
                    'label' => 'GenelPara (piyasa verisi)',
                    'url' => 'https://www.genelpara.com',
                    'fetched_at' => $metalsFetchedAt,
                    'is_stale' => $metalsAge === null || $metalsAge > config('zakat.cache.metals_ttl'),
                ],
            ],
            'has_data' => $gold24 > 0 && ! empty($forex),
        ];
    }
}
