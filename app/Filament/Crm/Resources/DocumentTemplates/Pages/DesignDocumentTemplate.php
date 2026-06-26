<?php

namespace App\Filament\Crm\Resources\DocumentTemplates\Pages;

use App\Filament\Crm\Resources\DocumentTemplates\DocumentTemplateResource;
use App\Models\DocumentTemplate;
use App\Support\Crm\TemplateEngine\FontRegistry;
use App\Support\Crm\TemplateEngine\TemplateFieldDefaults;
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

        $this->record->syncCanvasFromBackground();
        $this->record->saveQuietly();

        ['width' => $this->canvasWidth, 'height' => $this->canvasHeight] = $this->record->canvasSize();
        $this->fields = $this->record->resolvedTemplateFields();
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
        return FontRegistry::options();
    }

    /**
     * @return array<string, string>
     */
    public function getFieldKeyOptions(): array
    {
        return TemplateFieldDefaults::FIELD_LABELS;
    }

    public function save(): void
    {
        $settings = $this->record->settings ?? [];
        $settings['canvas'] = [
            'width' => $this->canvasWidth,
            'height' => $this->canvasHeight,
        ];
        $settings['fields'] = array_values($this->fields);

        $this->record->update(['settings' => $settings]);

        Notification::make()
            ->title('Şablon alanları kaydedildi')
            ->success()
            ->send();
    }

    public function addField(string $key): void
    {
        $label = TemplateFieldDefaults::FIELD_LABELS[$key] ?? $key;
        $type = $key === 'qr_code' ? 'qr' : 'text';

        $field = [
            'id' => 'field_' . $key . '_' . Str::random(4),
            'key' => $key,
            'label' => $label,
            'type' => $type,
            'x' => (int) round($this->canvasWidth * 0.15),
            'y' => (int) round($this->canvasHeight * 0.4),
            'width' => (int) round($this->canvasWidth * 0.7),
            'height' => $type === 'qr' ? 200 : 120,
            'align' => 'center',
            'valign' => 'middle',
            'font_family' => 'DejaVuSerif-Bold',
            'font_size' => 40,
            'color' => '#1B3A6B',
            'line_height' => 1.5,
            'letter_spacing' => 0,
            'max_lines' => $type === 'qr' ? 1 : 5,
            'auto_shrink' => true,
            'word_wrap' => $key !== 'ad_soyad' && $key !== 'bagis_turu' && $key !== 'tarih',
        ];

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
        $this->fields = TemplateFieldDefaults::forType(
            $this->record->type,
            $this->canvasWidth,
            $this->canvasHeight,
        );
        $this->selectedFieldId = $this->fields[0]['id'] ?? null;

        Notification::make()
            ->title('Varsayılan alanlar yüklendi')
            ->body('Kaydet butonuna basarak kalıcı hale getirin.')
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
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Geri')
                ->url(DocumentTemplateResource::getUrl('edit', ['record' => $this->record]))
                ->color('gray'),
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
