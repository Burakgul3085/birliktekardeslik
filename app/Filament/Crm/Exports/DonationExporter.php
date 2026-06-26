<?php

namespace App\Filament\Crm\Exports;

use App\Models\Donation;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class DonationExporter extends CrmExporter
{
    protected static ?string $model = Donation::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('donation_number')->label('Bağış No'),
            ExportColumn::make('receipt_number')->label('Makbuz No'),
            ExportColumn::make('donor.first_name')->label('Ad'),
            ExportColumn::make('donor.last_name')->label('Soyad'),
            ExportColumn::make('donor.phone')->label('Telefon'),
            ExportColumn::make('donor.city')->label('Şehir'),
            ExportColumn::make('donationType.name')->label('Bağış Türü'),
            ExportColumn::make('paymentMethod.name')->label('Ödeme Türü'),
            ExportColumn::make('amount')->label('Tutar'),
            ExportColumn::make('currency')->label('Para Birimi'),
            ExportColumn::make('donated_at')->label('Bağış Tarihi'),
            ExportColumn::make('project.title')->label('Proje'),
            ExportColumn::make('description')->label('Açıklama'),
            ExportColumn::make('notes')->label('Not'),
            ExportColumn::make('creator.name')->label('Kaydeden'),
            ExportColumn::make('created_at')->label('Oluşturulma'),
        ];
    }

    public static function getCompletedNotificationTitle(Export $export): string
    {
        return 'Dışa aktarma tamamlandı';
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = Number::format($export->successful_rows) . ' bağış kaydı dışa aktarıldı. İndirmek için aşağıdaki butona tıklayın.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' kayıtta hata oluştu.';
        }

        return $body;
    }
}
