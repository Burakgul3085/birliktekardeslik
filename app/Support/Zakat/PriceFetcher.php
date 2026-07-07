<?php

namespace App\Support\Zakat;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;
use Throwable;

class PriceFetcher
{
    public function fetchForex(): array
    {
        $response = Http::timeout(12)
            ->retry(2, 200)
            ->get('https://www.tcmb.gov.tr/kurlar/today.xml');

        if (! $response->successful()) {
            throw new \RuntimeException('TCMB döviz kurları alınamadı.');
        }

        $xml = new SimpleXMLElement($response->body());
        $rates = [];

        foreach ($xml->Currency as $currency) {
            $code = (string) ($currency['CurrencyCode'] ?? $currency['Kod'] ?? '');
            if (! in_array($code, ['USD', 'EUR', 'GBP'], true)) {
                continue;
            }

            $selling = (string) ($currency->ForexSelling ?? $currency->BanknoteSelling ?? '0');
            $selling = (float) str_replace(',', '.', $selling);

            if ($selling > 0) {
                $rates[$code] = round($selling, 4);
            }
        }

        if (count($rates) < 3) {
            throw new \RuntimeException('TCMB yanıtı eksik döviz verisi içeriyor.');
        }

        return $rates;
    }

    public function fetchMetals(): array
    {
        $response = Http::timeout(12)
            ->retry(2, 200)
            ->get('https://api.genelpara.com/json/', [
                'list' => 'altin',
                'sembol' => 'GA,GAG,22,18,14',
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException('Altın ve gümüş fiyatları alınamadı.');
        }

        $payload = $response->json();
        $data = $payload['data'] ?? [];

        $parse = static function (mixed $row): ?float {
            if (! is_array($row)) {
                return null;
            }

            $satis = $row['satis'] ?? $row['alis'] ?? null;
            if ($satis === null) {
                return null;
            }

            $value = (float) str_replace(',', '.', (string) $satis);

            return $value > 0 ? round($value, 2) : null;
        };

        $gold24 = $parse($data['GA'] ?? null);
        $silver = $parse($data['GAG'] ?? null);

        if ($gold24 === null || $silver === null) {
            throw new \RuntimeException('GenelPara yanıtı geçersiz metal fiyatı içeriyor.');
        }

        return [
            'gold_24_per_gram' => $gold24,
            'gold_22_per_gram' => $parse($data['22'] ?? null) ?? round($gold24 * 0.916, 2),
            'gold_18_per_gram' => $parse($data['18'] ?? null) ?? round($gold24 * 0.75, 2),
            'gold_14_per_gram' => $parse($data['14'] ?? null) ?? round($gold24 * 0.585, 2),
            'silver_per_gram' => $silver,
        ];
    }

    public function tryFetchForex(): ?array
    {
        try {
            return $this->fetchForex();
        } catch (Throwable $exception) {
            Log::warning('Zakat forex fetch failed', ['message' => $exception->getMessage()]);

            return null;
        }
    }

    public function tryFetchMetals(): ?array
    {
        try {
            return $this->fetchMetals();
        } catch (Throwable $exception) {
            Log::warning('Zakat metals fetch failed', ['message' => $exception->getMessage()]);

            return null;
        }
    }
}
