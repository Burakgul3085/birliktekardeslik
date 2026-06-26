<?php

namespace App\Filament\Crm\Resources\Donations\Pages;

use App\Filament\Crm\Exports\DonationExporter;
use App\Filament\Crm\Resources\Donations\DonationResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Resources\Pages\ListRecords;

class ListDonations extends ListRecords
{
    protected static string $resource = DonationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make()
                ->label('Excel Dışa Aktar')
                ->exporter(DonationExporter::class)
                ->authGuard('crm')
                ->formats([ExportFormat::Xlsx])
                ->columnMapping(false),
            CreateAction::make()->label('Yeni bağış'),
        ];
    }
}
