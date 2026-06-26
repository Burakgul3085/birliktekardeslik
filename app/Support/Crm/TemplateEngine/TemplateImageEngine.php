<?php

namespace App\Support\Crm\TemplateEngine;

use App\Models\DocumentTemplate;
use GdImage;

class TemplateImageEngine
{
    public function __construct(
        private readonly TextBoxRenderer $textRenderer = new TextBoxRenderer(),
        private readonly QrFieldRenderer $qrRenderer = new QrFieldRenderer(),
    ) {}

    /**
     * @param  array<string, string>  $values
     */
    public function render(DocumentTemplate $template, array $values): string
    {
        if (! $template->background_image) {
            throw new \RuntimeException('Şablon görseli bulunamadı.');
        }

        $path = storage_path('app/public/' . $template->background_image);

        if (! is_file($path)) {
            throw new \RuntimeException('Şablon görsel dosyası okunamadı.');
        }

        $image = $this->loadImage($path);
        $fields = $template->resolvedTemplateFields();

        foreach ($fields as $field) {
            $key = (string) ($field['key'] ?? '');
            $value = $values[$key] ?? '';

            if (($field['type'] ?? 'text') === 'qr') {
                $this->qrRenderer->render($image, $field, $value);
            } else {
                $this->textRenderer->render($image, $field, $value);
            }
        }

        ob_start();
        imagepng($image, null, 0);
        $binary = (string) ob_get_clean();
        imagedestroy($image);

        return $binary;
    }

    private function loadImage(string $path): GdImage
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        $image = match ($extension) {
            'jpg', 'jpeg' => imagecreatefromjpeg($path),
            'png' => imagecreatefrompng($path),
            'webp' => function_exists('imagecreatefromwebp') ? imagecreatefromwebp($path) : false,
            default => false,
        };

        if ($image === false) {
            throw new \RuntimeException('Şablon görseli yüklenemedi. Desteklenen formatlar: PNG, JPG.');
        }

        imagesavealpha($image, true);
        imagealphablending($image, true);

        return $image;
    }
}
