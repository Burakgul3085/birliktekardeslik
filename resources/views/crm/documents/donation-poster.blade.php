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
        /* İsim — siyah, büyük, üst orta alan */
        .poster-name {
            left: 6%;
            width: 88%;
            top: 248pt;
            font-family: DejaVu Serif, serif;
            font-size: 22pt;
            font-weight: bold;
            color: #111827;
            line-height: 1.25;
            letter-spacing: 0.4pt;
        }
        /* Bağış açıklaması — kırmızı, çok satırlı */
        .poster-description {
            left: 7%;
            width: 86%;
            top: 318pt;
            font-family: DejaVu Sans, sans-serif;
            font-size: 10.5pt;
            font-weight: bold;
            color: #B91C1C;
            line-height: 1.5;
        }
        /* Bağış türü — kırmızı, büyük */
        .poster-type {
            left: 6%;
            width: 88%;
            top: 448pt;
            font-family: DejaVu Sans, sans-serif;
            font-size: 18pt;
            font-weight: bold;
            color: #B91C1C;
            line-height: 1.2;
            letter-spacing: 0.3pt;
        }
        /* Tarih */
        .poster-date {
            left: 6%;
            width: 88%;
            top: 508pt;
            font-family: DejaVu Sans, sans-serif;
            font-size: 16pt;
            font-weight: bold;
            color: #B91C1C;
            line-height: 1.2;
        }
    </style>
</head>
<body>
    <div class="page">
        @if($backgroundDataUri)
            <img src="{{ $backgroundDataUri }}" class="page-bg" alt="">
        @endif
        @if(filled($posterName))
            <div class="layer poster-name">{{ $posterName }}</div>
        @endif
        @if(filled($posterDescription))
            <div class="layer poster-description">{{ $posterDescription }}</div>
        @endif
        @if(filled($donationType))
            <div class="layer poster-type">{{ mb_strtoupper($donationType, 'UTF-8') }}</div>
        @endif
        @if(filled($donationDate))
            <div class="layer poster-date">{{ $donationDate }}</div>
        @endif
    </div>
</body>
</html>
