<?php

namespace App\Support\Crm\TemplateEngine;

use App\Models\DocumentTemplate;

class TemplateRenderEngine
{
    public function __construct(
        private readonly BackgroundCanvasLoader $canvasLoader = new BackgroundCanvasLoader(),
        private readonly TextFieldRenderer $textRenderer = new TextFieldRenderer(),
        private readonly QrFieldRenderer $qrRenderer = new QrFieldRenderer(),
        private readonly PngEncoder $pngEncoder = new PngEncoder(),
        private readonly PdfFromPngConverter $pdfConverter = new PdfFromPngConverter(),
        private readonly TemplateFieldSynchronizer $fieldSynchronizer = new TemplateFieldSynchronizer(),
    ) {}

    /**
     * @param  array<string, string>  $values
     * @param  array<int, array<string, mixed>>|null  $fieldOverrides
     */
    public function renderPng(DocumentTemplate $template, array $values, ?array $fieldOverrides = null): string
    {
        if ($fieldOverrides === null) {
            $this->fieldSynchronizer->ensureFields($template);
            $template->loadMissing('fields');
            $definitions = $template->fields->map(fn ($field) => $field->toRenderDefinition())->all();
        } else {
            $definitions = TemplateFieldNormalizer::normalizeAll(array_values($fieldOverrides));
        }

        $canvas = $this->canvasLoader->load($template);

        foreach ($definitions as $definition) {
            $value = $definition['_text_override'] ?? ($values[$definition['key']] ?? '');

            if (($definition['type'] ?? 'text') === 'qr') {
                $this->qrRenderer->render($canvas, $definition, $value);
            } else {
                $this->textRenderer->render($canvas, $definition, $value);
            }
        }

        return $this->pngEncoder->encode($canvas);
    }

    /**
     * @param  array<string, string>  $values
     * @param  array<int, array<string, mixed>>|null  $fieldOverrides
     */
    public function renderPdf(DocumentTemplate $template, array $values, ?array $fieldOverrides = null): string
    {
        $png = $this->renderPng($template, $values, $fieldOverrides);

        return $this->pdfConverter->convert($png);
    }

    /**
     * @param  array<string, string>  $values
     * @param  array<int, array<string, mixed>>|null  $fieldOverrides
     * @return array{png: string, pdf: string}
     */
    public function render(DocumentTemplate $template, array $values, ?array $fieldOverrides = null): array
    {
        $png = $this->renderPng($template, $values, $fieldOverrides);

        return [
            'png' => $png,
            'pdf' => $this->pdfConverter->convert($png),
        ];
    }
}
