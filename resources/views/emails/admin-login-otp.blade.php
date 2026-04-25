@include('emails._layout', [
    'mailTitle' => 'Yönetim Paneli Giriş Doğrulama Kodu',
    'mailGreeting' => 'Merhaba ' . ($user->name ?: 'Yönetici') . ',',
    'mailIntro' => 'Yönetim paneli girişinizi tamamlamak için aşağıdaki 4 haneli doğrulama kodunu kullanın.',
    'mailContentHtml' => '
        <p style="margin:0 0 8px; font-size:13px; color:#334155;">Doğrulama Kodunuz</p>
        <p style="margin:0; font-size:32px; letter-spacing:8px; font-weight:800; color:#0f172a;">' . e($code) . '</p>
    ',
    'mailFooterNote' => 'Kodun geçerlilik süresi 10 dakikadır. Bu işlemi siz başlatmadıysanız bu e-postayı dikkate almayın.',
])

