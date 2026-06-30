<?php

return [

    'single' => [

        'label' => 'Kalıcı olarak sil',

        'modal' => [

            'heading' => ':label kalıcı olarak sil',

            'actions' => [

                'delete' => [
                    'label' => 'Sil',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Silindi',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Seçilenleri kalıcı olarak sil',

        'modal' => [

            'heading' => 'Seçilen :label kalıcı olarak sil',

            'actions' => [

                'delete' => [
                    'label' => 'Sil',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Silindi',
            ],

            'deleted_partial' => [
                'title' => ':total kayıttan :count tanesi silindi',
                'missing_authorization_failure_message' => ':count kaydı silme yetkiniz yok.',
                'missing_processing_failure_message' => ':count kayıt silinemedi.',
            ],

            'deleted_none' => [
                'title' => 'Silme başarısız',
                'missing_authorization_failure_message' => ':count kaydı silme yetkiniz yok.',
                'missing_processing_failure_message' => ':count kayıt silinemedi.',
            ],

        ],

    ],

];
