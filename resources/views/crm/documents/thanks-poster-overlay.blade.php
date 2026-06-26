<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 0; padding: 0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { margin: 0; padding: 0; }
        .page {
            position: relative;
            width: {{ $pageWidth }}pt;
            height: {{ $pageHeight }}pt;
            overflow: hidden;
            page-break-after: avoid;
            page-break-inside: avoid;
        }
        .page-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: {{ $pageWidth }}pt;
            height: {{ $pageHeight }}pt;
            z-index: 0;
        }
        .layer { position: absolute; z-index: 1; text-align: center; }
        /* "Sayın Ad Soyad" — TEŞEKKÜRLER altındaki isim satırı */
        .salutation {
            left: 8%;
            width: 84%;
            top: 312pt;
            font-family: DejaVu Serif, serif;
            font-size: 17pt;
            font-weight: bold;
            color: #1B3A6B;
            line-height: 1.3;
        }
        /* Teşekkür metni — ortadaki kutu alanı */
        .thank-you-body {
            left: 14%;
            width: 72%;
            top: 392pt;
            font-family: DejaVu Serif, serif;
            font-size: 11pt;
            font-weight: normal;
            color: #1B3A6B;
            line-height: 1.75;
        }
    </style>
</head>
<body>
    <div class="page">
        @if($backgroundDataUri)
            <img src="{{ $backgroundDataUri }}" class="page-bg" alt="">
        @endif
        @if(filled($salutation))
            <div class="layer salutation">{{ $salutation }}</div>
        @endif
        @if(filled($thankYouBody))
            <div class="layer thank-you-body">{{ $thankYouBody }}</div>
        @endif
    </div>
</body>
</html>
