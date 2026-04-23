@php
    $siteSettings = $siteSettings ?? \App\Models\Setting::current();
    $mailTitle = $subject;
    $mailGreeting = 'Merhaba,';
    $mailIntro = null;
    $mailContentHtml = $bodyHtml;
    $mailFooterNote = 'Bu e-posta ' . ($siteSettings->site_title ?? 'Birlikte Kardeşlik Derneği') . ' e-bülten listesine abone olduğunuz için gönderilmiştir.';
@endphp

@include('emails._layout')
