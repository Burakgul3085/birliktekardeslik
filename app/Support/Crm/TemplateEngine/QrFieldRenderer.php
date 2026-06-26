<?php

namespace App\Support\Crm\TemplateEngine;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use GdImage;

class QrFieldRenderer
{
    /**
     * @param  array<string, mixed>  $field
     */
    public function render(GdImage $image, array $field, string $url): void
    {
        if (blank($url)) {
            return;
        }

        $writer = new PngWriter();
        $size = min((int) $field['width'], (int) $field['height']);
        $qrCode = new QrCode(data: $url, size: $size, margin: 2);
        $result = $writer->write($qrCode);
        $qrImage = imagecreatefromstring($result->getString());

        if ($qrImage === false) {
            return;
        }

        $x = (int) $field['x'] + (int) (((int) $field['width'] - $size) / 2);
        $y = (int) $field['y'] + (int) (((int) $field['height'] - $size) / 2);

        imagecopyresampled($image, $qrImage, $x, $y, 0, 0, $size, $size, imagesx($qrImage), imagesy($qrImage));
        imagedestroy($qrImage);
    }
}
