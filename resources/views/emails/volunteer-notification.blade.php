@php
    $mailTitle = 'Yeni Gönüllülük Başvurusu';
    $mailGreeting = 'Merhaba,';
    $mailIntro = 'Site üzerinden yeni bir gönüllülük başvurusu alındı.';
    $mailContentHtml = '
        <p style="margin:0 0 8px;"><strong>Ad Soyad:</strong> ' . e($application->first_name . ' ' . $application->last_name) . '</p>
        <p style="margin:0 0 8px;"><strong>E-posta:</strong> ' . e($application->email) . '</p>
        <p style="margin:0 0 8px;"><strong>Telefon:</strong> ' . e($application->phone) . '</p>
        <p style="margin:0 0 8px;"><strong>Tercih:</strong> ' . e($application->preference) . '</p>
        <p style="margin:0 0 6px;"><strong>Kendinden Bahset:</strong></p>
        <div style="white-space:pre-line;">' . e($application->about) . '</div>
    ';
    $mailFooterNote = 'Başvuru admin panelde Gönüllü Başvuruları bölümüne kaydedilmiştir.';
@endphp

@include('emails._layout')

