<?php

namespace App\Filament\Crm\Resources\Donations\Pages;

use App\Filament\Crm\Resources\Donations\DonationResource;
use App\Support\Crm\DonationSpreadsheetExporter;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListDonations extends ListRecords
{
    protected static string $resource = DonationResource::class;

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
                            ->title('Dışa aktarılacak bağış bulunamadı')
                            ->warning()
                            ->send();

                        return;
                    }

                    return DonationSpreadsheetExporter::download($query);
                }),
            CreateAction::make()->label('Yeni bağış'),
        ];
    }
}
