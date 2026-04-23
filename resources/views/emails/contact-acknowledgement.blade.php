@php
    $mailTitle = 'Mesajınız Alındı';
    $mailGreeting = 'Merhaba ' . $contactMessage->first_name . ' ' . $contactMessage->last_name . ',';
    $mailIntro = 'Mesajınız ' . $siteTitle . ' ekibine başarıyla iletilmiştir.';
    $mailContentHtml = 'En kısa sürede size dönüş yapacağız. İlginiz için teşekkür ederiz.';
    $mailFooterNote = 'Saygılarımızla, ' . $siteTitle;
@endphp

@include('emails._layout')

