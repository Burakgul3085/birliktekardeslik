<?php

namespace App\Support\Crm;

use App\Models\DocumentTemplate;
use App\Support\Crm\TemplateEngine\BlankPosterCanvasBuilder;
use App\Support\Crm\TemplateEngine\TemplateFieldSynchronizer;
use Illuminate\Support\Facades\Storage;

class PosterTemplateInstaller
{
    public function __construct(
        private readonly BlankPosterCanvasBuilder $canvasBuilder = new BlankPosterCanvasBuilder(),
        private readonly TemplateFieldSynchronizer $fieldSynchronizer = new TemplateFieldSynchronizer(),
    ) {}

    /**
     * @return array<string, DocumentTemplate>
     */
    public function install(bool $replaceImages = false): array
    {
        $installed = [];

        foreach ($this->definitions() as $type => $definition) {
            $installed[$type] = $this->installTemplate($type, $definition, $replaceImages);
        }

        return $installed;
    }

    /**
     * @param  array<string, mixed>  $definition
     */
    private function installTemplate(string $type, array $definition, bool $replaceImages): DocumentTemplate
    {
        $record = DocumentTemplate::query()->updateOrCreate(
            ['type' => $type],
            [
                'name' => $definition['name'],
                'blade_view' => 'template_engine',
                'is_default' => true,
                'is_active' => true,
                'sort_order' => $definition['sort_order'],
                'message_template' => $definition['message_template'] ?? null,
            ],
        );

        $relativePath = 'crm/templates/' . $type . '-blank-v2.png';
        $needsImage = $replaceImages || blank($record->background_image) || ! Storage::disk('public')->exists($record->background_image);

        if ($needsImage) {
            Storage::disk('public')->put($relativePath, $this->canvasBuilder->build($type));
            $record->background_image = $relativePath;
        }

        $record->syncCanvasDimensions();
        $record->saveQuietly();

        if ($replaceImages) {
            $this->fieldSynchronizer->seedDefaults($record);
        } elseif ($needsImage || $record->fields()->count() === 0) {
            $this->fieldSynchronizer->seedDefaults($record);
        } else {
            $this->fieldSynchronizer->ensureFields($record);
        }

        return $record->fresh(['fields']);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function definitions(): array
    {
        return [
            DocumentTemplate::TYPE_DONATION_POSTER => [
                'name' => 'Bağış Afişi',
                'sort_order' => 2,
            ],
            DocumentTemplate::TYPE_THANKS_POSTER => [
                'name' => 'Teşekkür Afişi',
                'sort_order' => 3,
                'message_template' => '{tarih} tarihinde {bagis_turu} bağış türünden {tutar} {para_birimi} bağış yaptığınız için teşekkür ederiz. Desteğiniz ihtiyaç sahiplerine ulaşmıştır.',
            ],
        ];
    }
}
