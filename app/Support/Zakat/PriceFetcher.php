<?php

namespace App\Support\Zakat;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use SimpleXMLElement;
use Throwable;

class PriceFetcher
{
    private function client(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::timeout(15)
            ->retry(2, 300)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept' => 'application/json, text/xml, application/xml, */*',
                'Accept-Language' => 'tr-TR,tr;q=0.9,en;q=0.8',
                'Referer' => 'https://www.genelpara.com/',
                'Origin' => 'https://www.genelpara.com',
            ]);
    }

    public function fetchForex(): array
    {
        $response = $this->client()
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
        foreach (['GA,GAG,22,18,14', 'all'] as $symbols) {
            $data = $this->requestGenelParaMetals($symbols);

            if ($data === null) {
                continue;
            }

            $parsed = $this->parseMetalsFromGenelPara($data);

            if ($parsed !== null) {
                return $parsed;
            }
        }

        $goldData = $this->requestGenelParaMetals('GA,22,18,14');
        $silverData = $this->requestGenelParaMetals('GAG');
        $merged = array_merge($goldData ?? [], $silverData ?? []);

        if ($merged === []) {
            throw new \RuntimeException('Altın ve gümüş fiyatları alınamadı.');
        }

        $parsed = $this->parseMetalsFromGenelPara($merged);

        if ($parsed === null) {
            throw new \RuntimeException('GenelPara yanıtı geçersiz metal fiyatı içeriyor.');
        }

        return $parsed;
    }

    private function parseMetalsFromGenelPara(array $data): ?array
    {
        $gold24 = $this->parseGenelParaPrice($data['GA'] ?? null);
        $silver = $this->parseGenelParaPrice($data['GAG'] ?? null);

        if ($gold24 === null || $silver === null) {
            return null;
        }

        return [
            'gold_24_per_gram' => $gold24,
            'gold_22_per_gram' => $this->parseGenelParaPrice($data['22'] ?? null) ?? round($gold24 * 0.916, 2),
            'gold_18_per_gram' => $this->parseGenelParaPrice($data['18'] ?? null) ?? round($gold24 * 0.75, 2),
            'gold_14_per_gram' => $this->parseGenelParaPrice($data['14'] ?? null) ?? round($gold24 * 0.585, 2),
            'silver_per_gram' => $silver,
        ];
    }

    private function requestGenelParaMetals(string $symbols): ?array
    {
        $response = $this->client()->get('https://api.genelpara.com/json', [
            'list' => 'altin',
            'sembol' => $symbols,
        ]);

        if (! $response->successful()) {
            Log::warning('GenelPara metals HTTP error', [
                'symbols' => $symbols,
                'status' => $response->status(),
                'body' => Str::limit((string) $response->body(), 300),
            ]);

            return null;
        }

        $payload = $response->json();

        if (! is_array($payload) || ($payload['success'] ?? false) !== true) {
            Log::warning('GenelPara metals invalid payload', [
                'symbols' => $symbols,
                'error' => is_array($payload) ? ($payload['error'] ?? null) : null,
                'body' => Str::limit((string) $response->body(), 300),
            ]);

            return null;
        }

        $data = $payload['data'] ?? [];

        return is_array($data) ? $data : null;
    }

    private function parseGenelParaPrice(mixed $row): ?float
    {
        if (! is_array($row)) {
            return null;
        }

        $satis = $row['satis'] ?? $row['alis'] ?? null;
        if ($satis === null) {
            return null;
        }

        $value = (float) str_replace(',', '.', (string) $satis);

        return $value > 0 ? round($value, 2) : null;
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
