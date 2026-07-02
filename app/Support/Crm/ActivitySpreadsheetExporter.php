<?php

namespace App\Support\Crm;

use App\Models\Donation;
use App\Models\Setting;
use Illuminate\Support\Collection;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Faaliyet raporu için kapsamlı, çok sayfalı kurumsal Excel üretir.
 *
 * Uygulanan filtre (dönem + faaliyet) ne olursa olsun, aşağıdaki sayfalarda
 * o filtreye giren TÜM bağışların eksiksiz dökümü yer alır:
 *
 * Sayfa 1 - Özet: proje, bağışçı ve tür bazlı özet tabloları.
 * Sayfa 2 - Bağış Detayları: tüm bağışların tek listede satır satır dökümü.
 * Sayfa 3 - Proje Bazlı Kırılım: her faaliyet altında kim ne kadar bağış yapmış.
 * Sayfa 4 - Bağışçı Bazlı Kırılım: her bağışçının yaptığı tüm bağışların detayı.
 */
class ActivitySpreadsheetExporter extends CorporateSpreadsheet
{
    private const SHEET_SUMMARY = 0;

    private const SHEET_DETAIL = 1;

    private const SHEET_BY_PROJECT = 2;

    private const SHEET_BY_DONOR = 3;

    /** @var array<int, string> */
    private const METRIC_HEADERS_PROJECT = ['Sıra', 'Proje / Faaliyet', 'Bağış Adedi', 'Toplam Tutar (TRY)', 'Ort. Bağış (TRY)'];

    /** @var array<int, string> */
    private const METRIC_HEADERS_DONOR = ['Sıra', 'Bağışçı', 'Bağış Adedi', 'Toplam Tutar (TRY)', 'Ort. Bağış (TRY)'];

    /** @var array<int, string> */
    private const METRIC_HEADERS_TYPE = ['Sıra', 'Bağış Türü', 'Bağış Adedi', 'Toplam Tutar (TRY)', 'Ort. Bağış (TRY)'];

    /** @var array<int, float> */
    private const SUMMARY_WIDTHS = [6.0, 40.0, 14.0, 20.0, 18.0];

    /** @var array<int, string> */
    private const DETAIL_HEADERS = [
        'Sıra', 'Bağış No', 'Makbuz No', 'Bağışçı', 'Telefon',
        'Proje / Faaliyet', 'Bağış Türü', 'Ödeme Türü', 'Tutar', 'Para Birimi',
        'Bağış Tarihi', 'Açıklama',
    ];

    /** @var array<int, float> */
    private const DETAIL_WIDTHS = [6.0, 16.0, 16.0, 22.0, 15.0, 26.0, 18.0, 16.0, 14.0, 10.0, 18.0, 32.0];

    private const DETAIL_AMOUNT_COL = 8;

    /** @var array<int, string> */
    private const BY_PROJECT_HEADERS = [
        'Sıra', 'Bağışçı', 'Telefon', 'Bağış Türü', 'Ödeme Türü',
        'Tutar (TRY)', 'Bağış Tarihi', 'Makbuz No', 'Açıklama',
    ];

    /** @var array<int, float> */
    private const BY_PROJECT_WIDTHS = [6.0, 24.0, 15.0, 18.0, 16.0, 15.0, 18.0, 16.0, 34.0];

    private const BY_PROJECT_AMOUNT_COL = 5;

    /** @var array<int, string> */
    private const BY_DONOR_HEADERS = [
        'Sıra', 'Bağış No', 'Makbuz No', 'Proje / Faaliyet', 'Bağış Türü',
        'Ödeme Türü', 'Tutar (TRY)', 'Bağış Tarihi', 'Açıklama',
    ];

    /** @var array<int, float> */
    private const BY_DONOR_WIDTHS = [6.0, 16.0, 16.0, 26.0, 18.0, 16.0, 15.0, 18.0, 34.0];

    private const BY_DONOR_AMOUNT_COL = 6;

