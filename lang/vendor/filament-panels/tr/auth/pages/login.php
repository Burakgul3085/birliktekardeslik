<?php

return [

    'title' => 'Giriş',

    'heading' => 'Giriş yap',

    'actions' => [

        'register' => [
            'before' => 'veya',
            'label' => 'bir hesap oluşturun',
        ],

        'request_password_reset' => [
            'label' => 'Şifrenizi mi unuttunuz?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'E-posta adresi',
        ],

        'password' => [
            'label' => 'Şifre',
        ],

        'remember' => [
            'label' => 'Beni hatırla',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Giriş yap',
            ],

        ],

    ],

    'multi_factor' => [

        'heading' => 'Kimliğinizi doğrulayın',

        'subheading' => 'Girişe devam etmek için kimliğinizi doğrulamanız gerekiyor.',

        'form' => [

            'provider' => [
                'label' => 'Nasıl doğrulamak istersiniz?',
            ],

            'actions' => [

                'authenticate' => [
                    'label' => 'Girişi onayla',
                ],

            ],

        ],

    ],

    'messages' => [

        'failed' => 'Bu bilgiler kayıtlarımızla eşleşmiyor.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Çok fazla giriş denemesi',
            'body' => 'Lütfen :seconds saniye sonra tekrar deneyin.',
        ],

    ],

];
