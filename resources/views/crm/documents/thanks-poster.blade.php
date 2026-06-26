<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 20px; size: A4 landscape; }
        body { font-family: DejaVu Sans, sans-serif; margin: 0; }
        .poster {
            background: linear-gradient(135deg, #0d9488 0%, #14b8a6 45%, #5eead4 100%);
            color: #fff;
            border-radius: 18px;
            padding: 36px 42px;
            min-height: 520px;
            position: relative;
        }
        .logo { width: 72px; height: 72px; border-radius: 50%; background: #fff; padding: 4px; }
        h1 { font-size: 34px; margin: 16px 0 8px; }
        .name { font-size: 28px; font-weight: bold; margin: 18px 0; }
        .amount { font-size: 22px; background: rgba(255,255,255,.18); display: inline-block; padding: 8px 18px; border-radius: 999px; }
        .thanks { font-size: 16px; margin-top: 22px; max-width: 70%; line-height: 1.6; }
        .qr { position: absolute; right: 36px; bottom: 36px; width: 96px; height: 96px; background: #fff; padding: 6px; border-radius: 10px; }
    </style>
</head>
<body>
    <div class="poster">
        <img src="{{ $logoDataUri }}" class="logo" alt="Logo">
        <h1>Teşekkürler!</h1>
        <div class="name">{{ $placeholders['ad_soyad'] }}</div>
        <div class="amount">{{ $placeholders['bagis_tutari'] }} {{ $placeholders['para_birimi'] }} · {{ $placeholders['bagis_turu'] ?: 'Bağış' }}</div>
        <div class="thanks">
            İyiliğe ortak olduğunuz için teşekkür ederiz. Birlikte daha güçlüyüz.
            <br>{{ $settings->site_title ?? 'Birlikte Kardeşlik Derneği' }}
        </div>
        <img src="{{ $qrDataUri }}" class="qr" alt="QR">
    </div>
</body>
</html>
