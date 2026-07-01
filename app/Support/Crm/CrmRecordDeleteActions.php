<?php

namespace App\Support\Crm;

use Closure;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;

class CrmRecordDeleteActions
{
    public static function make(
        Closure $authorize,
        ?Closure $visible = null,
        string $heading = 'Kaydı sil',
        string $description = 'Bu kayıt kalıcı olarak silinecek. Bu işlem geri alınamaz.',
        string $successTitle = 'Kayıt silindi',
    ): DeleteAction {
        $can = $visible ?? $authorize;

        return DeleteAction::make()
            ->label('Sil')
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading($heading)
            ->modalDescription($description)
            ->successNotificationTitle($successTitle)
            ->visible($can)
            ->authorize($authorize);
    }

    public static function makeBulk(
        Closure $authorize,
        ?Closure $visible = null,
        string $label = 'Seçilenleri sil',
        string $heading = 'Seçilen kayıtları sil',
        string $description = 'Seçili kayıtlar kalıcı olarak silinecek. Bu işlem geri alınamaz.',
        string $successTitle = 'Kayıtlar silindi',
    ): DeleteBulkAction {
        $can = $visible ?? $authorize;

        return DeleteBulkAction::make()
            ->label($label)
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading($heading)
            ->modalDescription($description)
            ->successNotificationTitle($successTitle)
            ->visible($can)
            ->authorize($authorize);
    }
}
