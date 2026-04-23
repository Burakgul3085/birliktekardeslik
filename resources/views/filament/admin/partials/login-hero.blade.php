@php
    $settings = \App\Models\Setting::current();
    $logoUrl = $settings->logo ? asset('storage/' . $settings->logo) : asset('images/default-logo.svg');
@endphp
<div class="bkd-login-hero">
    <div style="display:flex; align-items:center; gap: .7rem;">
        <img src="{{ $logoUrl }}" alt="BKD Logo" style="width: 42px; height: 42px; border-radius: 9999px; background: #fff; padding: 2px; object-fit: cover;">
        <div>
            <div style="font-size: .95rem; font-weight: 700;">Hoş geldiniz</div>
            <div style="font-size: .82rem; opacity: .95;">Birlikte iyiliği büyüten yönetim paneli</div>
        </div>
    </div>
</div>
