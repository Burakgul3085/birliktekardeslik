<?php

return [

    'single' => [

        'label' => 'Geri yükle',

        'modal' => [

            'heading' => ':label geri yükle',

            'actions' => [

                'restore' => [
                    'label' => 'Geri yükle',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Geri yüklendi',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Seçilenleri geri yükle',

        'modal' => [

            'heading' => 'Seçilen :label geri yükle',

            'actions' => [

                'restore' => [
                    'label' => 'Geri yükle',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Geri yüklendi',
            ],

            'restored_partial' => [
                'title' => ':total kayıttan :count tanesi geri yüklendi',
                'missing_authorization_failure_message' => ':count kaydı geri yükleme yetkiniz yok.',
                'missing_processing_failure_message' => ':count kayıt geri yüklenemedi.',
            ],

            'restored_none' => [
                'title' => 'Geri yükleme başarısız',
                'missing_authorization_failure_message' => ':count kaydı geri yükleme yetkiniz yok.',
                'missing_processing_failure_message' => ':count kayıt geri yüklenemedi.',
            ],

        ],

    ],

];
