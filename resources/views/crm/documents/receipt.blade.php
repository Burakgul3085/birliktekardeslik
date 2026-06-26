<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 28px 32px; }
        body { font-family: DejaVu Sans, sans-serif; color: #0f172a; font-size: 12px; line-height: 1.5; }
        .header { border-bottom: 3px solid #0d9488; padding-bottom: 14px; margin-bottom: 20px; }
        .brand { display: table; width: 100%; }
        .brand-logo { width: 58px; height: 58px; border-radius: 50%; }
        .title { font-size: 20px; font-weight: bold; color: #0d9488; margin: 0; }
        .subtitle { color: #64748b; margin-top: 4px; }
        .badge { display: inline-block; background: #ccfbf1; color: #0f766e; padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: bold; }
        .grid { width: 100%; border-collapse: collapse; margin: 16px 0; }
        .grid td { padding: 8px 10px; border: 1px solid #e2e8f0; vertical-align: top; }
        .grid td.label { width: 32%; background: #f8fafc; font-weight: bold; color: #334155; }
        .amount-box { background: linear-gradient(135deg, #f0fdfa, #ecfeff); border: 1px solid #99f6e4; border-radius: 12px; padding: 16px; text-align: center; margin: 18px 0; }
        .amount { font-size: 26px; font-weight: bold; color: #0d9488; }
        .footer { margin-top: 24px; border-top: 1px solid #e2e8f0; padding-top: 14px; }
        .qr-wrap { text-align: right; }
        .qr { width: 92px; height: 92px; }
        .code { font-family: monospace; font-size: 11px; color: #475569; }
        .note { color: #64748b; font-size: 10px; margin-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <table width="100%">
            <tr>
                <td width="70">
                    @if($logoDataUri)
                        <img src="{{ $logoDataUri }}" class="brand-logo" alt="Logo">
                    @endif
                </td>
                <td>
                    <p class="title">{{ $settings->site_title ?? 'Birlikte Kardeşlik Derneği' }}</p>
                    <p class="subtitle">Bağış Makbuzu / Donation Receipt</p>
                </td>
                <td align="right">
                    <span class="badge">MAKBUZ</span>
                </td>
            </tr>
        </table>
    </div>

    <table class="grid">
        <tr>
            <td class="label">Bağış No</td>
            <td>{{ $placeholders['bagis_no'] }}</td>
            <td class="label">Makbuz No</td>
            <td>{{ $placeholders['makbuz_no'] }}</td>
        </tr>
        <tr>
            <td class="label">Bağışçı</td>
            <td>{{ $placeholders['ad_soyad'] }}</td>
            <td class="label">Telefon</td>
            <td>{{ $placeholders['telefon'] ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label">Bağış Türü</td>
            <td>{{ $placeholders['bagis_turu'] ?: '-' }}</td>
            <td class="label">Ödeme Türü</td>
            <td>{{ $donation->paymentMethod?->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Bağış Tarihi</td>
            <td>{{ $placeholders['tarih'] }}</td>
            <td class="label">Proje</td>
            <td>{{ $donation->project?->title ?? '-' }}</td>
        </tr>
    </table>

    <div class="amount-box">
        <div style="color:#64748b; font-size:11px;">Bağış Tutarı</div>
        <div class="amount">{{ $placeholders['bagis_tutari'] }} {{ $placeholders['para_birimi'] }}</div>
    </div>

    @if($placeholders['aciklama'])
        <p><strong>Açıklama:</strong> {{ $placeholders['aciklama'] }}</p>
    @endif

    <div class="footer">
        <table width="100%">
            <tr>
                <td valign="top">
                    <p class="note">Bu belge elektronik ortamda oluşturulmuştur. QR kodu okutarak doğrulayabilirsiniz.</p>
                    <p class="code">Doğrulama Kodu: {{ $verificationCode }}</p>
                    <p class="note">{{ $settings->address ?? '' }} · {{ $settings->phone ?? '' }} · {{ $settings->email ?? '' }}</p>
                </td>
                <td class="qr-wrap" width="110" valign="top">
                    <img src="{{ $qrDataUri }}" class="qr" alt="QR">
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
