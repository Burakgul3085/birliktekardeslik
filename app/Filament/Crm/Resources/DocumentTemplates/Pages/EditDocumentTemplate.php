<?php

namespace App\Filament\Crm\Resources\DocumentTemplates\Pages;

use App\Filament\Crm\Resources\DocumentTemplates\DocumentTemplateResource;
use App\Models\DocumentTemplate;
use App\Support\Crm\TemplateEngine\TemplateFieldSynchronizer;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditDocumentTemplate extends EditRecord
{
    protected static string $resource = DocumentTemplateResource::class;

    protected ?int $canvasWidthBefore = null;

    protected ?int $canvasHeightBefore = null;

    protected ?string $backgroundImageBefore = null;

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

    protected function beforeSave(): void
    {
        $this->canvasWidthBefore = $this->record->canvas_width;
        $this->canvasHeightBefore = $this->record->canvas_height;
        $this->backgroundImageBefore = $this->record->background_image;
    }

    protected function afterSave(): void
    {
        $oldWidth = (int) ($this->canvasWidthBefore ?? 0);
        $oldHeight = (int) ($this->canvasHeightBefore ?? 0);

        $this->record->syncCanvasDimensions();
        $sync = app(TemplateFieldSynchronizer::class);

        $newWidth = (int) ($this->record->canvas_width ?? 0);
        $newHeight = (int) ($this->record->canvas_height ?? 0);

        if ($this->record->fields()->exists()) {
            if ($oldWidth > 0 && $oldHeight > 0 && ($newWidth !== $oldWidth || $newHeight !== $oldHeight)) {
                $sync->rescaleFieldsToCanvas($this->record, $oldWidth, $oldHeight);

                Notification::make()
                    ->title('Şablon boyutu değişti')
                    ->body('Alan koordinatları yeni PNG boyutuna orantılı güncellendi.')
                    ->success()
                    ->send();
            } else {
                $sync->reapplyFieldsFromRatios($this->record);
            }
        }

        $this->record->saveQuietly();
        $sync->ensureFields($this->record);

        if ($this->backgroundImageBefore !== $this->record->background_image && filled($this->record->background_image)) {
            Notification::make()
                ->title('Şablon görseli güncellendi')
                ->body('Alan konumlarını Şablon Düzenleyicide hizalayıp Kaydet\'e basın.')
                ->success()
                ->send();
        }
    }
}
