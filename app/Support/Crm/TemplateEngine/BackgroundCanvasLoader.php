<?php

namespace App\Support\Crm\TemplateEngine;

use App\Models\DocumentTemplate;
use GdImage;

class BackgroundCanvasLoader
{
    public function load(DocumentTemplate $template): GdImage
    {
        if (! $template->background_image) {
            throw new \RuntimeException('Şablon PNG dosyası bulunamadı.');
        }

        $path = storage_path('app/public/' . $template->background_image);

        if (! is_file($path)) {
            throw new \RuntimeException('Şablon PNG dosyası okunamadı: ' . $template->background_image);
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        $image = match ($extension) {
            'jpg', 'jpeg' => imagecreatefromjpeg($path),
            'png' => imagecreatefrompng($path),
            'webp' => function_exists('imagecreatefromwebp') ? imagecreatefromwebp($path) : false,
            default => false,
        };

        if ($image === false) {
            throw new \RuntimeException('Şablon görseli yüklenemedi. PNG formatı önerilir.');
        }

        imagesavealpha($image, true);
        imagealphablending($image, true);

        return $image;
    }
}
