@include('emails._layout', [
    'mailTitle' => 'Admin Şifre Sıfırlama',
    'mailGreeting' => 'Merhaba ' . ($user->name ?: 'Yönetici') . ',',
    'mailIntro' => 'Şifre sıfırlama talebi alındı. Yeni giriş şifresi aşağıdadır.',
    'mailContentHtml' => '
        <p style="margin:0 0 8px; font-size:13px; color:#334155;">Hesap E-postası</p>
        <p style="margin:0 0 14px; font-size:14px; font-weight:700; color:#0f172a;">' . e($requestedEmail) . '</p>
        <p style="margin:0 0 8px; font-size:13px; color:#334155;">Yeni Şifre</p>
        <p style="margin:0; font-size:28px; letter-spacing:3px; font-weight:800; color:#0f172a;">' . e($newPassword) . '</p>
    ',
    'mailFooterNote' => 'Giriş yaptıktan sonra güvenlik için şifrenizi değiştirmeniz önerilir.',
])

