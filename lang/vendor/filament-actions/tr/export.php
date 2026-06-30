<?php

return [

    'label' => ':label dışa aktar',

    'modal' => [

        'heading' => ':label dışa aktar',

        'form' => [

            'columns' => [

                'label' => 'Sütunlar',

                'actions' => [

                    'select_all' => [
                        'label' => 'Tümünü seç',
                    ],

                    'deselect_all' => [
                        'label' => 'Seçimi kaldır',
                    ],

                ],

                'form' => [

                    'is_enabled' => [
                        'label' => ':column etkin',
                    ],

                    'label' => [
                        'label' => ':column etiketi',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'Dışa aktar',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Dışa aktarma tamamlandı',

            'actions' => [

                'download_csv' => [
                    'label' => '.csv indir',
                ],

                'download_xlsx' => [
                    'label' => '.xlsx indir',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Dışa aktarma çok büyük',
            'body' => 'Aynı anda 1 satırdan fazla dışa aktaramazsınız.|Aynı anda :count satırdan fazla dışa aktaramazsınız.',
        ],

        'no_columns' => [
            'title' => 'Sütun seçilmedi',
            'body' => 'Lütfen dışa aktarmak için en az bir sütun seçin.',
        ],

        'started' => [
            'title' => 'Dışa aktarma başladı',
            'body' => 'Dışa aktarma başladı ve arka planda 1 satır işlenecek. Tamamlandığında indirme bağlantısını içeren bir bildirim alacaksınız.|Dışa aktarma başladı ve arka planda :count satır işlenecek. Tamamlandığında indirme bağlantısını içeren bir bildirim alacaksınız.',
        ],

    ],

    'file_name' => 'disa-aktarma-:export_id-:model',

];
