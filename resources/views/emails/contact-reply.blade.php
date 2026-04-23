@php
    $mailTitle = $subject;
    $mailGreeting = 'Merhaba ' . $contactMessage->first_name . ' ' . $contactMessage->last_name . ',';
    $mailIntro = 'İletişim formundan ilettiğiniz mesajınıza yanıtımız aşağıdadır.';
    $mailContentHtml = nl2br(e($body));
    $mailFooterNote = 'Saygılarımızla, ' . $siteTitle;
@endphp

@include('emails._layout')

