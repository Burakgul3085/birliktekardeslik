<?php

namespace App\Support\Crm;

use App\Models\PosterDocument;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Storage;

/**
 * Üretilmiş afiş PNG'sini tek sayfalık PDF'e sarar (DomPDF).
 */
class PosterPdfBuilder
{
    public function build(PosterDocument $poster): string
    {
        $relativePath = $poster->image_path;

        if (! $relativePath || ! Storage::disk('public')->exists($relativePath)) {
            throw new \RuntimeException('Afiş görseli bulunamadı.');
        }

        $absolutePath = Storage::disk('public')->path($relativePath);
        [$widthPx, $heightPx] = $this->imageDimensions($absolutePath);

        // px -> pt (96 dpi varsayımı)
        $widthPt = $widthPx * 72 / 96;
        $heightPt = $heightPx * 72 / 96;

        $dataUri = $this->fileToDataUri($absolutePath);

        $html = '<html><head><style>'
            . '@page { margin: 0; }'
            . 'html,body { margin:0; padding:0; }'
            . 'img { display:block; width:' . $widthPt . 'pt; height:' . $heightPt . 'pt; }'
            . '</style></head><body>'
            . '<img src="' . $dataUri . '">'
            . '</body></html>';

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper([0, 0, $widthPt, $heightPt], 'portrait');
        $dompdf->render();

        return (string) $dompdf->output();
    }

    /**
     * @return array{0: int, 1: int}
     */
    private function imageDimensions(string $path): array
    {
        $size = @getimagesize($path);

        if (! $size) {
            return [720, 1080];
        }

        return [(int) $size[0], (int) $size[1]];
    }

    private function fileToDataUri(string $path): string
    {
        $mime = mime_content_type($path) ?: 'image/png';

        return 'data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($path));
    }
}
