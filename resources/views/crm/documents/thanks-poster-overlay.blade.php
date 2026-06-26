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
            font-family: DejaVu Sans, serif;
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
        .salutation {
            position: absolute;
            left: 50%;
            top: 36%;
            transform: translate(-50%, -50%);
            width: 80%;
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            color: #1B3A6B;
            line-height: 1.3;
        }
        .thank-you-body {
            position: absolute;
            left: 50%;
            top: 52%;
            transform: translate(-50%, -50%);
            width: 72%;
            text-align: center;
            font-size: 12px;
            color: #1B3A6B;
            line-height: 1.75;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
    <div class="page">
        @if(filled($salutation))
            <div class="salutation">{{ $salutation }}</div>
        @endif
        @if(filled($thankYouMessage))
            <div class="thank-you-body">{{ $thankYouMessage }}</div>
        @endif
    </div>
</body>
</html>
