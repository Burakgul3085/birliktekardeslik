<?php

return [

    'label' => 'Profil',

    'form' => [

        'email' => [
            'label' => 'E-posta adresi',
        ],

        'name' => [
            'label' => 'Ad',
        ],

        'password' => [
            'label' => 'Yeni şifre',
            'validation_attribute' => 'şifre',
        ],

        'password_confirmation' => [
            'label' => 'Yeni şifreyi onayla',
            'validation_attribute' => 'şifre onayı',
        ],

        'current_password' => [
            'label' => 'Mevcut şifre',
            'below_content' => 'Güvenliğiniz için devam etmek üzere şifrenizi onaylayın.',
            'validation_attribute' => 'mevcut şifre',
        ],

        'actions' => [

            'save' => [
                'label' => 'Değişiklikleri kaydet',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'İki adımlı doğrulama (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'E-posta değişikliği talebi gönderildi',
            'body' => 'E-posta adresinizi değiştirme talebi :email adresine gönderildi. Değişikliği doğrulamak için lütfen e-postanızı kontrol edin.',
        ],

        'saved' => [
            'title' => 'Kaydedildi',
        ],

        'throttled' => [
            'title' => 'Çok fazla istek. Lütfen :seconds saniye sonra tekrar deneyin.',
            'body' => 'Lütfen :seconds saniye sonra tekrar deneyin.',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'İptal',
        ],

    ],

];
