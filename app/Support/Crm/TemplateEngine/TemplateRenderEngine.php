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
     */
    public function renderPng(DocumentTemplate $template, array $values): string
    {
        $this->fieldSynchronizer->ensureFields($template);
        $template->loadMissing('fields');

        $canvas = $this->canvasLoader->load($template);

        foreach ($template->fields as $field) {
            $definition = $field->toRenderDefinition();
            $value = $values[$definition['key']] ?? '';

            if ($definition['type'] === 'qr') {
                $this->qrRenderer->render($canvas, $definition, $value);
            } else {
                $this->textRenderer->render($canvas, $definition, $value);
            }
        }

        return $this->pngEncoder->encode($canvas);
    }

    /**
     * @param  array<string, string>  $values
     */
    public function renderPdf(DocumentTemplate $template, array $values): string
    {
        $png = $this->renderPng($template, $values);

        return $this->pdfConverter->convert($png);
    }

    /**
     * @param  array<string, string>  $values
     * @return array{png: string, pdf: string}
     */
    public function render(DocumentTemplate $template, array $values): array
    {
        $png = $this->renderPng($template, $values);

        return [
            'png' => $png,
            'pdf' => $this->pdfConverter->convert($png),
        ];
    }
}
