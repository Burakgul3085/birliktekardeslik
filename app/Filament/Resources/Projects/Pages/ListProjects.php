<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\ActivitySectionSettings\ActivitySectionSettingResource;
use App\Filament\Resources\Projects\ProjectResource;
use App\Models\ActivitySectionSetting;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        $sectionSetting = ActivitySectionSetting::query()->firstOrCreate(
            ['title' => 'Faaliyetlerimiz'],
            [
                'badge_text' => 'Birlikte Kardeşlik Derneği',
                'description' => 'Afrika’da açlık ve susuzlukla mücadele için yürüttüğümüz gıda, temiz su ve acil yardım faaliyetleri.',
                'is_active' => true,
            ]
        );

        return [
            Action::make('homeActivitySection')
                ->label('Ana Sayfa Faaliyet Alanı')
                ->icon('heroicon-o-pencil-square')
                ->color('gray')
                ->url(ActivitySectionSettingResource::getUrl('edit', ['record' => $sectionSetting])),
            CreateAction::make(),
        ];
    }
}
