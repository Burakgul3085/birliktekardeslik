<?php

namespace App\Support\Crm\TemplateEngine;

class PdfFromPngConverter
{
    public function convert(string $pngBinary): string
    {
        if (! extension_loaded('imagick')) {
            throw new \RuntimeException(
                'Afiş PDF dönüşümü için sunucuda PHP Imagick eklentisi gereklidir. Kurulum: apt install php-imagick'
            );
        }

        $imagick = new \Imagick();
        $imagick->readImageBlob($pngBinary);
        $imagick->setImageFormat('pdf');
        $imagick->setImageCompressionQuality(100);

        return (string) $imagick->getImagesBlob();
    }
}
