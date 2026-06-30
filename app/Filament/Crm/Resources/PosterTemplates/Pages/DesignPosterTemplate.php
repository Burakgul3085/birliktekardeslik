<?php

namespace App\Filament\Crm\Resources\PosterTemplates\Pages;

use App\Filament\Crm\Resources\PosterTemplates\PosterTemplateResource;
use App\Models\PosterTemplate;
use App\Support\Crm\PosterDataResolver;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;

class DesignPosterTemplate extends Page
{
    use InteractsWithRecord;

    protected static string $resource = PosterTemplateResource::class;

    protected string $view = 'filament.crm.poster.designer';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        abort_unless(auth('crm')->user()?->canWriteDonations() ?? false, 403);
    }

    public function getTitle(): string
    {
        return 'Afiş Tasarla: ' . $this->record->name;
    }

    /**
     * Tasarımcıya gönderilecek yapılandırma.
     *
     * @return array<string, mixed>
     */
    public function getDesignerConfig(): array
    {
        /** @var PosterTemplate $template */
        $template = $this->record;

        return [
            'mode' => 'design',
            'templateId' => $template->id,
            'backgroundUrl' => $template->background_url,
            'canvasWidth' => $template->canvas_width ?: null,
            'canvasHeight' => $template->canvas_height ?: null,
            'layout' => $template->layout ?? [],
            'placeholders' => PosterDataResolver::availablePlaceholders(),
            'fonts' => self::availableFonts(),
            'saveUrl' => null,
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function availableFonts(): array
    {
        return [
            'Inter',
            'Lora',
            'Montserrat',
            'Roboto',
            'Open Sans',
            'Poppins',
            'Playfair Display',
            'Merriweather',
            'Oswald',
            'Arial',
            'Georgia',
            'Times New Roman',
        ];
    }

    /**
     * Fabric.js editöründen gelen layout JSON'unu kaydeder.
     *
     * @param  array<int, mixed>  $layout
     */
    public function saveLayout(array $layout, int $width, int $height): void
    {
        abort_unless(auth('crm')->user()?->canWriteDonations() ?? false, 403);

        $this->record->update([
            'layout' => $layout,
            'canvas_width' => $width,
            'canvas_height' => $height,
        ]);

        Notification::make()
            ->title('Şablon kaydedildi')
            ->body('Bundan sonra üretilen afişler bu tasarımı kullanacak.')
            ->success()
            ->send();
    }
}
