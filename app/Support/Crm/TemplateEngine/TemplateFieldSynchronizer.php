<?php

namespace App\Support\Crm\TemplateEngine;

use App\Models\DocumentTemplate;
use App\Models\DocumentTemplateField;

class TemplateFieldSynchronizer
{
    public function ensureFields(DocumentTemplate $template, bool $forceReset = false): void
    {
        $template->syncCanvasDimensions();

        if ($forceReset || $template->fields()->count() === 0) {
            $this->seedDefaults($template);

            return;
        }

        $this->migrateLegacySettingsIfNeeded($template);
    }

    public function seedDefaults(DocumentTemplate $template): void
    {
        if (! in_array($template->type, [DocumentTemplate::TYPE_DONATION_POSTER, DocumentTemplate::TYPE_THANKS_POSTER], true)) {
            return;
        }

        ['width' => $width, 'height' => $height] = $template->canvasSize();
        $defaults = TemplateFieldDefaults::forType($template->type, $width, $height);

        $template->fields()->delete();
        $this->persistFieldArrays($template, $defaults);
    }

    /**
     * @param  array<int, array<string, mixed>>  $fields
     */
    public function persistFieldArrays(DocumentTemplate $template, array $fields): void
    {
        $template->fields()->delete();

        foreach (array_values($fields) as $index => $field) {
            $normalized = TemplateFieldNormalizer::normalize($field);
            $template->fields()->create(
                DocumentTemplateField::fromLegacyArray($template->id, $normalized, $index),
            );
        }
    }

    public function migrateLegacySettingsIfNeeded(DocumentTemplate $template): void
    {
        if ($template->fields()->exists()) {
            return;
        }

        $legacy = $template->settings['fields'] ?? [];

        if (empty($legacy)) {
            $this->seedDefaults($template);

            return;
        }

        $this->persistFieldArrays($template, $legacy);
    }
}
