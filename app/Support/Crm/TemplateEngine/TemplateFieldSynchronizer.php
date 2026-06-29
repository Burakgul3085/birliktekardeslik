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
        $this->reapplyFieldsFromRatios($template);
        $this->ensureCatalogFields($template);
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
        ['width' => $canvasWidth, 'height' => $canvasHeight] = $template->canvasSize();
        $keptIds = [];

        foreach (array_values($fields) as $index => $field) {
            $normalized = TemplateFieldNormalizer::normalize($field);
            $normalized = TemplateCoordinateHelper::attachRatios($normalized, $canvasWidth, $canvasHeight);
            $attributes = DocumentTemplateField::fromLegacyArray($template->id, $normalized, $index);

            $dbId = $this->resolveFieldDatabaseId($normalized);

            if ($dbId !== null) {
                $existing = $template->fields()->where('id', $dbId)->first();
            } else {
                $existing = $template->fields()->where('field_key', $attributes['field_key'])->first();
            }

            if ($existing) {
                $existing->update($attributes);
                $keptIds[] = $existing->id;
            } else {
                $created = $template->fields()->create($attributes);
                $keptIds[] = $created->id;
            }
        }

        if ($keptIds !== []) {
            $template->fields()->whereNotIn('id', $keptIds)->delete();
        }
    }

    public function rescaleFieldsToCanvas(DocumentTemplate $template, int $oldWidth, int $oldHeight): void
    {
        $template->load('fields');

        ['width' => $newWidth, 'height' => $newHeight] = $template->canvasSize();

        if ($oldWidth < 1 || $oldHeight < 1 || ($newWidth === $oldWidth && $newHeight === $oldHeight)) {
            return;
        }

        $scaleX = $newWidth / $oldWidth;
        $scaleY = $newHeight / $oldHeight;

        foreach ($template->fields as $field) {
            if ($field->x_ratio !== null && $field->y_ratio !== null && $field->width_ratio !== null && $field->height_ratio !== null) {
                TemplateCoordinateHelper::applyRatiosToField($field, $newWidth, $newHeight);
            } else {
                TemplateCoordinateHelper::scaleFieldPixels($field, $scaleX, $scaleY);
                TemplateCoordinateHelper::applyRatiosToField($field, $newWidth, $newHeight);
            }

            $field->save();
        }
    }

    public function reapplyFieldsFromRatios(DocumentTemplate $template): void
    {
        $template->load('fields');
        ['width' => $width, 'height' => $height] = $template->canvasSize();

        if ($width < 1 || $height < 1) {
            return;
        }

        foreach ($template->fields as $field) {
            TemplateCoordinateHelper::applyRatiosToField($field, $width, $height);
            $field->save();
        }
    }

    public function ensureCatalogFields(DocumentTemplate $template): void
    {
        if (! in_array($template->type, [DocumentTemplate::TYPE_DONATION_POSTER, DocumentTemplate::TYPE_THANKS_POSTER], true)) {
            return;
        }

        $template->load('fields');
        $existingKeys = $template->fields->pluck('field_key')->all();

        if ($template->type === DocumentTemplate::TYPE_THANKS_POSTER && in_array('tesekkur_paragrafi', $existingKeys, true) && ! in_array('tesekkur_metni', $existingKeys, true)) {
            $legacy = $template->fields()->where('field_key', 'tesekkur_paragrafi')->first();
            if ($legacy) {
                $legacy->update([
                    'field_key' => 'tesekkur_metni',
                    'label' => 'Teşekkür Metni',
                ]);
            }
            $existingKeys = $template->fields()->pluck('field_key')->all();
        }

        $requiredKeys = TemplateFieldCatalog::keysForType($template->type);
        $missing = array_values(array_diff($requiredKeys, $existingKeys));

        if ($missing === []) {
            return;
        }

        ['width' => $width, 'height' => $height] = $template->canvasSize();
        $defaults = collect(TemplateFieldDefaults::forType($template->type, $width, $height))
            ->keyBy('key');

        $sortOrder = (int) $template->fields()->max('sort_order') + 1;

        foreach ($missing as $key) {
            $definition = $defaults->get($key);

            if (! $definition) {
                continue;
            }

            $attributes = DocumentTemplateField::fromLegacyArray(
                $template->id,
                TemplateCoordinateHelper::attachRatios($definition, $width, $height),
                $sortOrder++,
            );

            $template->fields()->create($attributes);
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

    /**
     * @param  array<string, mixed>  $field
     */
    private function resolveFieldDatabaseId(array $field): ?int
    {
        $id = (string) ($field['id'] ?? '');

        if (preg_match('/_(\d+)$/', $id, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }
}
