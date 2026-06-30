<?php

namespace App\Filament\Crm\Resources\Donors\Pages;

use App\Filament\Crm\Resources\Donors\DonorResource;
use App\Support\Crm\DonorSpreadsheetExporter;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListDonors extends ListRecords
{
    protected static string $resource = DonorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportExcel')
                ->label('Excel Dışa Aktar')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('gray')
                ->action(function () {
                    $query = $this->getTableQueryForExport();

                    if (! $query->exists()) {
                        Notification::make()
                            ->title('Dışa aktarılacak bağışçı bulunamadı')
                            ->warning()
                            ->send();

                        return;
                    }

                    return DonorSpreadsheetExporter::download($query);
                }),
            CreateAction::make()->label('Yeni bağışçı'),
        ];
    }
}
