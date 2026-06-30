<?php

namespace App\Support\Crm;

use App\Models\Donation;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\BorderPart;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\CellVerticalAlignment;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DonationSpreadsheetExporter
{
    /** Kurumsal renk paleti */
    private const BRAND_DARK = '0F766E';

    private const BRAND = '0D9488';

    private const BAND_LIGHT = 'CCFBF1';

    private const ZEBRA = 'F1F5F9';

    private const TOTALS_BG = 'E2E8F0';

    private const BORDER = 'CBD5E1';

    /** Sütun başlıkları */
    private const HEADERS = [
        'Sıra',
        'Bağış No',
        'Makbuz No',
        'Ad',
        'Soyad',
        'Telefon',
        'Şehir',
        'Bağış Türü',
        'Ödeme Türü',
        'Tutar',
        'Para Birimi',
        'Bağış Tarihi',
        'Proje / Faaliyet',
        'Açıklama',
        'Not',
        'Kaydeden',
        'Oluşturulma',
    ];

    /** 1 tabanlı sütun genişlikleri (A=1) */
    private const COLUMN_WIDTHS = [6, 18, 16, 16, 16, 16, 14, 18, 18, 14, 11, 18, 26, 30, 24, 16, 18];

    private const AMOUNT_COLUMN_INDEX = 9; // 0 tabanlı (Tutar)

    public static function download(Builder $query, ?string $filename = null): StreamedResponse
    {
        $filename ??= 'bagislar-' . now()->format('Y-m-d_His') . '.xlsx';

        return response()->streamDownload(function () use ($query): void {
            self::write($query);
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private static function write(Builder $query): void
    {
        $lastColumn = count(self::HEADERS) - 1; // 0 tabanlı son sütun

        // Özet veriler (başlık ve toplam satırı için önceden hesaplanır)
        $recordCount = (int) (clone $query)->count();
        $totalAmount = (float) (clone $query)->sum('amount');
        $totalsByCurrency = (clone $query)
            ->select('currency', DB::raw('SUM(amount) as total'))
            ->groupBy('currency')
            ->pluck('total', 'currency')
            ->all();

        $setting = Setting::current();
        $bands = self::titleBands($setting, $recordCount, $totalAmount);

        $options = new Options();
        foreach (self::COLUMN_WIDTHS as $i => $width) {
            $options->setColumnWidth((float) $width, $i + 1);
        }

        // Başlık bloğu (dinamik) + başlık satırı + veriler + toplam
        $titleRowCount = count($bands);
        $headerRow = $titleRowCount + 1;
        $firstDataRow = $headerRow + 1;
        $totalsRow = $firstDataRow + max($recordCount, 0);

        // Üst bilgi satırlarını yatay olarak birleştir
        for ($r = 1; $r <= $titleRowCount; $r++) {
            $options->mergeCells(0, $r, $lastColumn, $r);
        }
        // Toplam satırında etiket alanını birleştir (Sıra..Ödeme Türü)
        $options->mergeCells(0, $totalsRow, self::AMOUNT_COLUMN_INDEX - 1, $totalsRow);

        $writer = new Writer($options);
        $writer->openToFile('php://output');

        foreach ($bands as [$value, $style]) {
            $writer->addRow(self::bandRow($value, $lastColumn, $style));
        }

        self::writeHeaderRow($writer, $lastColumn);
        self::writeDataRows($writer, $query);
        self::writeTotalsRow($writer, $totalAmount, $totalsByCurrency, $lastColumn);

        $writer->close();
    }

    /**
     * @return array<int, array{0: string, 1: Style}>
     */
    private static function titleBands(Setting $setting, int $recordCount, float $totalAmount): array
    {
        $orgName = $setting->site_title ?: 'Birlikte Kardeşlik Derneği';

        $contactParts = array_filter([
            $setting->address,
            $setting->phone ? 'Tel: ' . $setting->phone : null,
            $setting->email,
        ]);

        $metaLine = sprintf(
            'Rapor Tarihi: %s          Kayıt Sayısı: %d          Toplam Tutar: %s TRY',
            now()->format('d.m.Y H:i'),
            $recordCount,
            number_format($totalAmount, 2, ',', '.'),
        );

        $bands = [[$orgName, self::titleStyle()]];

        if ($contactParts !== []) {
            $bands[] = [implode('   •   ', $contactParts), self::contactStyle()];
        }

        $bands[] = ['BAĞIŞ RAPORU', self::subtitleStyle()];
        $bands[] = [$metaLine, self::metaStyle()];

        return $bands;
    }

    private static function writeHeaderRow(Writer $writer, int $lastColumn): void
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
            ->with(['donor', 'donationType', 'paymentMethod', 'project', 'creator'])
            ->orderByDesc('donated_at')
            ->chunkById(200, function ($donations) use ($writer, &$index, $textStyles, $amountStyles, $centerStyles): void {
                foreach ($donations as $donation) {
                    /** @var Donation $donation */
                    $index++;
                    $zebra = ($index % 2) === 0 ? 1 : 0;

                    $text = $textStyles[$zebra];
                    $center = $centerStyles[$zebra];
                    $amount = $amountStyles[$zebra];

                    $cells = [
                        Cell::fromValue($index, $center),
                        Cell::fromValue($donation->donation_number ?? '', $text),
                        Cell::fromValue($donation->receipt_number ?? '', $text),
                        Cell::fromValue($donation->donor?->first_name ?? '', $text),
                        Cell::fromValue($donation->donor?->last_name ?? '', $text),
                        Cell::fromValue($donation->donor?->phone ?? '', $text),
                        Cell::fromValue($donation->donor?->city ?? '', $text),
                        Cell::fromValue($donation->donationType?->name ?? '', $text),
                        Cell::fromValue($donation->paymentMethod?->name ?? '', $text),
                        Cell::fromValue((float) $donation->amount, $amount),
                        Cell::fromValue($donation->currency ?? 'TRY', $center),
                        Cell::fromValue($donation->donated_at?->format('d.m.Y H:i') ?? '', $center),
                        Cell::fromValue($donation->project?->title ?? '', $text),
                        Cell::fromValue($donation->description ?? '', $text),
                        Cell::fromValue($donation->notes ?? '', $text),
                        Cell::fromValue($donation->creator?->name ?? '', $text),
                        Cell::fromValue($donation->created_at?->format('d.m.Y H:i') ?? '', $center),
                    ];

                    $writer->addRow(new Row($cells));
                }
            });
    }

    /**
     * @param  array<string, mixed>  $totalsByCurrency
     */
    private static function writeTotalsRow(Writer $writer, float $totalAmount, array $totalsByCurrency, int $lastColumn): void
    {
        $labelStyle = self::totalsLabelStyle();
        $amountStyle = self::totalsAmountStyle();
        $cellStyle = self::totalsCellStyle();

        $currencyParts = [];
        foreach ($totalsByCurrency as $currency => $sum) {
            $currencyParts[] = number_format((float) $sum, 2, ',', '.') . ' ' . ($currency ?: 'TRY');
        }
        $currencySummary = $currencyParts === [] ? 'TRY' : implode('  |  ', $currencyParts);

        $cells = [];
        // 0..(AMOUNT-1): birleştirilmiş etiket alanı
        $cells[] = Cell::fromValue('GENEL TOPLAM', $labelStyle);
        for ($i = 1; $i < self::AMOUNT_COLUMN_INDEX; $i++) {
            $cells[] = Cell::fromValue('', $labelStyle);
        }
        // Tutar
        $cells[] = Cell::fromValue($totalAmount, $amountStyle);
        // Para birimi özeti
        $cells[] = Cell::fromValue($currencySummary, $cellStyle);
        // Kalan sütunlar
        for ($i = self::AMOUNT_COLUMN_INDEX + 2; $i <= $lastColumn; $i++) {
            $cells[] = Cell::fromValue('', $cellStyle);
        }

        $writer->addRow(new Row($cells));
    }

    private static function bandRow(string $value, int $lastColumn, Style $style): Row
    {
        $cells = [Cell::fromValue($value, $style)];

        for ($i = 1; $i <= $lastColumn; $i++) {
            $cells[] = Cell::fromValue('', $style);
        }

        return new Row($cells);
    }

    private static function border(): Border
    {
        return new Border(
            new BorderPart(Border::TOP, self::BORDER, Border::WIDTH_THIN, Border::STYLE_SOLID),
            new BorderPart(Border::BOTTOM, self::BORDER, Border::WIDTH_THIN, Border::STYLE_SOLID),
            new BorderPart(Border::LEFT, self::BORDER, Border::WIDTH_THIN, Border::STYLE_SOLID),
            new BorderPart(Border::RIGHT, self::BORDER, Border::WIDTH_THIN, Border::STYLE_SOLID),
        );
    }

    private static function titleStyle(): Style
    {
        return (new Style())
            ->setFontBold()
            ->setFontSize(18)
            ->setFontColor('FFFFFF')
            ->setBackgroundColor(self::BRAND_DARK)
            ->setCellAlignment(CellAlignment::CENTER)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER);
    }

    private static function contactStyle(): Style
    {
        return (new Style())
            ->setFontSize(10)
            ->setFontColor('FFFFFF')
            ->setBackgroundColor(self::BRAND)
            ->setCellAlignment(CellAlignment::CENTER)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER);
    }

    private static function subtitleStyle(): Style
    {
        return (new Style())
            ->setFontBold()
            ->setFontSize(13)
            ->setFontColor(self::BRAND_DARK)
            ->setBackgroundColor(self::BAND_LIGHT)
            ->setCellAlignment(CellAlignment::CENTER)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER);
    }

    private static function metaStyle(): Style
    {
        return (new Style())
            ->setFontSize(10)
            ->setFontColor('334155')
            ->setBackgroundColor(self::BAND_LIGHT)
            ->setCellAlignment(CellAlignment::CENTER)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER);
    }

    private static function headerStyle(): Style
    {
        return (new Style())
            ->setFontBold()
            ->setFontSize(11)
            ->setFontColor('FFFFFF')
            ->setBackgroundColor(self::BRAND_DARK)
            ->setCellAlignment(CellAlignment::CENTER)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER)
            ->setShouldWrapText()
            ->setBorder(self::border());
    }

    private static function cellStyle(bool $zebra, string $alignment = CellAlignment::LEFT): Style
    {
        $style = (new Style())
            ->setFontSize(10)
            ->setFontColor('1E293B')
            ->setCellAlignment($alignment)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER)
            ->setBorder(self::border());

        if ($zebra) {
            $style->setBackgroundColor(self::ZEBRA);
        }

        return $style;
    }

    private static function amountStyle(bool $zebra): Style
    {
        $style = (new Style())
            ->setFontSize(10)
            ->setFontColor('1E293B')
            ->setCellAlignment(CellAlignment::RIGHT)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER)
            ->setFormat('#,##0.00')
            ->setBorder(self::border());

        if ($zebra) {
            $style->setBackgroundColor(self::ZEBRA);
        }

        return $style;
    }

    private static function totalsLabelStyle(): Style
    {
        return (new Style())
            ->setFontBold()
            ->setFontSize(11)
            ->setFontColor(self::BRAND_DARK)
            ->setBackgroundColor(self::TOTALS_BG)
            ->setCellAlignment(CellAlignment::RIGHT)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER)
            ->setBorder(self::border());
    }

    private static function totalsAmountStyle(): Style
    {
        return (new Style())
            ->setFontBold()
            ->setFontSize(11)
            ->setFontColor(self::BRAND_DARK)
            ->setBackgroundColor(self::TOTALS_BG)
            ->setCellAlignment(CellAlignment::RIGHT)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER)
            ->setFormat('#,##0.00')
            ->setBorder(self::border());
    }

    private static function totalsCellStyle(): Style
    {
        return (new Style())
            ->setFontBold()
            ->setFontSize(10)
            ->setFontColor(self::BRAND_DARK)
            ->setBackgroundColor(self::TOTALS_BG)
            ->setCellAlignment(CellAlignment::CENTER)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER)
            ->setBorder(self::border());
    }
}
