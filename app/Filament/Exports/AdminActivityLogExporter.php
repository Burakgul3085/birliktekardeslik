<?php

namespace App\Filament\Exports;

use App\Models\AdminActivityLog;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class AdminActivityLogExporter extends Exporter
{
    protected static ?string $model = AdminActivityLog::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('created_at')->label('Tarih'),
            ExportColumn::make('causer.name')->label('Kullanıcı'),
            ExportColumn::make('event_type')->label('Olay Tipi'),
            ExportColumn::make('description')->label('Aksiyon'),
            ExportColumn::make('subject_type')->label('Modül'),
            ExportColumn::make('subject_id')->label('Modül ID'),
            ExportColumn::make('route_name')->label('Rota'),
            ExportColumn::make('url')->label('URL'),
            ExportColumn::make('method')->label('Yöntem'),
            ExportColumn::make('ip_address')->label('IP'),
            ExportColumn::make('properties')->label('Değişiklik JSON'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Admin log disa aktarma tamamlandi. ' . Number::format($export->successful_rows) . ' kayit disa aktarildi.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' kayitta hata olustu.';
        }

        return $body;
    }
}
