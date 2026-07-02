<?php

namespace App\Support\Crm;

use App\Models\Donation;
use App\Models\Setting;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ActivitySpreadsheetExporter extends CorporateSpreadsheet
{
    private const SUMMARY_SHEET = 0;

    private const DETAIL_SHEET = 1;

    private const PROJECT_HEADERS = ['Sıra', 'Proje / Faaliyet', 'Bağış Adedi', 'Toplam Tutar (TRY)', 'Ort. Bağış (TRY)'];

    private const DONOR_HEADERS = ['Sıra', 'Bağışçı', 'Bağış Adedi', 'Toplam Tutar (TRY)', 'Ort. Bağış (TRY)'];

    private const TYPE_HEADERS = ['Sıra', 'Bağış Türü', 'Bağış Adedi', 'Toplam Tutar (TRY)'];

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

        self::writeSummarySheet($writer, $report, $options);

        if ($report->meta['has_detail_sheet'] ?? false) {
            $writer->addNewSheetAndMakeItCurrent();
            self::writeDetailSheet($writer, $report, $options);
        }

        $writer->close();
    }

    private static function writeSummarySheet(Writer $writer, ActivityReportResult $report, Options $options): void
    {
        $lastColumn = count(self::PROJECT_HEADERS) - 1;

        foreach ([6.0, 34.0, 14.0, 18.0, 16.0] as $i => $width) {
            $options->setColumnWidth($width, $i + 1);
        }

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
            number_format($report->summary['total_amount'], 2, ',', '.'),
        );

        $bands = self::titleBands(Setting::current(), 'FAALİYET RAPORU', $metaLine);
        $bands[] = [$summaryLine, self::metaStyle()];

        foreach ($bands as [$value, $style]) {
            $writer->addRow(self::bandRow($value, $lastColumn, $style));
            $rowNum++;
            $options->mergeCells(0, $rowNum, $lastColumn, $rowNum, self::SUMMARY_SHEET);
        }

        self::spacer($writer, $rowNum);

        self::writeSectionTitle($writer, $rowNum, 'Proje / Faaliyet Özeti', $lastColumn);
        self::writeMetricTable(
            $writer,
            $rowNum,
            $options,
            self::PROJECT_HEADERS,
            $report->projectRows,
            $report->summary['donation_count'],
            $report->summary['total_amount'],
        );

        self::spacer($writer, $rowNum);

        self::writeSectionTitle($writer, $rowNum, 'Bağışçı Bazlı Özet', $lastColumn);
        self::writeMetricTable(
            $writer,
            $rowNum,
            $options,
            self::DONOR_HEADERS,
            $report->donorRows,
            $report->summary['donation_count'],
            $report->summary['total_amount'],
        );

        self::spacer($writer, $rowNum);

        self::writeSectionTitle($writer, $rowNum, 'Bağış Türü Özeti (bilgi)', $lastColumn);
        self::writeTypeTable($writer, $rowNum, $report, $lastColumn);
    }

    private static function writeDetailSheet(Writer $writer, ActivityReportResult $report, Options $options): void
    {
        $showProject = (bool) ($report->meta['show_project_column'] ?? false);

        $headers = ['Sıra', 'Bağış No', 'Makbuz No', 'Bağışçı', 'Telefon'];
        $widths = [8.0, 18.0, 16.0, 24.0, 16.0];

        if ($showProject) {
            $headers[] = 'Proje / Faaliyet';
            $widths[] = 26.0;
        }

        $headers = array_merge($headers, ['Tutar', 'Para Birimi', 'Bağış Tarihi', 'Bağış Türü', 'Açıklama']);
        $widths = array_merge($widths, [14.0, 12.0, 18.0, 18.0, 30.0]);

        $lastColumn = count($headers) - 1;
        $amountColumn = array_search('Tutar', $headers, true);

        foreach ($widths as $i => $width) {
            $options->setColumnWidth($width, $i + 1);
        }

        $rowNum = 0;

        $metaLine = sprintf(
            'Faaliyet: %s          Dönem: %s          Toplam kayıt: %d',
            $report->meta['project_label'],
            $report->meta['period_label'],
            $report->detailTotalCount,
        );

        $bands = self::titleBands(Setting::current(), 'BAĞIŞ DETAY LİSTESİ', $metaLine);

        foreach ($bands as [$value, $style]) {
            $writer->addRow(self::bandRow($value, $lastColumn, $style));
            $rowNum++;
            $options->mergeCells(0, $rowNum, $lastColumn, $rowNum, self::DETAIL_SHEET);
        }

        $headerCells = [];
        foreach ($headers as $header) {
            $headerCells[] = Cell::fromValue($header, self::headerStyle());
        }
        $writer->addRow(new Row($headerCells));
        $rowNum++;

        $builder = app(ActivityReportBuilder::class);
        $filters = ActivityReportFilterResolver::get();
        if (filled($report->meta['project_id'] ?? null)) {
            $filters['project_id'] = $report->meta['project_id'];
        }

        $index = 0;
        $totalAmount = 0.0;

        foreach ($builder->detailDonations($filters) as $donation) {
            /** @var Donation $donation */
            $index++;
            $rowNum++;
            $zebra = ($index % 2) === 0;
            $text = self::cellStyle($zebra);
            $center = self::cellStyle($zebra, CellAlignment::CENTER);
            $amountStyle = self::amountStyle($zebra);
            $totalAmount += (float) $donation->amount;

            $donorName = trim(($donation->donor?->first_name ?? '') . ' ' . ($donation->donor?->last_name ?? ''));

            $cells = [
                Cell::fromValue($index, $center),
                Cell::fromValue($donation->donation_number ?? '', $text),
                Cell::fromValue($donation->receipt_number ?? '', $text),
                Cell::fromValue($donorName !== '' ? $donorName : 'Bilinmeyen bağışçı', $text),
                Cell::fromValue($donation->donor?->phone ?? '', $text),
            ];

            if ($showProject) {
                $cells[] = Cell::fromValue($donation->project?->title ?? 'Proje atanmamış', $text);
            }

            $cells = array_merge($cells, [
                Cell::fromValue((float) $donation->amount, $amountStyle),
                Cell::fromValue($donation->currency ?? 'TRY', $center),
                Cell::fromValue($donation->donated_at?->format('d.m.Y H:i') ?? '', $center),
                Cell::fromValue($donation->donationType?->name ?? '', $text),
                Cell::fromValue($donation->description ?? '', $text),
            ]);

            $writer->addRow(new Row($cells));
        }

        $rowNum++;
        $cells = [Cell::fromValue('GENEL TOPLAM', self::totalsLabelStyle())];
        for ($i = 1; $i < $amountColumn; $i++) {
            $cells[] = Cell::fromValue('', self::totalsLabelStyle());
        }
        $cells[] = Cell::fromValue($totalAmount, self::totalsAmountStyle());
        for ($i = $amountColumn + 1; $i <= $lastColumn; $i++) {
            $cells[] = Cell::fromValue('', self::totalsCellStyle());
        }
        $writer->addRow(new Row($cells));
        $options->mergeCells(0, $rowNum, $amountColumn - 1, $rowNum, self::DETAIL_SHEET);
    }

    private static function spacer(Writer $writer, int &$rowNum): void
    {
        $writer->addRow(new Row([Cell::fromValue('', self::cellStyle(false))]));
        $rowNum++;
    }

    private static function writeSectionTitle(Writer $writer, int &$rowNum, string $title, int $lastColumn): void
    {
        $writer->addRow(self::bandRow($title, $lastColumn, self::subtitleStyle()));
        $rowNum++;
    }

    /**
     * @param  array<int, string>  $headers
     * @param  array<int, array{label: string, donation_count: int, total_amount: float, average_amount: float}>  $rows
     */
    private static function writeMetricTable(
        Writer $writer,
        int &$rowNum,
        Options $options,
        array $headers,
        array $rows,
        int $summaryCount,
        float $summaryTotal,
    ): void {
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
            Cell::fromValue($summaryCount, self::totalsCellStyle()),
            Cell::fromValue($summaryTotal, self::totalsAmountStyle()),
            Cell::fromValue('', self::totalsCellStyle()),
        ]));
        $options->mergeCells(0, $rowNum, 1, $rowNum, self::SUMMARY_SHEET);
    }

    private static function writeTypeTable(Writer $writer, int &$rowNum, ActivityReportResult $report, int $lastColumn): void
    {
        $headerCells = [];
        foreach (self::TYPE_HEADERS as $header) {
            $headerCells[] = Cell::fromValue($header, self::headerStyle());
        }
        $writer->addRow(new Row($headerCells));
        $rowNum++;

        $index = 0;
        foreach ($report->typeRows as $row) {
            $index++;
            $rowNum++;
            $zebra = ($index % 2) === 0;
            $cells = [
                Cell::fromValue($index, self::cellStyle($zebra, CellAlignment::CENTER)),
                Cell::fromValue($row['label'], self::cellStyle($zebra)),
                Cell::fromValue($row['donation_count'], self::cellStyle($zebra, CellAlignment::CENTER)),
                Cell::fromValue($row['total_amount'], self::amountStyle($zebra)),
            ];

            while (count($cells) <= $lastColumn) {
                $cells[] = Cell::fromValue('', self::cellStyle($zebra));
            }

            $writer->addRow(new Row($cells));
        }
    }
}
