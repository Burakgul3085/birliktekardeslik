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

        {{-- Şablondaki "Sayın ....." satırını kapat --}}
        @if(filled($salutation))
            <div style="
                position: absolute;
                z-index: 1;
                left: 52pt;
                top: 326pt;
                width: {{ $pageWidth - 104 }}pt;
                height: 30pt;
                background-color: #ffffff;
            "></div>
            <div style="
                position: absolute;
                z-index: 2;
                left: 52pt;
                top: 330pt;
                width: {{ $pageWidth - 104 }}pt;
                text-align: center;
                font-family: DejaVu Serif, serif;
                font-size: 15pt;
                font-weight: bold;
                color: #1B3A6B;
                line-height: 1.2;
            ">{{ $salutation }}</div>
        @endif

        {{-- Teşekkür metni: ortadaki kutu alanı --}}
        @if(filled($thankYouBody))
            <div style="
                position: absolute;
                z-index: 1;
                left: 88pt;
                top: 408pt;
                width: {{ $pageWidth - 176 }}pt;
                text-align: center;
                font-family: DejaVu Serif, serif;
                font-size: {{ $bodyFontSize ?? 10 }}pt;
                font-weight: normal;
                color: #1B3A6B;
                line-height: 1.7;
            ">{{ $thankYouBody }}</div>
        @endif
    </div>
</body>
</html>
