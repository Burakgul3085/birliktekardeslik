<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 30px; }
        body { font-family: DejaVu Sans, sans-serif; color: #0f172a; }
        .cert {
            border: 6px double #0d9488;
            padding: 34px 38px;
            min-height: 700px;
            text-align: center;
            position: relative;
        }
        .logo { width: 70px; height: 70px; border-radius: 50%; }
        h1 { color: #0d9488; font-size: 28px; letter-spacing: 2px; margin: 14px 0; }
        h2 { font-size: 22px; margin: 24px 0 8px; }
        .text { font-size: 14px; line-height: 1.8; max-width: 85%; margin: 0 auto; }
        .meta { margin-top: 28px; font-size: 12px; color: #475569; }
        .qr { position: absolute; bottom: 28px; right: 28px; width: 84px; height: 84px; }
    </style>
</head>
<body>
    <div class="cert">
        @if($logoDataUri)
            <img src="{{ $logoDataUri }}" class="logo" alt="Logo">
        @endif
        <h1>SERTİFİKA</h1>
        <p class="text">Bu belge,</p>
        <h2>{{ $placeholders['ad_soyad'] }}</h2>
        <p class="text">
            adına <strong>{{ $placeholders['tarih'] }}</strong> tarihinde yapılan
            <strong>{{ $placeholders['bagis_tutari'] }} {{ $placeholders['para_birimi'] }}</strong>
            tutarındaki bağışın hayırsever katkısı nedeniyle takdirle verilmiştir.
        </p>
        <p class="meta">
            Bağış No: {{ $placeholders['bagis_no'] }}<br>
            {{ $settings->site_title ?? 'Birlikte Kardeşlik Derneği' }}
        </p>
        <img src="{{ $qrDataUri }}" class="qr" alt="QR">
    </div>
</body>
</html>
