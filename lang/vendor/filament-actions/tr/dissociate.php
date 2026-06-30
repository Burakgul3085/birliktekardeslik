<?php

return [

    'single' => [

        'label' => 'İlişkiyi kaldır',

        'modal' => [

            'heading' => ':label ilişkisini kaldır',

            'actions' => [

                'dissociate' => [
                    'label' => 'İlişkiyi kaldır',
                ],

            ],

        ],

        'notifications' => [

            'dissociated' => [
                'title' => 'İlişki kaldırıldı',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Seçilenlerin ilişkisini kaldır',

        'modal' => [

            'heading' => 'Seçilen :label ilişkisini kaldır',

            'actions' => [

                'dissociate' => [
                    'label' => 'İlişkiyi kaldır',
                ],

            ],

        ],

        'notifications' => [

            'dissociated' => [
                'title' => 'İlişki kaldırıldı',
            ],

        ],

    ],

];
