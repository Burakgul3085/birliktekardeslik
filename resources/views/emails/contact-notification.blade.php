@php
    $mailTitle = 'Yeni İletişim Mesajı';
    $mailGreeting = 'Merhaba,';
    $mailIntro = 'Site üzerinden yeni bir iletişim formu mesajı alındı.';
    $mailContentHtml = '
        <p style="margin:0 0 8px;"><strong>Ad Soyad:</strong> ' . e($contactMessage->first_name . ' ' . $contactMessage->last_name) . '</p>
        <p style="margin:0 0 8px;"><strong>E-posta:</strong> ' . e($contactMessage->email) . '</p>
        <p style="margin:0 0 6px;"><strong>Mesaj:</strong></p>
        <div style="white-space:pre-line;">' . e($contactMessage->message) . '</div>
    ';
    $mailFooterNote = 'Mesaj admin panelde İletişim Mesajları bölümüne de kaydedilmiştir.';
@endphp

@include('emails._layout')