    public static function download(ActivityReportResult $report, ?string $filename = null): StreamedResponse
    {
        $filename ??= self::buildFilename($report);

        return response()->streamDownload(function () use ($report): void {
            self::write($report);
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private static function buildFilename(ActivityReportResult $report): string
    {
        $slug = $report->meta['project_slug'] ?? 'tum-faaliyetler';

        return 'faaliyet-raporu_' . $slug . '_' . now()->format('Y-m-d_His') . '.xlsx';
    }

    private static function write(ActivityReportResult $report): void
    {
        $options = new Options();
        $writer = new Writer($options);
        $writer->openToFile('php://output');

        $donations = self::resolveDonations($report);

        // Sayfa 1 — Özet
        $writer->getCurrentSheet()->setName('Özet');
        self::applyWidths($writer, self::SUMMARY_WIDTHS);
        self::writeSummarySheet($writer, $report, $options);

        // Sayfa 2 — Bağış Detayları (düz liste)
        $writer->addNewSheetAndMakeItCurrent();
        $writer->getCurrentSheet()->setName('Bağış Detayları');
        self::applyWidths($writer, self::DETAIL_WIDTHS);
        self::writeDetailSheet($writer, $report, $options, $donations);

        // Sayfa 3 — Proje Bazlı Kırılım
        $writer->addNewSheetAndMakeItCurrent();
        $writer->getCurrentSheet()->setName('Proje Bazlı Kırılım');
        self::applyWidths($writer, self::BY_PROJECT_WIDTHS);
        self::writeByProjectSheet($writer, $report, $options, $donations);

        // Sayfa 4 — Bağışçı Bazlı Kırılım
        $writer->addNewSheetAndMakeItCurrent();
        $writer->getCurrentSheet()->setName('Bağışçı Bazlı Kırılım');
        self::applyWidths($writer, self::BY_DONOR_WIDTHS);
        self::writeByDonorSheet($writer, $report, $options, $donations);

        $writer->close();
    }

    /**
     * @return Collection<int, Donation>
     */
    private static function resolveDonations(ActivityReportResult $report): Collection
    {
        $filters = ActivityReportFilterResolver::get();

        if (filled($report->meta['project_id'] ?? null)) {
            $filters['project_id'] = $report->meta['project_id'];
        }

        return app(ActivityReportBuilder::class)->detailDonations($filters);
    }

    /**
     * @param  array<int, float>  $widths
     */
    private static function applyWidths(Writer $writer, array $widths): void
    {
        $sheet = $writer->getCurrentSheet();

        foreach ($widths as $i => $width) {
            $sheet->setColumnWidth($width, $i + 1);
        }
    }

    // ---------------------------------------------------------------------
    // Sayfa 1 — Özet
    // ---------------------------------------------------------------------

    private static function writeSummarySheet(Writer $writer, ActivityReportResult $report, Options $options): void
    {
        $lastColumn = count(self::METRIC_HEADERS_PROJECT) - 1;
        $rowNum = 0;

        $metaLine = sprintf(
            'Dönem: %s          Faaliyet: %s          Oluşturma: %s          Hazırlayan: %s',
            $report->meta['period_label'],
            $report->meta['project_label'],
            $report->meta['generated_at'],
            $report->meta['generated_by'],
        );

        $summaryLine = sprintf(
            'Toplam Bağış Adedi: %d          Toplam Tutar: %s TRY',
            $report->summary['donation_count'],
            self::money($report->summary['total_amount']),
        );

        $bands = self::titleBands(Setting::current(), 'FAALİYET RAPORU', $metaLine);
        $bands[] = [$summaryLine, self::metaStyle()];

        foreach ($bands as [$value, $style]) {
            $writer->addRow(self::bandRow($value, $lastColumn, $style));
            $rowNum++;
            $options->mergeCells(0, $rowNum, $lastColumn, $rowNum, self::SHEET_SUMMARY);
        }

        self::spacer($writer, $rowNum);
        self::sectionTitle($writer, $rowNum, 'Proje / Faaliyet Özeti', $lastColumn);
        self::metricTable($writer, $rowNum, $options, self::METRIC_HEADERS_PROJECT, $report->projectRows, $report->summary, self::SHEET_SUMMARY);

        self::spacer($writer, $rowNum);
        self::sectionTitle($writer, $rowNum, 'Bağışçı Bazlı Özet', $lastColumn);
        self::metricTable($writer, $rowNum, $options, self::METRIC_HEADERS_DONOR, $report->donorRows, $report->summary, self::SHEET_SUMMARY);

        self::spacer($writer, $rowNum);
        self::sectionTitle($writer, $rowNum, 'Bağış Türü Özeti (bilgi)', $lastColumn);
        self::metricTable($writer, $rowNum, $options, self::METRIC_HEADERS_TYPE, $report->typeRows, $report->summary, self::SHEET_SUMMARY);
    }

    /**
     * @param  array<int, string>  $headers
     * @param  array<int, array{label: string, donation_count: int, total_amount: float, average_amount: float}>  $rows
     * @param  array{donation_count: int, total_amount: float}  $summary
     */
    private static function metricTable(Writer $writer, int &$rowNum, Options $options, array $headers, array $rows, array $summary, int $sheetIndex): void
    {
        $headerCells = [];
        foreach ($headers as $header) {
            $headerCells[] = Cell::fromValue($header, self::headerStyle());
        }
        $writer->addRow(new Row($headerCells));
        $rowNum++;

        $index = 0;
        foreach ($rows as $row) {
            $index++;
            $rowNum++;
            $zebra = ($index % 2) === 0;
            $writer->addRow(new Row([
                Cell::fromValue($index, self::cellStyle($zebra, CellAlignment::CENTER)),
                Cell::fromValue($row['label'], self::cellStyle($zebra)),
                Cell::fromValue($row['donation_count'], self::cellStyle($zebra, CellAlignment::CENTER)),
                Cell::fromValue($row['total_amount'], self::amountStyle($zebra)),
                Cell::fromValue($row['average_amount'], self::amountStyle($zebra)),
            ]));
        }

        $rowNum++;
        $writer->addRow(new Row([
            Cell::fromValue('GENEL TOPLAM', self::totalsLabelStyle()),
            Cell::fromValue('', self::totalsLabelStyle()),
            Cell::fromValue($summary['donation_count'], self::totalsCellStyle()),
            Cell::fromValue($summary['total_amount'], self::totalsAmountStyle()),
            Cell::fromValue('', self::totalsCellStyle()),
        ]));
        $options->mergeCells(0, $rowNum, 1, $rowNum, $sheetIndex);
    }

    // ---------------------------------------------------------------------
    // Sayfa 2 — Bağış Detayları (düz liste)
    // ---------------------------------------------------------------------

    /**
     * @param  Collection<int, Donation>  $donations
     */
    private static function writeDetailSheet(Writer $writer, ActivityReportResult $report, Options $options, Collection $donations): void
    {
        $lastColumn = count(self::DETAIL_HEADERS) - 1;
        $amountColumn = self::DETAIL_AMOUNT_COL;
        $rowNum = 0;

        $metaLine = sprintf(
            'Faaliyet: %s          Dönem: %s          Toplam kayıt: %d',
            $report->meta['project_label'],
            $report->meta['period_label'],
            $donations->count(),
        );

        foreach (self::titleBands(Setting::current(), 'BAĞIŞ DETAY LİSTESİ', $metaLine) as [$value, $style]) {
            $writer->addRow(self::bandRow($value, $lastColumn, $style));
            $rowNum++;
            $options->mergeCells(0, $rowNum, $lastColumn, $rowNum, self::SHEET_DETAIL);
        }

        $headerCells = [];
        foreach (self::DETAIL_HEADERS as $header) {
            $headerCells[] = Cell::fromValue($header, self::headerStyle());
        }
        $writer->addRow(new Row($headerCells));
        $rowNum++;

        $index = 0;
        $total = 0.0;

        foreach ($donations as $donation) {
            $index++;
            $rowNum++;
            $zebra = ($index % 2) === 0;
            $text = self::cellStyle($zebra);
            $center = self::cellStyle($zebra, CellAlignment::CENTER);
            $total += (float) $donation->amount;

            $writer->addRow(new Row([
                Cell::fromValue($index, $center),
                Cell::fromValue($donation->donation_number ?? '', $text),
                Cell::fromValue($donation->receipt_number ?? '', $text),
                Cell::fromValue(self::donorName($donation), $text),
                Cell::fromValue($donation->donor?->phone ?? '', $text),
                Cell::fromValue($donation->project?->title ?? 'Proje atanmamış', $text),
                Cell::fromValue($donation->donationType?->name ?? '', $text),
                Cell::fromValue($donation->paymentMethod?->name ?? '', $text),
                Cell::fromValue((float) $donation->amount, self::amountStyle($zebra)),
                Cell::fromValue($donation->currency ?? 'TRY', $center),
                Cell::fromValue($donation->donated_at?->format('d.m.Y H:i') ?? '', $center),
                Cell::fromValue($donation->description ?? '', $text),
            ]));
        }

        $rowNum++;
        self::totalsRow($writer, $lastColumn, $amountColumn, 'GENEL TOPLAM', $total);
        $options->mergeCells(0, $rowNum, $amountColumn - 1, $rowNum, self::SHEET_DETAIL);
    }

    // ---------------------------------------------------------------------
    // Sayfa 3 — Proje Bazlı Kırılım
    // ---------------------------------------------------------------------

    /**
     * @param  Collection<int, Donation>  $donations
     */
    private static function writeByProjectSheet(Writer $writer, ActivityReportResult $report, Options $options, Collection $donations): void
    {
        self::writeGroupedSheet(
            $writer,
            $options,
            self::SHEET_BY_PROJECT,
            'PROJE / FAALİYET BAZLI KIRILIM',
            sprintf('Her faaliyet altında kimin ne kadar bağış yaptığı gösterilir.          Dönem: %s', $report->meta['period_label']),
            $donations,
            self::BY_PROJECT_HEADERS,
            self::BY_PROJECT_AMOUNT_COL,
            fn (Donation $donation): string => $donation->project?->title ?? 'Proje atanmamış',
            fn (Donation $donation, bool $zebra): array => [
                Cell::fromValue(self::donorName($donation), self::cellStyle($zebra)),
                Cell::fromValue($donation->donor?->phone ?? '', self::cellStyle($zebra)),
                Cell::fromValue($donation->donationType?->name ?? '', self::cellStyle($zebra)),
                Cell::fromValue($donation->paymentMethod?->name ?? '', self::cellStyle($zebra)),
                Cell::fromValue((float) $donation->amount, self::amountStyle($zebra)),
                Cell::fromValue($donation->donated_at?->format('d.m.Y H:i') ?? '', self::cellStyle($zebra, CellAlignment::CENTER)),
                Cell::fromValue($donation->receipt_number ?? '', self::cellStyle($zebra)),
                Cell::fromValue($donation->description ?? '', self::cellStyle($zebra)),
            ],
        );
    }

    // ---------------------------------------------------------------------
    // Sayfa 4 — Bağışçı Bazlı Kırılım
    // ---------------------------------------------------------------------

    /**
     * @param  Collection<int, Donation>  $donations
     */
    private static function writeByDonorSheet(Writer $writer, ActivityReportResult $report, Options $options, Collection $donations): void
    {
        self::writeGroupedSheet(
            $writer,
            $options,
            self::SHEET_BY_DONOR,
            'BAĞIŞÇI BAZLI KIRILIM',
            sprintf('Her bağışçının yaptığı tüm bağışların detayı gösterilir.          Dönem: %s', $report->meta['period_label']),
            $donations,
            self::BY_DONOR_HEADERS,
            self::BY_DONOR_AMOUNT_COL,
            fn (Donation $donation): string => self::donorName($donation)
                . ($donation->donor?->phone ? ' (' . $donation->donor->phone . ')' : ''),
            fn (Donation $donation, bool $zebra): array => [
                Cell::fromValue($donation->donation_number ?? '', self::cellStyle($zebra)),
                Cell::fromValue($donation->receipt_number ?? '', self::cellStyle($zebra)),
                Cell::fromValue($donation->project?->title ?? 'Proje atanmamış', self::cellStyle($zebra)),
                Cell::fromValue($donation->donationType?->name ?? '', self::cellStyle($zebra)),
                Cell::fromValue($donation->paymentMethod?->name ?? '', self::cellStyle($zebra)),
                Cell::fromValue((float) $donation->amount, self::amountStyle($zebra)),
                Cell::fromValue($donation->donated_at?->format('d.m.Y H:i') ?? '', self::cellStyle($zebra, CellAlignment::CENTER)),
                Cell::fromValue($donation->description ?? '', self::cellStyle($zebra)),
            ],
        );
    }

    /**
     * Bağışları verilen anahtara göre gruplayıp, her grup için alt-başlık,
     * tablo başlığı, satırlar ve ara toplam yazar. En sonda genel toplam.
     *
     * @param  Collection<int, Donation>  $donations
     * @param  array<int, string>  $headers
     * @param  callable(Donation): string  $groupKey
     * @param  callable(Donation, bool): array<int, Cell>  $rowCells  Sıra sütunu HARİÇ hücreler (zebra durumuna göre stillenmiş).
     */
    private static function writeGroupedSheet(
        Writer $writer,
        Options $options,
        int $sheetIndex,
        string $title,
        string $metaLine,
        Collection $donations,
        array $headers,
        int $amountColumn,
        callable $groupKey,
        callable $rowCells,
    ): void {
        $lastColumn = count($headers) - 1;
        $rowNum = 0;

        foreach (self::titleBands(Setting::current(), $title, $metaLine) as [$value, $style]) {
            $writer->addRow(self::bandRow($value, $lastColumn, $style));
            $rowNum++;
            $options->mergeCells(0, $rowNum, $lastColumn, $rowNum, $sheetIndex);
        }

        $groups = $donations
            ->groupBy($groupKey)
            ->sortByDesc(fn (Collection $group): float => (float) $group->sum('amount'));

        $grandTotal = 0.0;

        foreach ($groups as $groupLabel => $group) {
            /** @var Collection<int, Donation> $group */
            $groupTotal = (float) $group->sum('amount');
            $grandTotal += $groupTotal;

            self::spacer($writer, $rowNum);

            $groupHeader = sprintf(
                '%s  —  %d bağış • %s TRY',
                $groupLabel,
                $group->count(),
                self::money($groupTotal),
            );
            $writer->addRow(self::bandRow($groupHeader, $lastColumn, self::subtitleStyle()));
            $rowNum++;
            $options->mergeCells(0, $rowNum, $lastColumn, $rowNum, $sheetIndex);

            $headerCells = [];
            foreach ($headers as $header) {
                $headerCells[] = Cell::fromValue($header, self::headerStyle());
            }
            $writer->addRow(new Row($headerCells));
            $rowNum++;

            $index = 0;
            foreach ($group->sortByDesc(fn (Donation $donation): float => (float) $donation->amount) as $donation) {
                $index++;
                $rowNum++;
                $zebra = ($index % 2) === 0;

                $cells = [Cell::fromValue($index, self::cellStyle($zebra, CellAlignment::CENTER))];

                foreach ($rowCells($donation, $zebra) as $cell) {
                    $cells[] = $cell;
                }

                $writer->addRow(new Row($cells));
            }

            $rowNum++;
            self::totalsRow($writer, $lastColumn, $amountColumn, 'ARA TOPLAM', $groupTotal);
            $options->mergeCells(0, $rowNum, $amountColumn - 1, $rowNum, $sheetIndex);
        }

        self::spacer($writer, $rowNum);
        $rowNum++;
        self::totalsRow($writer, $lastColumn, $amountColumn, 'GENEL TOPLAM', $grandTotal);
        $options->mergeCells(0, $rowNum, $amountColumn - 1, $rowNum, $sheetIndex);
    }

    // ---------------------------------------------------------------------
    // Ortak yardımcılar
    // ---------------------------------------------------------------------

    private static function totalsRow(Writer $writer, int $lastColumn, int $amountColumn, string $label, float $amount): void
    {
        $cells = [Cell::fromValue($label, self::totalsLabelStyle())];

        for ($i = 1; $i < $amountColumn; $i++) {
            $cells[] = Cell::fromValue('', self::totalsLabelStyle());
        }

        $cells[] = Cell::fromValue($amount, self::totalsAmountStyle());

        for ($i = $amountColumn + 1; $i <= $lastColumn; $i++) {
            $cells[] = Cell::fromValue('', self::totalsCellStyle());
        }

        $writer->addRow(new Row($cells));
    }

    private static function spacer(Writer $writer, int &$rowNum): void
    {
        $writer->addRow(new Row([Cell::fromValue('', self::cellStyle(false))]));
        $rowNum++;
    }

    private static function sectionTitle(Writer $writer, int &$rowNum, string $title, int $lastColumn): void
    {
        $writer->addRow(self::bandRow($title, $lastColumn, self::subtitleStyle()));
        $rowNum++;
    }

    private static function donorName(Donation $donation): string
    {
        $name = trim(($donation->donor?->first_name ?? '') . ' ' . ($donation->donor?->last_name ?? ''));

        return $name !== '' ? $name : 'Bilinmeyen bağışçı';
    }

    private static function money(float $amount): string
    {
        return number_format($amount, 2, ',', '.');
    }
}
