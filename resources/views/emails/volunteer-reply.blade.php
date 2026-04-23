@php
    $mailTitle = $subject;
    $mailGreeting = 'Merhaba ' . $application->first_name . ' ' . $application->last_name . ',';
    $mailIntro = 'Gönüllülük başvurunuza yanıtımız aşağıdadır.';
    $mailContentHtml = nl2br(e($body));
    $mailFooterNote = 'Saygılarımızla, ' . $siteTitle;
@endphp

@include('emails._layout')

