<?php

namespace App\Filament\Resources\AdminActivityLogs\Pages;

use App\Filament\Resources\AdminActivityLogs\AdminActivityLogResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListAdminActivityLogs extends ListRecords
{
    protected static string $resource = AdminActivityLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('quick_suspicious')
                ->label('Şüpheli Kullanıcı İşlem Yoğunluğu')
                ->color(request()->query('quick') === 'suspicious' ? 'danger' : 'gray')
                ->url(static::getUrl(['quick' => 'suspicious'])),
            Action::make('quick_changes')
                ->label('Sadece Değişiklik İşleri')
                ->color(request()->query('quick') === 'changes' ? 'warning' : 'gray')
                ->url(static::getUrl(['quick' => 'changes'])),
            Action::make('quick_today_logins')
                ->label('Bugünün Girişleri')
                ->color(request()->query('quick') === 'today_logins' ? 'info' : 'gray')
                ->url(static::getUrl(['quick' => 'today_logins'])),
            Action::make('quick_clear')
                ->label('Hızlı Filtreyi Temizle')
                ->color('primary')
                ->url(static::getUrl()),
        ];
    }
}
