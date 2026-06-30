<?php

return [

    'label' => ':label içe aktar',

    'modal' => [

        'heading' => ':label içe aktar',

        'form' => [

            'file' => [

                'label' => 'Dosya',

                'placeholder' => 'Bir CSV dosyası yükleyin',

                'rules' => [
                    'duplicate_columns' => '{0} Dosya birden fazla boş sütun başlığı içeremez.|{1,*} Dosya yinelenen sütun başlıkları içeremez: :columns.',
                ],

            ],

            'columns' => [
                'label' => 'Sütunlar',
                'placeholder' => 'Bir sütun seçin',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Örnek CSV dosyasını indir',
            ],

            'import' => [
                'label' => 'İçe aktar',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'İçe aktarma tamamlandı',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Başarısız satır hakkında bilgileri indir|Başarısız satırlar hakkında bilgileri indir',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Yüklenen CSV dosyası çok büyük',
            'body' => 'Aynı anda 1 satırdan fazla içe aktaramazsınız.|Aynı anda :count satırdan fazla içe aktaramazsınız.',
        ],

        'started' => [
            'title' => 'İçe aktarma başladı',
            'body' => 'İçe aktarma başladı ve arka planda 1 satır işlenecek.|İçe aktarma başladı ve arka planda :count satır işlenecek.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-ornek',
    ],

    'failure_csv' => [
        'file_name' => 'ice-aktarma-:import_id-:csv_name-basarisiz-satirlar',
        'error_header' => 'hata',
        'system_error' => 'Sistem hatası, lütfen destek ile iletişime geçin.',
        'column_mapping_required_for_new_record' => ':attribute sütunu dosyadaki bir sütunla eşleştirilmedi, ancak yeni kayıt oluşturmak için gereklidir.',
    ],

];
