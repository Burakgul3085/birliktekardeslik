<?php

namespace App\Support\Crm\TemplateEngine;

use App\Models\DonationDocument;

class DocumentRenderService
{
    public function __construct(
        private readonly TemplateRenderEngine $engine = new TemplateRenderEngine(),
        private readonly TemplateFieldSynchronizer $fieldSynchronizer = new TemplateFieldSynchronizer(),
    ) {}

    /**
     * @return array{png: string, pdf: string}
     */
    public function renderForDocument(DonationDocument $document): array
    {
        $document->loadMissing(['template.fields', 'fieldOverrides', 'donation.donor', 'donation.donationType']);

        $template = $document->template;

        if (! $template || ! $template->usesTemplateEngine()) {
            throw new \RuntimeException('Bu belge PNG şablon motoru ile oluşturulamaz.');
        }

        $values = TemplateValueResolver::forDonation(
            $document->donation,
            $template->type,
            $document->verification_url,
            $template,
        );

        $fields = $this->mergedFieldDefinitions($document);
        $png = $this->engine->renderPng($template, $values, $fields);

        return [
            'png' => $png,
            'pdf' => $this->engine->renderPdf($template, $values, $fields),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function mergedFieldDefinitions(DonationDocument $document): array
    {
        $template = $document->template;
        $this->fieldSynchronizer->ensureFields($template);
        $template->loadMissing('fields');

        $overrides = $document->fieldOverrides->keyBy('field_key');

        return $template->fields->map(function ($field) use ($overrides) {
            $definition = $field->toRenderDefinition();

            if ($override = $overrides->get($field->field_key)) {
                $definition = array_merge($definition, $override->toRenderPatch());

                if (filled($override->text_override)) {
                    $definition['_text_override'] = $override->text_override;
                }
            }

            return $definition;
        })->values()->all();
    }
}
