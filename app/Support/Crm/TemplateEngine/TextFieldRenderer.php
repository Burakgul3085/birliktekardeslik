<?php

namespace App\Support\Crm\TemplateEngine;

use GdImage;

class TextFieldRenderer
{
    /**
     * @param  array<string, mixed>  $field
     */
    public function render(GdImage $image, array $field, string $text): void
    {
        $text = trim($text);

        if ($text === '') {
            return;
        }

        if (! function_exists('imagettftext')) {
            throw new \RuntimeException('PHP GD FreeType desteği gerekli (imagettftext).');
        }

        $fontPath = FontRegistry::path((string) ($field['font_family'] ?? 'DejaVuSans'));
        $fontSize = (int) ($field['font_size'] ?? 32);
        $minFontSize = 8;
        $autoResize = (bool) ($field['auto_resize'] ?? $field['auto_shrink'] ?? true);
        $wordWrap = (bool) ($field['word_wrap'] ?? true);
        $maxLines = max(1, (int) ($field['max_lines'] ?? 5));
        $lineHeight = (float) ($field['line_height'] ?? 1.4);
        $verticalAlign = (string) ($field['vertical_align'] ?? $field['valign'] ?? 'middle');
        $color = $this->allocateColor($image, (string) ($field['color'] ?? '#000000'));

        while ($fontSize >= $minFontSize) {
            $lines = $wordWrap
                ? $this->wrapText($text, $fontPath, $fontSize, (int) $field['width'], $maxLines)
                : [preg_replace('/\s+/u', ' ', $text) ?? $text];

            if (count($lines) > $maxLines) {
                if ($autoResize) {
                    $fontSize--;

                    continue;
                }

                $lines = array_slice($lines, 0, $maxLines);
            }

            if (! $wordWrap && ! $this->fitsWidth($lines[0], $fontPath, $fontSize, (int) $field['width'])) {
                if ($autoResize) {
                    $fontSize--;

                    continue;
                }
            }

            $metrics = $this->blockMetrics($lines, $fontPath, $fontSize, $lineHeight);

            if ($metrics['height'] > (int) $field['height'] && $autoResize) {
                $fontSize--;

                continue;
            }

            $this->drawBlock($image, $field, $lines, $fontPath, $fontSize, $lineHeight, $color, $metrics, $verticalAlign);

            return;
        }
    }

    /**
     * @return array<int, string>
     */
    private function wrapText(string $text, string $fontPath, int $fontSize, int $maxWidth, int $maxLines): array
    {
        $words = preg_split('/\s+/u', trim($text)) ?: [];
        $lines = [];
        $current = '';

        foreach ($words as $word) {
            $candidate = $current === '' ? $word : $current . ' ' . $word;

            if ($this->textWidth($candidate, $fontPath, $fontSize) <= $maxWidth) {
                $current = $candidate;

                continue;
            }

            if ($current !== '') {
                $lines[] = $current;
            }

            $current = $word;

            if (count($lines) >= $maxLines) {
                break;
            }
        }

        if ($current !== '' && count($lines) < $maxLines) {
            $lines[] = $current;
        }

        return $lines === [] ? [''] : $lines;
    }

    private function fitsWidth(string $text, string $fontPath, int $fontSize, int $maxWidth): bool
    {
        return $this->textWidth($text, $fontPath, $fontSize) <= $maxWidth;
    }

    private function textWidth(string $text, string $fontPath, int $fontSize): int
    {
        $box = imagettfbbox($fontSize, 0, $fontPath, $text);

        return (int) abs($box[2] - $box[0]);
    }

    /**
     * @param  array<int, string>  $lines
     * @return array{width: int, height: int, line_height: int, ascent: int}
     */
    private function blockMetrics(array $lines, string $fontPath, int $fontSize, float $lineHeight): array
    {
        $lineHeightPx = (int) round($fontSize * $lineHeight);
        $box = imagettfbbox($fontSize, 0, $fontPath, 'Ay');
        $ascent = (int) abs($box[7]);
        $maxWidth = 0;

        foreach ($lines as $line) {
            $maxWidth = max($maxWidth, $this->textWidth($line, $fontPath, $fontSize));
        }

        return [
            'width' => $maxWidth,
            'height' => count($lines) * $lineHeightPx,
            'line_height' => $lineHeightPx,
            'ascent' => $ascent,
        ];
    }

    /**
     * @param  array<string, mixed>  $field
     * @param  array<int, string>  $lines
     * @param  array{width: int, height: int, line_height: int, ascent: int}  $metrics
     */
    private function drawBlock(
        GdImage $image,
        array $field,
        array $lines,
        string $fontPath,
        int $fontSize,
        float $lineHeight,
        int $color,
        array $metrics,
        string $verticalAlign,
    ): void {
        $lineHeightPx = $metrics['line_height'];
        $blockHeight = $metrics['height'];
        $yStart = match ($verticalAlign) {
            'top' => (int) $field['y'] + 4 + $metrics['ascent'],
            'bottom' => (int) $field['y'] + (int) $field['height'] - $blockHeight + $metrics['ascent'],
            default => (int) $field['y'] + (int) round((((int) $field['height'] - $blockHeight) / 2)) + $metrics['ascent'],
        };

        foreach ($lines as $index => $line) {
            $lineWidth = $this->textWidth($line, $fontPath, $fontSize);
            $x = match ($field['align'] ?? 'center') {
                'left' => (int) $field['x'],
                'right' => (int) $field['x'] + (int) $field['width'] - $lineWidth,
                default => (int) $field['x'] + (int) (((int) $field['width'] - $lineWidth) / 2),
            };

            $y = $yStart + ($index * $lineHeightPx);
            imagettftext($image, $fontSize, 0, $x, $y, $color, $fontPath, $line);
        }
    }

    private function allocateColor(GdImage $image, string $hex): int
    {
        $hex = ltrim($hex, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return imagecolorallocate($image, $r, $g, $b);
    }
}
