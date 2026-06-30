<?php

return [

    'title' => 'E-posta adresinizi doğrulayın',

    'heading' => 'E-posta adresinizi doğrulayın',

    'actions' => [

        'resend_notification' => [
            'label' => 'Tekrar gönder',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'Gönderdiğimiz e-postayı almadınız mı?',
        'notification_sent' => ':email adresine, e-posta adresinizi nasıl doğrulayacağınıza dair talimatları içeren bir e-posta gönderdik.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'E-postayı tekrar gönderdik.',
        ],

        'notification_resend_throttled' => [
            'title' => 'Çok fazla tekrar gönderme denemesi',
            'body' => 'Lütfen :seconds saniye sonra tekrar deneyin.',
        ],

    ],

];
