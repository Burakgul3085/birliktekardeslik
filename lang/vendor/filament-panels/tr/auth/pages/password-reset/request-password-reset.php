<?php

return [

    'title' => 'Şifrenizi sıfırlayın',

    'heading' => 'Şifrenizi mi unuttunuz?',

    'actions' => [

        'login' => [
            'label' => 'girişe geri dön',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'E-posta adresi',
        ],

        'actions' => [

            'request' => [
                'label' => 'E-posta gönder',
            ],

        ],

    ],

    'notifications' => [

        'sent' => [
            'body' => 'Hesabınız mevcut değilse e-postayı almazsınız.',
        ],

        'throttled' => [
            'title' => 'Çok fazla istek',
            'body' => 'Lütfen :seconds saniye sonra tekrar deneyin.',
        ],

    ],

];
