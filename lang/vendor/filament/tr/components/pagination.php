<?php

return [

    'label' => 'Sayfalama gezinmesi',

    'overview' => '{1} 1 sonuç gösteriliyor|[2,*] :total sonuçtan :first - :last arası gösteriliyor',

    'fields' => [

        'records_per_page' => [

            'label' => 'Sayfa başına',

            'options' => [
                'all' => 'Tümü',
            ],

        ],

    ],

    'actions' => [

        'first' => [
            'label' => 'İlk',
        ],

        'go_to_page' => [
            'label' => ':page sayfasına git',
        ],

        'last' => [
            'label' => 'Son',
        ],

        'next' => [
            'label' => 'Sonraki',
        ],

        'previous' => [
            'label' => 'Önceki',
        ],

    ],

];
