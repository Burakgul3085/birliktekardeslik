<?php

namespace App\Support\Crm\TemplateEngine;

use GdImage;

class PngEncoder
{
    public function encode(GdImage $image): string
    {
        ob_start();
        imagepng($image, null, 0);
        $binary = (string) ob_get_clean();
        imagedestroy($image);

        return $binary;
    }
}
