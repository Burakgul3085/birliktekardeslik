<?php

namespace App\Support\Zakat;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class PriceFetcher
{
    private const FOREX_CODES = ['USD', 'EUR', 'GBP', 'CHF', 'SAR', 'AED'];

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
        $response = $this->client()->get('https://api.genelpara.com/json', [
            'list' => 'doviz',
            'sembol' => implode(',', self::FOREX_CODES),
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException('GenelPara döviz kurları alınamadı.');
        }

        $payload = $response->json();

        if (! is_array($payload) || ($payload['success'] ?? false) !== true) {
            throw new \RuntimeException('GenelPara döviz yanıtı geçersiz.');
        }

        $data = $payload['data'] ?? [];
        $rates = [];
        $trends = [];

        foreach (self::FOREX_CODES as $code) {
            $parsed = $this->parseGenelParaRow($data[$code] ?? null);

            if ($parsed !== null) {
                $rates[$code] = $parsed['price'];
                $trends[strtolower($code)] = $parsed['trend'];
            }
        }

        if (count($rates) < 3) {
            throw new \RuntimeException('GenelPara döviz verisi eksik.');
        }

        return [
            'rates' => $rates,
            'trends' => $trends,
        ];
    }

    public function fetchMetals(): array
    {
        foreach (['GA,GAG,22,18,14,C,Y,T,ATA,CMR', 'all'] as $symbols) {
            $data = $this->requestGenelParaMetals($symbols);

            if ($data === null) {
                continue;
            }

            $parsed = $this->parseMetalsFromGenelPara($data);

            if ($parsed !== null) {
                return $parsed;
            }
        }

        $goldData = $this->requestGenelParaMetals('GA,22,18,14,C,Y,T,ATA,CMR');
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
        $gold24Row = $this->parseGenelParaRow($data['GA'] ?? null);
        $silverRow = $this->parseGenelParaRow($data['GAG'] ?? null);

        if ($gold24Row === null || $silverRow === null) {
            return null;
        }

        $gold24 = $gold24Row['price'];
        $silver = $silverRow['price'];

        $trends = [
            'gold_24' => $gold24Row['trend'],
            'silver' => $silverRow['trend'],
        ];

        $karatMap = [
            'gold_22' => '22',
            'gold_18' => '18',
            'gold_14' => '14',
        ];

        $prices = [
            'gold_24_per_gram' => $gold24,
            'gold_22_per_gram' => $this->parseGenelParaPrice($data['22'] ?? null) ?? round($gold24 * 0.916, 2),
            'gold_18_per_gram' => $this->parseGenelParaPrice($data['18'] ?? null) ?? round($gold24 * 0.75, 2),
            'gold_14_per_gram' => $this->parseGenelParaPrice($data['14'] ?? null) ?? round($gold24 * 0.585, 2),
            'silver_per_gram' => $silver,
            'coin_quarter_try' => $this->parseGenelParaPrice($data['C'] ?? null) ?? 0,
            'coin_half_try' => $this->parseGenelParaPrice($data['Y'] ?? null) ?? 0,
            'coin_full_try' => $this->parseGenelParaPrice($data['T'] ?? null) ?? 0,
            'coin_ata_try' => $this->parseGenelParaPrice($data['ATA'] ?? null) ?? 0,
            'coin_cmr_try' => $this->parseGenelParaPrice($data['CMR'] ?? null) ?? 0,
        ];

        $coinMap = [
            'coin_quarter' => 'C',
            'coin_half' => 'Y',
            'coin_full' => 'T',
            'coin_ata' => 'ATA',
            'coin_cmr' => 'CMR',
        ];

        foreach ($karatMap as $trendKey => $symbol) {
            $row = $this->parseGenelParaRow($data[$symbol] ?? null);
            $trends[$trendKey] = $row['trend'] ?? $this->flatTrend();
        }

        foreach ($coinMap as $trendKey => $symbol) {
            $row = $this->parseGenelParaRow($data[$symbol] ?? null);
            $trends[$trendKey] = $row['trend'] ?? $this->flatTrend();
        }

        return [
            'prices' => $prices,
            'trends' => $trends,
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

    private function parseGenelParaRow(mixed $row): ?array
    {
        $price = $this->parseGenelParaPrice($row);

        if ($price === null) {
            return null;
        }

        return [
            'price' => $price,
            'trend' => $this->parseGenelParaTrend($row),
        ];
    }

    private function parseGenelParaTrend(mixed $row): array
    {
        if (! is_array($row)) {
            return $this->flatTrend();
        }

        $change = isset($row['degisim'])
            ? (float) str_replace(',', '.', (string) $row['degisim'])
            : 0.0;

        $rate = isset($row['oran'])
            ? (float) str_replace(',', '.', (string) $row['oran'])
            : 0.0;

        $yon = (string) ($row['yon'] ?? 'moneyNat');

        $direction = match ($yon) {
            'moneyUp' => 'up',
            'moneyDown' => 'down',
            default => 'flat',
        };

        return [
            'change' => round($change, 4),
            'rate' => round($rate, 4),
            'direction' => $direction,
        ];
    }

    private function flatTrend(): array
    {
        return [
            'change' => 0.0,
            'rate' => 0.0,
            'direction' => 'flat',
        ];
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
