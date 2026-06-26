<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 0; }
        * { box-sizing: border-box; }
        html, body {
            margin: 0;
            padding: 0;
            width: {{ $pageWidth }}pt;
            height: {{ $pageHeight }}pt;
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
        .field {
            position: absolute;
            white-space: pre-wrap;
            line-height: 1.25;
            max-width: 80%;
        }
        .field--left { transform: translate(0, -50%); }
        .field--center { transform: translate(-50%, -50%); }
        .field--right { transform: translate(-100%, -50%); }
        .qr {
            position: absolute;
            transform: translate(-50%, -50%);
        }
    </style>
</head>
<body>
    <div class="page">
        @foreach($fields as $field)
            @php
                $align = $field['align'] ?? 'center';
                $value = $placeholders[$field['key'] ?? ''] ?? '';
            @endphp
            @if(filled($value))
                <div
                    class="field field--{{ $align }}"
                    style="
                        left: {{ (float) ($field['x'] ?? 50) }}%;
                        top: {{ (float) ($field['y'] ?? 50) }}%;
                        font-size: {{ (int) ($field['font_size'] ?? 16) }}px;
                        color: {{ $field['color'] ?? '#0f172a' }};
                        text-align: {{ $align }};
                        font-weight: {{ $field['font_weight'] ?? 'normal' }};
                    "
                >{{ $value }}</div>
            @endif
        @endforeach

        @if(($qr['enabled'] ?? false) && $qrDataUri)
            <img
                src="{{ $qrDataUri }}"
                class="qr"
                alt="QR"
                style="
                    left: {{ (float) ($qr['x'] ?? 88) }}%;
                    top: {{ (float) ($qr['y'] ?? 88) }}%;
                    width: {{ (int) ($qr['size'] ?? 70) }}px;
                    height: {{ (int) ($qr['size'] ?? 70) }}px;
                "
            >
        @endif
    </div>
</body>
</html>
