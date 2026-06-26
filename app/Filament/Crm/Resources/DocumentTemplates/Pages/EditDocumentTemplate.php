<?php

namespace App\Filament\Crm\Resources\DocumentTemplates\Pages;

use App\Filament\Crm\Resources\DocumentTemplates\DocumentTemplateResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditDocumentTemplate extends EditRecord
{
    protected static string $resource = DocumentTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('design')
                ->label('Şablon Düzenleyici')
                ->icon('heroicon-o-cursor-arrow-rays')
                ->url(fn (): string => DocumentTemplateResource::getUrl('design', ['record' => $this->record]))
                ->visible(fn (): bool => filled($this->record->background_image)),
            ...parent::getHeaderActions(),
        ];
    }

    protected function afterSave(): void
    {
        $this->record->syncCanvasFromBackground();
        $this->record->saveQuietly();
    }
}
