<?php

namespace App\Support\Crm\TemplateEngine;

use Dompdf\Dompdf;
use Dompdf\Options;

class TemplatePdfExporter
{
    public function fromPng(string $pngBinary): string
    {
        if (extension_loaded('imagick')) {
            return $this->viaImagick($pngBinary);
        }

        return $this->viaEmbeddedImage($pngBinary);
    }

    private function viaImagick(string $pngBinary): string
    {
        $imagick = new \Imagick();
        $imagick->readImageBlob($pngBinary);
        $imagick->setImageFormat('pdf');
        $imagick->setImageCompressionQuality(100);

        return (string) $imagick->getImagesBlob();
    }

    private function viaEmbeddedImage(string $pngBinary): string
    {
        $info = getimagesizefromstring($pngBinary);

        if ($info === false) {
            throw new \RuntimeException('PNG boyutları okunamadı.');
        }

        [$widthPx, $heightPx] = $info;
        $widthPt = $widthPx * 72 / 96;
        $heightPt = $heightPx * 72 / 96;
        $dataUri = 'data:image/png;base64,' . base64_encode($pngBinary);

        $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><style>'
            . '@page{margin:0;}body{margin:0;padding:0;}'
            . 'img{display:block;width:' . $widthPt . 'pt;height:' . $heightPt . 'pt;}'
            . '</style></head><body><img src="' . $dataUri . '" alt=""></body></html>';

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper([0, 0, $widthPt, $heightPt]);
        $dompdf->render();

        return (string) $dompdf->output();
    }
}
