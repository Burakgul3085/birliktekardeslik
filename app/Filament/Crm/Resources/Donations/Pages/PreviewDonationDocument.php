<?php

namespace App\Filament\Crm\Resources\Donations\Pages;

use App\Filament\Crm\Concerns\InteractsWithPosterToolbar;
use App\Filament\Crm\Resources\Donations\DonationResource;
use App\Models\DocumentFieldOverride;
use App\Models\DonationDocument;
use App\Support\Crm\DonationDocumentGenerator;
use App\Support\Crm\TemplateEngine\DocumentRenderService;
use App\Support\Crm\TemplateEngine\FontRegistry;
use App\Support\Crm\TemplateEngine\TemplateCoordinateHelper;
use App\Support\Crm\TemplateEngine\TemplateValueResolver;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Storage;

class PreviewDonationDocument extends Page
{
    use InteractsWithRecord;
    use InteractsWithPosterToolbar;

    protected static string $resource = DonationResource::class;

    protected static ?string $title = 'Belge Önizleme';

    protected string $view = 'filament.crm.donations.preview-document';

    public ?DonationDocument $previewDocument = null;

    /** @var array<string, array<string, mixed>> */
    public array $fieldStates = [];

    public ?string $selectedFieldKey = null;

    public ?string $previewDataUri = null;

    public int $canvasWidth = 2480;

    public int $canvasHeight = 3508;

    public ?string $backgroundUrl = null;

    /** @var array<string, string> */
    public array $fieldValues = [];

    public function mount(int|string $record, int $documentId): void
    {
        $this->record = $this->resolveRecord($record);

        $this->previewDocument = DonationDocument::query()
            ->with(['template.fields', 'fieldOverrides', 'donation.donor', 'donation.donationType'])
            ->where('donation_id', $this->record->id)
            ->findOrFail($documentId);

        if (! $this->previewDocument->isPoster()) {
            abort(404);
        }

        if (! $this->previewDocument->template) {
            Notification::make()
                ->title('Şablon bulunamadı')
                ->body('Bu belge için aktif afiş şablonu tanımlı değil.')
                ->danger()
                ->send();

            return;
        }

        $this->loadFieldStates();
        $this->loadCanvasContext();

        if ($this->fieldStates !== []) {
            $this->selectedFieldKey = array_key_first($this->fieldStates);
            $this->syncToolbarWithSelection();
        }
    }

