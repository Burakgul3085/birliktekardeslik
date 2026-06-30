<?php

namespace App\Support\Crm;

use App\Models\Donation;
use App\Models\Donor;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DonorSpreadsheetExporter extends CorporateSpreadsheet
{
    /** Sütun başlıkları */
    private const HEADERS = [
        'Sıra',
        'Ad',
        'Soyad',
        'Telefon',
        'E-posta',
        'Şehir',
        'Ülke',
        'Adres',
        'Bağış Sayısı',
        'Toplam Tutar',
        'İlk Bağış',
        'Son Bağış',
        'Kayıt Tarihi',
    ];

    /** 1 tabanlı sütun genişlikleri (A=1) */
    private const COLUMN_WIDTHS = [6, 16, 16, 16, 28, 14, 12, 32, 13, 16, 14, 14, 14];

    private const COUNT_COLUMN_INDEX = 8;  // 0 tabanlı (Bağış Sayısı)

    private const AMOUNT_COLUMN_INDEX = 9; // 0 tabanlı (Toplam Tutar)

    public static function download(Builder $query, ?string $filename = null): StreamedResponse
    {
        $filename ??= 'bagiscilar-' . now()->format('Y-m-d_His') . '.xlsx';

        return response()->streamDownload(function () use ($query): void {
            self::write($query);
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private static function write(Builder $query): void
    {
        $lastColumn = count(self::HEADERS) - 1;

        // Özet veriler: bağışçı listesine ait bağışlar üzerinden hesaplanır.
        $base = (clone $query)->reorder();
        $recordCount = (int) (clone $base)->count();
        $donorIds = (clone $base)->pluck('id')->all();

        $totalDonationCount = Donation::query()->whereIn('donor_id', $donorIds)->count();
        $totalAmount = (float) Donation::query()->whereIn('donor_id', $donorIds)->sum('amount');
        $totalsByCurrency = Donation::query()
            ->whereIn('donor_id', $donorIds)
            ->select('currency', DB::raw('SUM(amount) as total'))
            ->groupBy('currency')
            ->pluck('total', 'currency')
            ->all();

        $metaLine = sprintf(
            'Rapor Tarihi: %s          Bağışçı Sayısı: %d          Toplam Bağış: %s TRY (%d bağış)',
            now()->format('d.m.Y H:i'),
            $recordCount,
            number_format($totalAmount, 2, ',', '.'),
            $totalDonationCount,
        );

        $bands = self::titleBands(Setting::current(), 'BAĞIŞÇI RAPORU', $metaLine);

        $options = new Options();
        foreach (self::COLUMN_WIDTHS as $i => $width) {
            $options->setColumnWidth((float) $width, $i + 1);
        }

        $titleRowCount = count($bands);
        $headerRow = $titleRowCount + 1;
        $firstDataRow = $headerRow + 1;
        $totalsRow = $firstDataRow + max($recordCount, 0);

        for ($r = 1; $r <= $titleRowCount; $r++) {
            $options->mergeCells(0, $r, $lastColumn, $r);
        }
        // Toplam satırında etiket alanı (Sıra..Adres) birleştirilir
        $options->mergeCells(0, $totalsRow, self::COUNT_COLUMN_INDEX - 1, $totalsRow);

        $writer = new Writer($options);
        $writer->openToFile('php://output');

        foreach ($bands as [$value, $style]) {
            $writer->addRow(self::bandRow($value, $lastColumn, $style));
        }

        self::writeHeaderRow($writer);
        self::writeDataRows($writer, $query);
        self::writeTotalsRow($writer, $totalDonationCount, $totalAmount, $totalsByCurrency, $lastColumn);

        $writer->close();
    }

    private static function writeHeaderRow(Writer $writer): void
    {
        $style = self::headerStyle();
        $cells = [];

        foreach (self::HEADERS as $header) {
            $cells[] = Cell::fromValue($header, $style);
        }

        $writer->addRow(new Row($cells));
    }

    private static function writeDataRows(Writer $writer, Builder $query): void
    {
        $textStyles = [self::cellStyle(false), self::cellStyle(true)];
        $amountStyles = [self::amountStyle(false), self::amountStyle(true)];
        $centerStyles = [self::cellStyle(false, CellAlignment::CENTER), self::cellStyle(true, CellAlignment::CENTER)];

        $index = 0;

        (clone $query)
            ->with('donations')
            ->orderByDesc('created_at')
            ->chunkById(200, function ($donors) use ($writer, &$index, $textStyles, $amountStyles, $centerStyles): void {
                foreach ($donors as $donor) {
                    /** @var Donor $donor */
                    $index++;
                    $zebra = ($index % 2) === 0 ? 1 : 0;

                    $text = $textStyles[$zebra];
                    $center = $centerStyles[$zebra];
                    $amount = $amountStyles[$zebra];

                    $donations = $donor->donations;
                    $count = $donations->count();
                    $sum = (float) $donations->sum('amount');
                    $first = $donations->min('donated_at');
                    $last = $donations->max('donated_at');

                    $cells = [
                        Cell::fromValue($index, $center),
                        Cell::fromValue($donor->first_name ?? '', $text),
                        Cell::fromValue($donor->last_name ?? '', $text),
                        Cell::fromValue($donor->phone ?? '', $text),
                        Cell::fromValue($donor->email ?? '', $text),
                        Cell::fromValue($donor->city ?? '', $text),
                        Cell::fromValue($donor->country ?? '', $text),
                        Cell::fromValue($donor->address ?? '', $text),
                        Cell::fromValue($count, $center),
                        Cell::fromValue($sum, $amount),
                        Cell::fromValue(self::formatDate($first), $center),
                        Cell::fromValue(self::formatDate($last), $center),
                        Cell::fromValue($donor->created_at?->format('d.m.Y') ?? '', $center),
                    ];

                    $writer->addRow(new Row($cells));
                }
            });
    }

    /**
     * @param  array<string, mixed>  $totalsByCurrency
     */
    private static function writeTotalsRow(Writer $writer, int $totalDonationCount, float $totalAmount, array $totalsByCurrency, int $lastColumn): void
    {
        $labelStyle = self::totalsLabelStyle();
        $amountStyle = self::totalsAmountStyle();
        $cellStyle = self::totalsCellStyle();

        $currencyParts = [];
        foreach ($totalsByCurrency as $currency => $sum) {
            $currencyParts[] = number_format((float) $sum, 2, ',', '.') . ' ' . ($currency ?: 'TRY');
        }
        $currencySummary = $currencyParts === [] ? 'TRY' : implode('  |  ', $currencyParts);

        $cells = [Cell::fromValue('GENEL TOPLAM', $labelStyle)];
        for ($i = 1; $i < self::COUNT_COLUMN_INDEX; $i++) {
            $cells[] = Cell::fromValue('', $labelStyle);
        }
        // Bağış Sayısı toplamı
        $cells[] = Cell::fromValue($totalDonationCount, $cellStyle);
        // Toplam Tutar
        $cells[] = Cell::fromValue($totalAmount, $amountStyle);
        // İlk/Son bağış: para birimi özeti İlk Bağış sütununda gösterilir
        $cells[] = Cell::fromValue($currencySummary, $cellStyle);
        for ($i = self::AMOUNT_COLUMN_INDEX + 2; $i <= $lastColumn; $i++) {
            $cells[] = Cell::fromValue('', $cellStyle);
        }

        $writer->addRow(new Row($cells));
    }

    private static function formatDate(mixed $value): string
    {
        if ($value instanceof Carbon) {
            return $value->format('d.m.Y');
        }

        if (is_string($value) && $value !== '') {
            return Carbon::parse($value)->format('d.m.Y');
        }

        return '';
    }
}
