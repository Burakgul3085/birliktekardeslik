<?php

return [

    'title' => 'Kayıt ol',

    'heading' => 'Kayıt ol',

    'actions' => [

        'login' => [
            'before' => 'veya',
            'label' => 'hesabınıza giriş yapın',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'E-posta adresi',
        ],

        'name' => [
            'label' => 'Ad',
        ],

        'password' => [
            'label' => 'Şifre',
            'validation_attribute' => 'şifre',
        ],

        'password_confirmation' => [
            'label' => 'Şifreyi onayla',
        ],

        'actions' => [

            'register' => [
                'label' => 'Kayıt ol',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Çok fazla kayıt denemesi',
            'body' => 'Lütfen :seconds saniye sonra tekrar deneyin.',
        ],

    ],

];
