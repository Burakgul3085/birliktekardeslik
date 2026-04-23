@php
    $mailTitle = 'Gönüllülük Başvurunuz Alındı';
    $mailGreeting = 'Merhaba ' . $application->first_name . ' ' . $application->last_name . ',';
    $mailIntro = 'Gönüllülük başvurunuz ' . $siteTitle . ' ekibine başarıyla iletilmiştir.';
    $mailContentHtml = 'Başvurunuz incelenecek ve en kısa sürede sizinle iletişime geçilecektir.';
    $mailFooterNote = 'Saygılarımızla, ' . $siteTitle;
@endphp

@include('emails._layout')

