<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 0; size: A4 portrait; }
        * { box-sizing: border-box; }
        html, body {
            margin: 0;
            padding: 0;
            width: {{ $pageWidth }}pt;
            height: {{ $pageHeight }}pt;
            font-family: DejaVu Sans, sans-serif;
        }
        .page {
            position: relative;
            width: {{ $pageWidth }}pt;
            height: {{ $pageHeight }}pt;
            overflow: hidden;
            @if($backgroundDataUri)
            background-image: url('{{ $backgroundDataUri }}');
            background-size: 100% 100%;
            background-repeat: no-repeat;
            background-position: center;
            @endif
        }
        .poster-name {
            position: absolute;
            left: 50%;
            top: 31%;
            transform: translate(-50%, -50%);
            width: 88%;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            color: #111827;
            line-height: 1.3;
            letter-spacing: 0.5px;
        }
        .poster-description {
            position: absolute;
            left: 50%;
            top: 47%;
            transform: translate(-50%, -50%);
            width: 86%;
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            color: #B91C1C;
            line-height: 1.45;
            white-space: pre-wrap;
        }
        .poster-type {
            position: absolute;
            left: 50%;
            top: 64%;
            transform: translate(-50%, -50%);
            width: 88%;
            text-align: center;
            font-size: 17px;
            font-weight: bold;
            color: #B91C1C;
            line-height: 1.25;
            letter-spacing: 0.3px;
        }
        .poster-date {
            position: absolute;
            left: 50%;
            top: 71%;
            transform: translate(-50%, -50%);
            width: 88%;
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            color: #B91C1C;
        }
    </style>
</head>
<body>
    <div class="page">
        @if(filled($posterName))
            <div class="poster-name">{{ $posterName }}</div>
        @endif
        @if(filled($posterDescription))
            <div class="poster-description">{{ $posterDescription }}</div>
        @endif
        @if(filled($donationType))
            <div class="poster-type">{{ mb_strtoupper($donationType, 'UTF-8') }}</div>
        @endif
        @if(filled($donationDate))
            <div class="poster-date">{{ $donationDate }}</div>
        @endif
    </div>
</body>
</html>
