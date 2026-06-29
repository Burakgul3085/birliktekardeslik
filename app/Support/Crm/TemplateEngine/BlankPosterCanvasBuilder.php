<?php

namespace App\Support\Crm\TemplateEngine;

use App\Models\DocumentTemplate;
use GdImage;

class BlankPosterCanvasBuilder
{
    public const WIDTH = 2480;

    public const HEIGHT = 3508;

    /**
     * Boş afiş şablonu PNG üretir (logo/çerçeve sabit, metin alanları boş).
     */
    public function build(string $type): string
    {
        $image = imagecreatetruecolor(self::WIDTH, self::HEIGHT);

        if ($image === false) {
            throw new \RuntimeException('PNG tuvali oluşturulamadı.');
        }

        imagealphablending($image, true);
        imagesavealpha($image, true);

        $background = $this->color($image, '#FAF7F2');
        imagefilledrectangle($image, 0, 0, self::WIDTH, self::HEIGHT, $background);

        $accent = $type === DocumentTemplate::TYPE_DONATION_POSTER
            ? $this->color($image, '#B91C1C')
            : $this->color($image, '#1B3A6B');

        $frame = $this->color($image, '#1B3A6B');
        $muted = $this->color($image, '#D6DCE8');

        $this->drawFrame($image, $frame, $accent, $muted);
        $this->drawHeaderBand($image, $frame, $muted);
        $this->drawFooterBand($image, $frame, $muted);

        $binary = $this->encode($image);
        imagedestroy($image);

        return $binary;
    }

    private function drawFrame(GdImage $image, int $frame, int $accent, int $muted): void
    {
        $margin = 72;

        imagerectangle($image, $margin, $margin, self::WIDTH - $margin, self::HEIGHT - $margin, $frame);
        imagerectangle($image, $margin + 8, $margin + 8, self::WIDTH - $margin - 8, self::HEIGHT - $margin - 8, $accent);
        imagerectangle($image, $margin + 16, $margin + 16, self::WIDTH - $margin - 16, self::HEIGHT - $margin - 16, $muted);
    }

    private function drawHeaderBand(GdImage $image, int $frame, int $muted): void
    {
        $top = 180;
        $height = 260;
        imagefilledrectangle($image, 120, $top, self::WIDTH - 120, $top + $height, $muted);
        imagerectangle($image, 120, $top, self::WIDTH - 120, $top + $height, $frame);
    }

    private function drawFooterBand(GdImage $image, int $frame, int $muted): void
    {
        $height = 140;
        $top = self::HEIGHT - 220 - $height;
        imagefilledrectangle($image, 120, $top, self::WIDTH - 120, $top + $height, $muted);
        imagerectangle($image, 120, $top, self::WIDTH - 120, $top + $height, $frame);
    }

    private function color(GdImage $image, string $hex): int
    {
        $hex = ltrim($hex, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return imagecolorallocate($image, $r, $g, $b);
    }

    private function encode(GdImage $image): string
    {
        ob_start();
        imagepng($image, null, 6);
        $binary = ob_get_clean();

        if (! is_string($binary)) {
            throw new \RuntimeException('PNG kodlanamadı.');
        }

        return $binary;
    }
}
