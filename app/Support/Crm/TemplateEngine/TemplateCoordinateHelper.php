<?php

namespace App\Support\Crm\TemplateEngine;

use App\Models\DocumentTemplateField;

class TemplateCoordinateHelper
{
    /**
     * @param  array<string, mixed>  $field
     * @return array<string, mixed>
     */
    public static function attachRatios(array $field, int $canvasWidth, int $canvasHeight): array
    {
        if ($canvasWidth < 1 || $canvasHeight < 1) {
            return $field;
        }

        $field['x_ratio'] = round((int) ($field['x'] ?? 0) / $canvasWidth, 6);
        $field['y_ratio'] = round((int) ($field['y'] ?? 0) / $canvasHeight, 6);
        $field['width_ratio'] = round(max(1, (int) ($field['width'] ?? 1)) / $canvasWidth, 6);
        $field['height_ratio'] = round(max(1, (int) ($field['height'] ?? 1)) / $canvasHeight, 6);

        return $field;
    }

    /**
     * @param  array<string, mixed>  $field
     * @return array<string, mixed>
     */
    public static function pixelsFromRatios(array $field, int $canvasWidth, int $canvasHeight): array
    {
        if (! isset($field['x_ratio'], $field['y_ratio'], $field['width_ratio'], $field['height_ratio'])) {
            return $field;
        }

        $field['x'] = max(0, (int) round((float) $field['x_ratio'] * $canvasWidth));
        $field['y'] = max(0, (int) round((float) $field['y_ratio'] * $canvasHeight));
        $field['width'] = max(1, (int) round((float) $field['width_ratio'] * $canvasWidth));
        $field['height'] = max(1, (int) round((float) $field['height_ratio'] * $canvasHeight));

        return $field;
    }

    public static function applyRatiosToField(DocumentTemplateField $field, int $canvasWidth, int $canvasHeight): void
    {
        if ($canvasWidth < 1 || $canvasHeight < 1) {
            return;
        }

        if ($field->x_ratio !== null && $field->y_ratio !== null && $field->width_ratio !== null && $field->height_ratio !== null) {
            $field->x = max(0, (int) round((float) $field->x_ratio * $canvasWidth));
            $field->y = max(0, (int) round((float) $field->y_ratio * $canvasHeight));
            $field->width = max(1, (int) round((float) $field->width_ratio * $canvasWidth));
            $field->height = max(1, (int) round((float) $field->height_ratio * $canvasHeight));

            return;
        }

        $field->x_ratio = round($field->x / $canvasWidth, 6);
        $field->y_ratio = round($field->y / $canvasHeight, 6);
        $field->width_ratio = round(max(1, $field->width) / $canvasWidth, 6);
        $field->height_ratio = round(max(1, $field->height) / $canvasHeight, 6);
    }

    public static function scaleFieldPixels(DocumentTemplateField $field, float $scaleX, float $scaleY): void
    {
        $field->x = max(0, (int) round($field->x * $scaleX));
        $field->y = max(0, (int) round($field->y * $scaleY));
        $field->width = max(1, (int) round($field->width * $scaleX));
        $field->height = max(1, (int) round($field->height * $scaleY));

        if ($field->field_type === 'text') {
            $scale = min($scaleX, $scaleY);
            $field->font_size = max(8, (int) round($field->font_size * $scale));
        }
    }
}
