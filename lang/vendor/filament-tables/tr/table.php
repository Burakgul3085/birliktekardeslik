<?php

return [

    'column_manager' => [

        'heading' => 'Sütunlar',

        'actions' => [

            'apply' => [
                'label' => 'Sütunları uygula',
            ],

            'reset' => [
                'label' => 'Sıfırla',
            ],

        ],

    ],

    'columns' => [

        'actions' => [
            'label' => 'İşlem|İşlemler',
        ],

        'select' => [

            'loading_message' => 'Yükleniyor...',

            'no_options_message' => 'Seçenek bulunamadı.',

            'no_search_results_message' => 'Aramanızla eşleşen seçenek yok.',

            'placeholder' => 'Bir seçenek seçin',

            'searching_message' => 'Aranıyor...',

            'search_prompt' => 'Aramak için yazmaya başlayın...',

        ],

        'text' => [

            'actions' => [
                'collapse_list' => ':count tanesini gizle',
                'expand_list' => ':count tane daha göster',
            ],

            'more_list_items' =>'ve :count tane daha',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Toplu işlemler için tüm öğeleri seç/kaldır.',
        ],

        'bulk_select_record' => [
            'label' => 'Toplu işlemler için :key öğesini seç/kaldır.',
        ],

        'bulk_select_group' => [
            'label' => 'Toplu işlemler için :title grubunu seç/kaldır.',
        ],

        'search' => [
            'label' => 'Ara',
            'placeholder' => 'Ara',
            'indicator' => 'Arama',
        ],

    ],

    'summary' => [

        'heading' => 'Özet',

        'subheadings' => [
            'all' => 'Tüm :label',
            'group' => ':group özeti',
            'page' => 'Bu sayfa',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Ortalama',
            ],

            'count' => [
                'label' => 'Adet',
            ],

            'sum' => [
                'label' => 'Toplam',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Sıralamayı bitir',
        ],

        'enable_reordering' => [
            'label' => 'Kayıtları yeniden sırala',
        ],

        'filter' => [
            'label' => 'Filtrele',
        ],

        'group' => [
            'label' => 'Grupla',
        ],

        'open_bulk_actions' => [
            'label' => 'Toplu işlemler',
        ],

        'column_manager' => [
            'label' => 'Sütun yöneticisi',
        ],

    ],

    'empty' => [

        'heading' => 'Kayıt bulunamadı',

        'description' => 'Başlamak için yeni bir kayıt oluşturun.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Filtreleri uygula',
            ],

            'remove' => [
                'label' => 'Filtreyi kaldır',
            ],

            'remove_all' => [
                'label' => 'Tüm filtreleri kaldır',
                'tooltip' => 'Tüm filtreleri kaldır',
            ],

            'reset' => [
                'label' => 'Sıfırla',
            ],

        ],

        'heading' => 'Filtreler',

        'indicator' => 'Aktif filtreler',

        'multi_select' => [
            'placeholder' => 'Tümü',
        ],

        'select' => [

            'placeholder' => 'Tümü',

            'relationship' => [
                'empty_option_label' => 'Yok',
            ],

        ],

        'trashed' => [

            'label' => 'Silinen kayıtlar',

            'only_trashed' => 'Yalnızca silinen kayıtlar',

            'with_trashed' => 'Silinenler dahil',

            'without_trashed' => 'Silinenler hariç',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Şuna göre grupla',
            ],

            'direction' => [

                'label' => 'Grup yönü',

                'options' => [
                    'asc' => 'Artan',
                    'desc' => 'Azalan',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Kayıtları sürükleyip bırakarak sıralayın.',

    'selection_indicator' => [

        'selected_count' => '1 kayıt seçildi|:count kayıt seçildi',

        'actions' => [

            'select_all' => [
                'label' => 'Tümünü seç (:count)',
            ],

            'deselect_all' => [
                'label' => 'Seçimi kaldır',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Şuna göre sırala',
            ],

            'direction' => [

                'label' => 'Sıralama yönü',

                'options' => [
                    'asc' => 'Artan',
                    'desc' => 'Azalan',
                ],

            ],

        ],

    ],

    'default_model_label' => 'kayıt',

];