    public function selectField(string $fieldKey): void
    {
        if (isset($this->fieldStates[$fieldKey])) {
            $this->selectedFieldKey = $fieldKey;
            $this->syncToolbarWithSelection();
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function updateFieldGeometry(string $fieldKey, array $payload): void
    {
        if (! isset($this->fieldStates[$fieldKey])) {
            return;
        }

        $states = $this->fieldStates;

        foreach (['x', 'y', 'width', 'height'] as $key) {
            if (array_key_exists($key, $payload)) {
                $states[$fieldKey][$key] = max($key === 'width' || $key === 'height' ? 24 : 0, (int) $payload[$key]);
            }
        }

        $this->fieldStates = $states;
    }

    public function updateFieldText(string $fieldKey, string $text): void
    {
        if (! isset($this->fieldStates[$fieldKey])) {
            return;
        }

        $states = $this->fieldStates;
        $states[$fieldKey]['text_override'] = trim($text);
        $this->fieldStates = $states;
    }

    protected function pushToolbarToField(): void
    {
        if (! $this->selectedFieldKey || ! isset($this->fieldStates[$this->selectedFieldKey])) {
            return;
        }

        $states = $this->fieldStates;
        $states[$this->selectedFieldKey] = array_merge(
            $states[$this->selectedFieldKey],
            $this->toolbarPatch(),
        );
        $this->fieldStates = $states;
    }

    protected function syncToolbarWithSelection(): void
    {
        if (! $this->selectedFieldKey || ! isset($this->fieldStates[$this->selectedFieldKey])) {
            return;
        }

        $this->pullToolbarFromField($this->fieldStates[$this->selectedFieldKey]);
    }

    public function getTitle(): string|Htmlable
    {
        if (! $this->previewDocument) {
            return 'Belge Önizleme';
        }

        return 'Belge Önizleme — ' . $this->previewDocument->type_label;
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
    public function getWebFontFamily(string $fontFamily): string
    {
        return match ($fontFamily) {
            'DejaVuSerif', 'DejaVuSerif-Bold' => "'Lora', Georgia, serif",
            default => "'Inter', system-ui, sans-serif",
        };
    }

    public function getDisplayText(string $fieldKey): string
    {
        $override = trim((string) ($this->fieldStates[$fieldKey]['text_override'] ?? ''));

        if ($override !== '') {
            return $override;
        }

        return $this->fieldValues[$fieldKey] ?? '';
    }

    public function refreshPreview(): void
    {
        if (! $this->previewDocument) {
            return;
        }

        $this->pushToolbarToField();

        try {
            $this->persistOverridesToDatabase(false);
            $this->previewDocument->load('fieldOverrides');

            $result = app(DocumentRenderService::class)->renderForDocument($this->previewDocument);
            $this->previewDataUri = 'data:image/png;base64,' . base64_encode($result['png']);
        } catch (\Throwable $exception) {
            Notification::make()
                ->title('Önizleme oluşturulamadı')
                ->body($exception->getMessage())
                ->danger()
                ->send();
        }
    }

    public function saveDocument(): void
    {
        if (! $this->previewDocument) {
            return;
        }

        $this->pushToolbarToField();

        $this->persistOverridesToDatabase(true);
        app(DonationDocumentGenerator::class)->rerender($this->previewDocument->fresh());
        $this->refreshPreview();

        Notification::make()->title('Belge ayarları kaydedildi')->success()->send();
    }

    public function applyToTemplate(): void
    {
        if (! $this->previewDocument?->template) {
            return;
        }

        $template = $this->previewDocument->template;
        $canvas = $template->canvasSize();

        foreach ($this->fieldStates as $fieldKey => $state) {
            $field = $template->fields()->where('field_key', $fieldKey)->first();

            if (! $field) {
                continue;
            }

            $attributes = [
                'x' => (int) ($state['x'] ?? $field->x),
                'y' => (int) ($state['y'] ?? $field->y),
                'width' => (int) ($state['width'] ?? $field->width),
                'height' => (int) ($state['height'] ?? $field->height),
                'font_family' => (string) ($state['font_family'] ?? $field->font_family),
                'font_size' => (int) ($state['font_size'] ?? $field->font_size),
                'color' => (string) ($state['color'] ?? $field->color),
                'align' => (string) ($state['align'] ?? $field->align),
                'vertical_align' => (string) ($state['vertical_align'] ?? $field->vertical_align),
            ];

            $attributes = TemplateCoordinateHelper::attachRatios($attributes, $canvas['width'], $canvas['height']);
            $field->update($attributes);
        }

        Notification::make()
            ->title('Ayarlar şablona uygulandı')
            ->body('Bundan sonra bu şablondan üretilen yeni belgeler bu konumları kullanacak.')
            ->success()
            ->send();
    }

    public function finalizeAndDownload(): mixed
    {
        if (! $this->previewDocument) {
            return null;
        }

        $this->persistOverridesToDatabase(true);
        $document = app(DonationDocumentGenerator::class)->finalize($this->previewDocument->fresh());

        if (! Storage::disk('public')->exists($document->pdf_path)) {
            Notification::make()->title('PDF oluşturulamadı')->danger()->send();

            return null;
        }

        Notification::make()->title('Belge onaylandı')->success()->send();

        return Storage::disk('public')->download(
            $document->pdf_path,
            $document->type . '-' . $document->verification_code . '.pdf',
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Bağışa dön')
                ->url(DonationResource::getUrl('edit', ['record' => $this->record]))
                ->color('gray'),
            Action::make('refresh')
                ->label('PDF render önizleme')
                ->icon('heroicon-o-eye')
                ->action(fn () => $this->refreshPreview())
                ->color('info'),
            Action::make('save')
                ->label('Belgeye kaydet')
                ->action(fn () => $this->saveDocument())
                ->color('primary'),
            Action::make('applyTemplate')
                ->label('Şablona uygula')
                ->action(fn () => $this->applyToTemplate())
                ->color('warning')
                ->requiresConfirmation(),
            Action::make('download')
                ->label('Onayla ve PDF indir')
                ->action(fn () => $this->finalizeAndDownload())
                ->color('success'),
        ];
    }

    private function loadFieldStates(): void
    {
        if (! $this->previewDocument?->template) {
            return;
        }

        $this->previewDocument->template->loadMissing('fields');
        $overrides = $this->previewDocument->fieldOverrides->keyBy('field_key');

        foreach ($this->previewDocument->template->fields as $field) {
            $override = $overrides->get($field->field_key);

            $this->fieldStates[$field->field_key] = [
                'label' => $field->label,
                'field_key' => $field->field_key,
                'type' => $field->field_type,
                'x' => $override?->x ?? $field->x,
                'y' => $override?->y ?? $field->y,
                'width' => $override?->width ?? $field->width,
                'height' => $override?->height ?? $field->height,
                'font_family' => $override?->font_family ?? $field->font_family,
                'font_size' => $override?->font_size ?? $field->font_size,
                'color' => $override?->color ?? $field->color,
                'align' => $override?->align ?? $field->align,
                'vertical_align' => $override?->vertical_align ?? $field->vertical_align,
                'text_override' => $override?->text_override ?? '',
            ];
        }
    }

    private function loadCanvasContext(): void
    {
        if (! $this->previewDocument?->template) {
            return;
        }

        ['width' => $this->canvasWidth, 'height' => $this->canvasHeight] = $this->previewDocument->template->canvasSize();

        if ($this->previewDocument->template->background_image) {
            $this->backgroundUrl = asset('storage/' . $this->previewDocument->template->background_image);
        }

        $this->fieldValues = TemplateValueResolver::forDonation(
            $this->previewDocument->donation,
            $this->previewDocument->template->type,
            $this->previewDocument->verification_url,
            $this->previewDocument->template,
        );
    }

    private function persistOverridesToDatabase(bool $notify): void
    {
        if (! $this->previewDocument) {
            return;
        }

        foreach ($this->fieldStates as $fieldKey => $state) {
            DocumentFieldOverride::query()->updateOrCreate(
                [
                    'donation_document_id' => $this->previewDocument->id,
                    'field_key' => $fieldKey,
                ],
                [
                    'x' => (int) ($state['x'] ?? 0),
                    'y' => (int) ($state['y'] ?? 0),
                    'width' => (int) ($state['width'] ?? 100),
                    'height' => (int) ($state['height'] ?? 50),
                    'font_family' => $state['font_family'] ?? null,
                    'font_size' => isset($state['font_size']) ? (int) $state['font_size'] : null,
                    'color' => $state['color'] ?? null,
                    'align' => $state['align'] ?? null,
                    'vertical_align' => $state['vertical_align'] ?? null,
                    'text_override' => filled($state['text_override'] ?? null) ? $state['text_override'] : null,
                ],
            );
        }

        if ($notify) {
            $this->previewDocument->load('fieldOverrides');
        }
    }
}
