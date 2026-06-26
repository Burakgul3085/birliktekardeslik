<?php

namespace App\Support\Crm;

use App\Models\Donation;
use Illuminate\Database\Eloquent\Builder;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DonationSpreadsheetExporter
{
    public static function download(Builder $query, ?string $filename = null): StreamedResponse
    {
        $filename ??= 'bagislar-' . now()->format('Y-m-d_His') . '.xlsx';

        return response()->streamDownload(function () use ($query): void {
            $writer = new Writer();
            $writer->openToFile('php://output');

            $writer->addRow(Row::fromValues([
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
                'Proje',
                'Açıklama',
                'Not',
                'Kaydeden',
                'Oluşturulma',
            ]));

            (clone $query)
                ->with(['donor', 'donationType', 'paymentMethod', 'project', 'creator'])
                ->orderByDesc('donated_at')
                ->chunkById(200, function ($donations) use ($writer): void {
                    foreach ($donations as $donation) {
                        /** @var Donation $donation */
                        $writer->addRow(Row::fromValues(self::rowValues($donation)));
                    }
                });

            $writer->close();
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * @return array<int, string>
     */
    private static function rowValues(Donation $donation): array
    {
        return [
            $donation->donation_number,
            $donation->receipt_number ?? '',
            $donation->donor?->first_name ?? '',
            $donation->donor?->last_name ?? '',
            $donation->donor?->phone ?? '',
            $donation->donor?->city ?? '',
            $donation->donationType?->name ?? '',
            $donation->paymentMethod?->name ?? '',
            (string) $donation->amount,
            $donation->currency,
            $donation->donated_at?->format('d.m.Y H:i') ?? '',
            $donation->project?->title ?? '',
            $donation->description ?? '',
            $donation->notes ?? '',
            $donation->creator?->name ?? '',
            $donation->created_at?->format('d.m.Y H:i') ?? '',
        ];
    }
}
