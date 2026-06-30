<?php

namespace App\Filament\Crm\Resources\PosterTemplates\Pages;

use App\Filament\Crm\Resources\PosterTemplates\PosterTemplateResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditPosterTemplate extends EditRecord
{
    protected static string $resource = PosterTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('design')
                ->label('Tasarla')
                ->icon(Heroicon::OutlinedPaintBrush)
                ->color('primary')
                ->url(fn (): string => DesignPosterTemplate::getUrl(['record' => $this->record])),
            DeleteAction::make()->label('Sil'),
        ];
    }
}
