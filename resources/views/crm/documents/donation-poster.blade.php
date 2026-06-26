<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 0; padding: 0; }
        body { margin: 0; padding: 0; }
    </style>
</head>
<body>
    <div style="position: relative; width: {{ $pageWidth }}pt; height: {{ $pageHeight }}pt; overflow: hidden; page-break-after: avoid;">
        @if($backgroundDataUri)
            <img src="{{ $backgroundDataUri }}" alt="" style="position: absolute; top: 0; left: 0; width: {{ $pageWidth }}pt; height: {{ $pageHeight }}pt; z-index: 0;">
        @endif

        {{-- İsim: siyah serif, büyük, üst orta beyaz alan --}}
        @if(filled($posterName))
            <div style="
                position: absolute;
                z-index: 1;
                left: 42pt;
                top: 222pt;
                width: {{ $pageWidth - 84 }}pt;
                text-align: center;
                font-family: DejaVu Serif, serif;
                font-size: {{ $nameFontSize ?? 24 }}pt;
                font-weight: bold;
                color: #111827;
                line-height: 1.2;
                letter-spacing: 0.5pt;
            ">{{ $posterName }}</div>
        @endif

        {{-- Bağış açıklaması: kırmızı, çok satırlı, orta alan --}}
        @if(filled($posterDescription))
            <div style="
                position: absolute;
                z-index: 1;
                left: 48pt;
                top: 278pt;
                width: {{ $pageWidth - 96 }}pt;
                text-align: center;
                font-family: DejaVu Sans, sans-serif;
                font-size: {{ $descriptionFontSize ?? 10 }}pt;
                font-weight: bold;
                color: #B91C1C;
                line-height: 1.45;
            ">{{ $posterDescription }}</div>
        @endif

        {{-- Bağış türü --}}
        @if(filled($donationType))
            <div style="
                position: absolute;
                z-index: 1;
                left: 42pt;
                top: 430pt;
                width: {{ $pageWidth - 84 }}pt;
                text-align: center;
                font-family: DejaVu Sans, sans-serif;
                font-size: 19pt;
                font-weight: bold;
                color: #B91C1C;
                line-height: 1.15;
                letter-spacing: 0.4pt;
            ">{{ mb_strtoupper($donationType, 'UTF-8') }}</div>
        @endif

        {{-- Tarih --}}
        @if(filled($donationDate))
            <div style="
                position: absolute;
                z-index: 1;
                left: 42pt;
                top: 472pt;
                width: {{ $pageWidth - 84 }}pt;
                text-align: center;
                font-family: DejaVu Sans, sans-serif;
                font-size: 16pt;
                font-weight: bold;
                color: #B91C1C;
                line-height: 1.15;
            ">{{ $donationDate }}</div>
        @endif
    </div>
</body>
</html>
