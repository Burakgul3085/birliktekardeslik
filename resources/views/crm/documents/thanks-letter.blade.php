<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 36px 40px; }
        body { font-family: DejaVu Sans, sans-serif; color: #0f172a; }
        .frame { border: 2px solid #0d9488; border-radius: 16px; padding: 28px; min-height: 680px; position: relative; }
        .top { text-align: center; margin-bottom: 24px; }
        .logo { width: 64px; height: 64px; border-radius: 50%; }
        h1 { color: #0d9488; font-size: 24px; margin: 10px 0 4px; }
        .lead { color: #64748b; font-size: 13px; }
        .body { font-size: 14px; line-height: 1.8; margin: 28px 0; text-align: justify; }
        .highlight { color: #0d9488; font-weight: bold; }
        .sign { margin-top: 48px; text-align: right; }
        .qr { position: absolute; bottom: 24px; left: 28px; width: 80px; height: 80px; }
        .code { position: absolute; bottom: 28px; right: 28px; font-size: 10px; color: #64748b; text-align: right; }
    </style>
</head>
<body>
    <div class="frame">
        <div class="top">
            @if($logoDataUri)
                <img src="{{ $logoDataUri }}" class="logo" alt="Logo">
            @endif
            <h1>Teşekkür Belgesi</h1>
            <div class="lead">{{ $settings->site_title ?? 'Birlikte Kardeşlik Derneği' }}</div>
        </div>

        <div class="body">
            Sayın <span class="highlight">{{ $placeholders['ad_soyad'] }}</span>,<br><br>
            <span class="highlight">{{ $placeholders['tarih'] }}</span> tarihinde gerçekleştirdiğiniz
            <span class="highlight">{{ $placeholders['bagis_tutari'] }} {{ $placeholders['para_birimi'] }}</span>
            tutarındaki <span class="highlight">{{ $placeholders['bagis_turu'] ?: 'bağışınız' }}</span>
            için içtenlikle teşekkür ederiz.<br><br>
            Desteğiniz, ihtiyaç sahiplerine ulaşan umut ve dayanışmanın en güzel örneğidir.
            Birlikte iyiliği büyütmeye devam ediyoruz.
        </div>

        <div class="sign">
            <strong>{{ $settings->site_title ?? 'Birlikte Kardeşlik Derneği' }}</strong><br>
            Yönetim Kurulu
        </div>

        <img src="{{ $qrDataUri }}" class="qr" alt="QR">
        <div class="code">{{ $verificationCode }}</div>
    </div>
</body>
</html>
