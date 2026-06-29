<?php

namespace App\Filament\Crm\Resources\Donations\Pages;

use App\Filament\Crm\Resources\Donations\DonationResource;
use App\Models\DocumentFieldOverride;
use App\Models\DonationDocument;
use App\Support\Crm\DonationDocumentGenerator;
use App\Support\Crm\TemplateEngine\DocumentRenderService;
use App\Support\Crm\TemplateEngine\FontRegistry;
use App\Support\Crm\TemplateEngine\TemplateCoordinateHelper;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Storage;

class PreviewDonationDocument extends Page
{
    use InteractsWithRecord;

    protected static string $resource = DonationResource::class;

    protected static ?string $title = 'Belge Önizleme';

    protected string $view = 'filament.crm.donations.preview-document';

    public ?DonationDocument $previewDocument = null;

    /** @var array<string, array<string, mixed>> */
    public array $fieldStates = [];

    public ?string $previewDataUri = null;

    public function mount(int|string $record, int $documentId): void
    {
        $this->record = $this->resolveRecord($record);

        $this->previewDocument = DonationDocument::query()
            ->with(['template.fields', 'fieldOverrides'])
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

    public function refreshPreview(): void
    {
        if (! $this->previewDocument) {
            return;
        }

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
                ->label('Önizlemeyi yenile')
                ->action('refreshPreview')
                ->color('info'),
            Action::make('save')
                ->label('Belgeye kaydet')
                ->action('saveDocument')
                ->color('primary'),
            Action::make('applyTemplate')
                ->label('Şablona uygula')
                ->action('applyToTemplate')
                ->color('warning')
                ->requiresConfirmation(),
            Action::make('download')
                ->label('Onayla ve PDF indir')
                ->action('finalizeAndDownload')
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
