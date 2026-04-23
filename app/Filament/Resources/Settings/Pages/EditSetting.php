<?php

namespace App\Filament\Resources\Settings\Pages;

use App\Filament\Resources\Settings\SettingResource;
use App\Support\DonationQrService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditSetting extends EditRecord
{
    protected static string $resource = SettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('qrYenile')
                ->label('QR Yenile')
                ->icon('heroicon-o-qr-code')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Bağış QR kodu yenilensin mi?')
                ->modalDescription('Ayarlar sayfasındaki Bağış Sayfası URL alanını güncellediyseniz, bu işlem QR kodunu yeni adrese göre üretir.')
                ->action(function (): void {
                    app(DonationQrService::class)->generate(force: true);

                    Notification::make()
                        ->title('Bağış QR kodu başarıyla yenilendi.')
                        ->success()
                        ->send();
                }),
            DeleteAction::make(),
        ];
    }
}
