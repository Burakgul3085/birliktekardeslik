<?php

namespace App\Filament\Crm\Resources\DocumentTemplates\Pages;

use App\Filament\Crm\Resources\DocumentTemplates\DocumentTemplateResource;
use App\Support\Crm\TemplateEngine\TemplateCoordinateHelper;
use App\Support\Crm\TemplateEngine\TemplateFieldCatalog;
use App\Support\Crm\TemplateEngine\TemplateFieldNormalizer;
use App\Support\Crm\TemplateEngine\TemplateFieldSynchronizer;
use App\Support\Crm\TemplateEngine\TemplateRenderEngine;
use App\Support\Crm\TemplateEngine\TemplateSampleValues;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class DesignDocumentTemplate extends Page
{
    use InteractsWithRecord;

    protected static string $resource = DocumentTemplateResource::class;

    protected string $view = 'filament.crm.document-templates.design';

    protected static ?string $title = 'Şablon Düzenleyici';

    /** @var array<int, array<string, mixed>> */
    public array $fields = [];

    public int $canvasWidth = 2480;

    public int $canvasHeight = 3508;

    public ?string $selectedFieldId = null;

    public ?string $backgroundUrl = null;

    public ?string $previewDataUri = null;

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        if (! $this->record->background_image) {
            Notification::make()
                ->title('Önce şablon görseli yükleyin')
                ->warning()
                ->send();

            $this->redirect(DocumentTemplateResource::getUrl('edit', ['record' => $this->record]));

            return;
        }

        $this->record->syncCanvasDimensions();
        $this->record->saveQuietly();

        app(TemplateFieldSynchronizer::class)->ensureFields($this->record);
        $this->record->load('fields');

        ['width' => $this->canvasWidth, 'height' => $this->canvasHeight] = $this->record->canvasSize();
        $this->fields = $this->record->fields->map(fn ($field) => $field->toRenderDefinition())->all();
        $this->backgroundUrl = asset('storage/' . $this->record->background_image);

        if ($this->fields !== []) {
            $this->selectedFieldId = $this->fields[0]['id'] ?? null;
        }
    }

    public function getTitle(): string|Htmlable
    {
        return 'Şablon Düzenleyici — ' . $this->record->name;
    }

    /**
     * @return array<string, string>
     */
    public function getFontOptions(): array
    {
        return \App\Support\Crm\TemplateEngine\FontRegistry::options();
    }

    /**
     * @return array<string, string>
     */
    public function getFieldKeyOptions(): array
    {
        return TemplateFieldCatalog::labelsForType($this->record->type);
    }

    public function save(): void
    {
        app(TemplateFieldSynchronizer::class)->persistFieldArrays(
            $this->record,
            TemplateFieldNormalizer::normalizeAll(array_values($this->fields)),
        );

        $this->record->syncCanvasDimensions();
        $this->record->saveQuietly();
        $this->record->load('fields');
        $this->fields = $this->record->fields->map(fn ($field) => $field->toRenderDefinition())->all();

        Notification::make()
            ->title('Şablon alanları kaydedildi')
            ->success()
            ->send();
    }

    public function renderPreview(): void
    {
        try {
            $engine = app(TemplateRenderEngine::class);
            $png = $engine->renderPng(
                $this->record,
                TemplateSampleValues::forType($this->record->type),
                TemplateFieldNormalizer::normalizeAll(array_values($this->fields)),
            );

            $this->previewDataUri = 'data:image/png;base64,' . base64_encode($png);

            Notification::make()
                ->title('Önizleme oluşturuldu')
                ->body('Aşağıdaki görüntü, gerçek render motoru ile üretilmiştir.')
                ->success()
                ->send();
        } catch (\Throwable $exception) {
            Notification::make()
                ->title('Önizleme oluşturulamadı')
                ->body($exception->getMessage())
                ->danger()
                ->send();
        }
    }

    public function addField(string $key): void
    {
        $label = TemplateFieldCatalog::LABELS[$key] ?? $key;
        $type = $key === 'qr_code' ? 'qr' : 'text';
        $isSingleLine = TemplateFieldCatalog::isSingleLine($key);

        $field = TemplateFieldNormalizer::normalize([
            'id' => 'field_' . $key . '_' . Str::random(4),
            'key' => $key,
            'label' => $label,
            'type' => $type,
            'x' => (int) round($this->canvasWidth * 0.15),
            'y' => (int) round($this->canvasHeight * 0.4),
            'width' => (int) round($this->canvasWidth * 0.7),
            'height' => $type === 'qr' ? 200 : 120,
            'font_family' => 'DejaVuSerif-Bold',
            'font_size' => 40,
            'color' => '#1B3A6B',
            'line_height' => 1.5,
            'max_lines' => $isSingleLine ? 1 : 8,
            'auto_resize' => true,
            'word_wrap' => ! $isSingleLine,
        ]);

        $field = TemplateCoordinateHelper::attachRatios($field, $this->canvasWidth, $this->canvasHeight);

        $this->fields[] = $field;
        $this->selectedFieldId = $field['id'];
    }

    public function removeField(string $fieldId): void
    {
        $this->fields = array_values(array_filter(
            $this->fields,
            fn (array $field): bool => ($field['id'] ?? '') !== $fieldId,
        ));

        if ($this->selectedFieldId === $fieldId) {
            $this->selectedFieldId = $this->fields[0]['id'] ?? null;
        }
    }

    public function resetDefaults(): void
    {
        app(TemplateFieldSynchronizer::class)->seedDefaults($this->record);
        $this->record->load('fields');
        $this->fields = $this->record->fields->map(fn ($field) => $field->toRenderDefinition())->all();
        $this->selectedFieldId = $this->fields[0]['id'] ?? null;
        $this->previewDataUri = null;

        Notification::make()
            ->title('Varsayılan alanlar yüklendi')
            ->body('Konumları düzenleyicide ince ayar yapıp kaydedin.')
            ->success()
            ->send();
    }

    public function selectField(string $fieldId): void
    {
        $this->selectedFieldId = $fieldId;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function updateFieldGeometry(string $fieldId, array $payload): void
    {
        foreach ($this->fields as $index => $field) {
            if (($field['id'] ?? '') !== $fieldId) {
                continue;
            }

            foreach (['x', 'y', 'width', 'height'] as $key) {
                if (array_key_exists($key, $payload)) {
                    $this->fields[$index][$key] = max(0, (int) $payload[$key]);
                }
            }

            $this->fields[$index] = TemplateCoordinateHelper::attachRatios(
                $this->fields[$index],
                $this->canvasWidth,
                $this->canvasHeight,
            );
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Geri')
                ->url(DocumentTemplateResource::getUrl('edit', ['record' => $this->record]))
                ->color('gray'),
            Action::make('preview')
                ->label('Örnek Veriyle Önizle')
                ->icon('heroicon-o-eye')
                ->action('renderPreview')
                ->color('info'),
            Action::make('reset')
                ->label('Varsayılanlara sıfırla')
                ->action('resetDefaults')
                ->color('warning')
                ->requiresConfirmation(),
            Action::make('save')
                ->label('Kaydet')
                ->action('save')
                ->color('primary'),
        ];
    }
}
