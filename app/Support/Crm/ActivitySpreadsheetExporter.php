<?php

namespace App\Support\Crm;

use App\Models\Donation;
use App\Models\Setting;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ActivitySpreadsheetExporter extends CorporateSpreadsheet
{
    private const PROJECT_HEADERS = ['Sıra', 'Proje / Faaliyet', 'Bağış Adedi', 'Toplam Tutar (TRY)', 'Ort. Bağış (TRY)'];

    private const TYPE_HEADERS = ['Sıra', 'Bağış Türü', 'Bağış Adedi', 'Toplam Tutar (TRY)'];

    private const DETAIL_HEADERS = [
        'Sıra',
        'Bağış No',
        'Makbuz No',
        'Bağışçı',
        'Tutar',
        'Para Birimi',
        'Bağış Tarihi',
        'Bağış Türü',
        'Açıklama',
    ];

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

        foreach (self::PROJECT_HEADERS as $i => $width) {
            $options->setColumnWidth(match ($i) {
                0 => 6.0,
                1 => 34.0,
                2 => 14.0,
                3 => 18.0,
                default => 16.0,
            }, $i + 1);
        }

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

        $titleRowCount = count($bands);
        for ($r = 1; $r <= $titleRowCount; $r++) {
            $options->mergeCells(0, $r, $lastColumn, $r);
        }

        foreach ($bands as [$value, $style]) {
            $writer->addRow(self::bandRow($value, $lastColumn, $style));
        }

        $writer->addRow(new Row([Cell::fromValue('', self::cellStyle(false))]));

        self::writeSectionTitle($writer, 'Proje / Faaliyet Özeti', $lastColumn);
        self::writeProjectTable($writer, $report, $lastColumn, $options);

        $writer->addRow(new Row([Cell::fromValue('', self::cellStyle(false))]));

        self::writeSectionTitle($writer, 'Bağış Türü Özeti (bilgi)', $lastColumn);
        self::writeTypeTable($writer, $report, $lastColumn);
    }

    private static function writeDetailSheet(Writer $writer, ActivityReportResult $report, Options $options): void
    {
        $lastColumn = count(self::DETAIL_HEADERS) - 1;
        $amountColumn = 4;

        foreach ([8.0, 18.0, 16.0, 24.0, 14.0, 12.0, 18.0, 18.0, 30.0] as $i => $width) {
            $options->setColumnWidth($width, $i + 1);
        }

        $metaLine = sprintf(
            'Faaliyet: %s          Dönem: %s          Toplam kayıt: %d',
            $report->meta['project_label'],
            $report->meta['period_label'],
            $report->detailTotalCount,
        );

        $bands = self::titleBands(Setting::current(), 'BAĞIŞ DETAY LİSTESİ', $metaLine);
        $titleRowCount = count($bands);

        for ($r = 1; $r <= $titleRowCount; $r++) {
            $options->mergeCells(0, $r, $lastColumn, $r);
        }

        foreach ($bands as [$value, $style]) {
            $writer->addRow(self::bandRow($value, $lastColumn, $style));
        }

        $headerCells = [];
        foreach (self::DETAIL_HEADERS as $header) {
            $headerCells[] = Cell::fromValue($header, self::headerStyle());
        }
        $writer->addRow(new Row($headerCells));

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
            $zebra = ($index % 2) === 0;
            $text = self::cellStyle($zebra);
            $center = self::cellStyle($zebra, CellAlignment::CENTER);
            $amountStyle = self::amountStyle($zebra);
            $totalAmount += (float) $donation->amount;

            $writer->addRow(new Row([
                Cell::fromValue($index, $center),
                Cell::fromValue($donation->donation_number ?? '', $text),
                Cell::fromValue($donation->receipt_number ?? '', $text),
                Cell::fromValue(trim(($donation->donor?->first_name ?? '') . ' ' . ($donation->donor?->last_name ?? '')), $text),
                Cell::fromValue((float) $donation->amount, $amountStyle),
                Cell::fromValue($donation->currency ?? 'TRY', $center),
                Cell::fromValue($donation->donated_at?->format('d.m.Y H:i') ?? '', $center),
                Cell::fromValue($donation->donationType?->name ?? '', $text),
                Cell::fromValue($donation->description ?? '', $text),
            ]));
        }

        $totalsRow = $titleRowCount + 2 + max($index, 0);
        $options->mergeCells(0, $totalsRow, $amountColumn - 1, $totalsRow);

        $cells = [Cell::fromValue('GENEL TOPLAM', self::totalsLabelStyle())];
        for ($i = 1; $i < $amountColumn; $i++) {
            $cells[] = Cell::fromValue('', self::totalsLabelStyle());
        }
        $cells[] = Cell::fromValue($totalAmount, self::totalsAmountStyle());
        for ($i = $amountColumn + 1; $i <= $lastColumn; $i++) {
            $cells[] = Cell::fromValue('', self::totalsCellStyle());
        }

        $writer->addRow(new Row($cells));
    }

    private static function writeSectionTitle(Writer $writer, string $title, int $lastColumn): void
    {
        $writer->addRow(self::bandRow($title, $lastColumn, self::subtitleStyle()));
    }

    private static function writeProjectTable(Writer $writer, ActivityReportResult $report, int $lastColumn, Options $options): void
    {
        $headerCells = [];
        foreach (self::PROJECT_HEADERS as $header) {
            $headerCells[] = Cell::fromValue($header, self::headerStyle());
        }
        $writer->addRow(new Row($headerCells));

        $index = 0;
        foreach ($report->projectRows as $row) {
            $index++;
            $zebra = ($index % 2) === 0;
            $writer->addRow(new Row([
                Cell::fromValue($index, self::cellStyle($zebra, CellAlignment::CENTER)),
                Cell::fromValue($row['label'], self::cellStyle($zebra)),
                Cell::fromValue($row['donation_count'], self::cellStyle($zebra, CellAlignment::CENTER)),
                Cell::fromValue($row['total_amount'], self::amountStyle($zebra)),
                Cell::fromValue($row['average_amount'], self::amountStyle($zebra)),
            ]));
        }

        $totalsRow = count($report->projectRows) + 8;
        $options->mergeCells(0, $totalsRow, 2, $totalsRow);

        $cells = [
            Cell::fromValue('GENEL TOPLAM', self::totalsLabelStyle()),
            Cell::fromValue('', self::totalsLabelStyle()),
            Cell::fromValue($report->summary['donation_count'], self::totalsCellStyle()),
            Cell::fromValue($report->summary['total_amount'], self::totalsAmountStyle()),
            Cell::fromValue('', self::totalsCellStyle()),
        ];
        $writer->addRow(new Row($cells));
    }

    private static function writeTypeTable(Writer $writer, ActivityReportResult $report, int $lastColumn): void
    {
        $headerCells = [];
        foreach (self::TYPE_HEADERS as $header) {
            $headerCells[] = Cell::fromValue($header, self::headerStyle());
        }
        $writer->addRow(new Row($headerCells));

        $index = 0;
        foreach ($report->typeRows as $row) {
            $index++;
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
