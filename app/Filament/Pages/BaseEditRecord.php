<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

/**
 * Kayıt sonrası ilgili kaynağın liste sayfasına yönlendirir ve forma «Geri Dön» ekler.
 */
abstract class BaseEditRecord extends EditRecord
{
    protected function getRedirectUrl(): ?string
    {
        return static::getResource()::getUrl('index');
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            $this->getBackToIndexFormAction(),
            $this->getCancelFormAction(),
        ];
    }

    protected function getBackToIndexFormAction(): Action
    {
        $url = static::getResource()::getUrl('index');

        return Action::make('backToIndex')
            ->label('Geri Dön')
            ->icon('heroicon-o-arrow-left')
            ->url($url)
            ->color('gray');
    }
}
