<?php

return [

    'label' => 'Sorgu oluşturucu',

    'form' => [

        'operator' => [
            'label' => 'Operatör',
        ],

        'or_groups' => [

            'label' => 'Gruplar',

            'block' => [
                'label' => 'VEYA koşulu',
                'or' => 'VEYA',
            ],

        ],

        'rules' => [

            'label' => 'Kurallar',

            'item' => [
                'and' => 'VE',
            ],

        ],

    ],

    'no_rules' => '(Kural yok)',

    'item_separators' => [
        'and' => 'VE',
        'or' => 'VEYA',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'Dolu',
                'inverse' => 'Boş',
            ],

            'summary' => [
                'direct' => ':attribute dolu',
                'inverse' => ':attribute boş',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'Doğru',
                    'inverse' => 'Yanlış',
                ],

                'summary' => [
                    'direct' => ':attribute doğru',
                    'inverse' => ':attribute yanlış',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'Sonra',
                    'inverse' => 'Sonra değil',
                ],

                'summary' => [
                    'direct' => ':attribute :date tarihinden sonra',
                    'inverse' => ':attribute :date tarihinden sonra değil',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'Önce',
                    'inverse' => 'Önce değil',
                ],

                'summary' => [
                    'direct' => ':attribute :date tarihinden önce',
                    'inverse' => ':attribute :date tarihinden önce değil',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'Tarih',
                    'inverse' => 'Tarih değil',
                ],

                'summary' => [
                    'direct' => ':attribute :date',
                    'inverse' => ':attribute :date değil',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'Ay',
                    'inverse' => 'Ay değil',
                ],

                'summary' => [
                    'direct' => ':attribute :month',
                    'inverse' => ':attribute :month değil',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'Yıl',
                    'inverse' => 'Yıl değil',
                ],

                'summary' => [
                    'direct' => ':attribute :year',
                    'inverse' => ':attribute :year değil',
                ],

            ],

            'unit_labels' => [
                'second' => 'Saniye',
                'minute' => 'Dakika',
                'hour' => 'Saat',
                'day' => 'Gün',
                'week' => 'Hafta',
                'month' => 'Ay',
                'quarter' => 'Çeyrek',
                'year' => 'Yıl',
            ],

            'presets' => [
                'past_decade' => 'Geçen on yıl',
                'past_5_years' => 'Son 5 yıl',
                'past_2_years' => 'Son 2 yıl',
                'past_year' => 'Geçen yıl',
                'past_6_months' => 'Son 6 ay',
                'past_quarter' => 'Geçen çeyrek',
                'past_month' => 'Geçen ay',
                'past_2_weeks' => 'Son 2 hafta',
                'past_week' => 'Geçen hafta',
                'past_hour' => 'Geçen saat',
                'past_minute' => 'Geçen dakika',
                'this_decade' => 'Bu on yıl',
                'this_year' => 'Bu yıl',
                'this_quarter' => 'Bu çeyrek',
                'this_month' => 'Bu ay',
                'today' => 'Bugün',
                'this_hour' => 'Bu saat',
                'this_minute' => 'Bu dakika',
                'next_minute' => 'Sonraki dakika',
                'next_hour' => 'Sonraki saat',
                'next_week' => 'Sonraki hafta',
                'next_2_weeks' => 'Sonraki 2 hafta',
                'next_month' => 'Sonraki ay',
                'next_quarter' => 'Sonraki çeyrek',
                'next_6_months' => 'Sonraki 6 ay',
                'next_year' => 'Sonraki yıl',
                'next_2_years' => 'Sonraki 2 yıl',
                'next_5_years' => 'Sonraki 5 yıl',
                'next_decade' => 'Sonraki on yıl',
                'custom' => 'Özel',
            ],

            'form' => [

                'date' => [
                    'label' => 'Tarih',
                ],

                'month' => [
                    'label' => 'Ay',
                ],

                'year' => [
                    'label' => 'Yıl',
                ],

                'mode' => [

                    'label' => 'Tarih türü',

                    'options' => [
                        'absolute' => 'Belirli tarih',
                        'relative' => 'Kayan pencere',
                    ],

                ],

                'preset' => [
                    'label' => 'Zaman aralığı',
                ],

                'relative_value' => [
                    'label' => 'Kaç tane',
                ],

                'relative_unit' => [
                    'label' => 'Zaman birimi',
                ],

                'tense' => [

                    'label' => 'Zaman kipi',

                    'options' => [
                        'past' => 'Geçmiş',
                        'future' => 'Gelecek',
                    ],

                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'Eşittir',
                    'inverse' => 'Eşit değildir',
                ],

                'summary' => [
                    'direct' => ':attribute :number değerine eşittir',
                    'inverse' => ':attribute :number değerine eşit değildir',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'En fazla',
                    'inverse' => 'Daha büyük',
                ],

                'summary' => [
                    'direct' => ':attribute en fazla :number',
                    'inverse' => ':attribute :number değerinden büyük',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'En az',
                    'inverse' => 'Daha küçük',
                ],

                'summary' => [
                    'direct' => ':attribute en az :number',
                    'inverse' => ':attribute :number değerinden küçük',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'Ortalama',
                    'summary' => 'Ortalama :attribute',
                ],

                'max' => [
                    'label' => 'En büyük',
                    'summary' => 'En büyük :attribute',
                ],

                'min' => [
                    'label' => 'En küçük',
                    'summary' => 'En küçük :attribute',
                ],

                'sum' => [
                    'label' => 'Toplam',
                    'summary' => ':attribute toplamı',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'Toplama',
                ],

                'number' => [
                    'label' => 'Sayı',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'Var',
                    'inverse' => 'Yok',
                ],

                'summary' => [
                    'direct' => ':count :relationship var',
                    'inverse' => ':count :relationship yok',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'En fazla',
                    'inverse' => 'Daha fazla',
                ],

                'summary' => [
                    'direct' => 'En fazla :count :relationship var',
                    'inverse' => ':count :relationship değerinden fazla var',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'En az',
                    'inverse' => 'Daha az',
                ],

                'summary' => [
                    'direct' => 'En az :count :relationship var',
                    'inverse' => ':count :relationship değerinden az var',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'Boş',
                    'inverse' => 'Boş değil',
                ],

                'summary' => [
                    'direct' => ':relationship boş',
                    'inverse' => ':relationship boş değil',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'Şudur',
                        'inverse' => 'Şu değildir',
                    ],

                    'multiple' => [
                        'direct' => 'İçerir',
                        'inverse' => 'İçermez',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship şudur: :values',
                        'inverse' => ':relationship şu değildir: :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship şunları içerir: :values',
                        'inverse' => ':relationship şunları içermez: :values',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' veya ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'Değer',
                    ],

                    'values' => [
                        'label' => 'Değerler',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'Adet',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'Şudur',
                    'inverse' => 'Şu değildir',
                ],

                'summary' => [
                    'direct' => ':attribute şudur: :values',
                    'inverse' => ':attribute şu değildir: :values',
                    'values_glue' => [
                        ', ',
                        'final' => ' veya ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'Değer',
                    ],

                    'values' => [
                        'label' => 'Değerler',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'İçerir',
                    'inverse' => 'İçermez',
                ],

                'summary' => [
                    'direct' => ':attribute :text içerir',
                    'inverse' => ':attribute :text içermez',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'İle biter',
                    'inverse' => 'İle bitmez',
                ],

                'summary' => [
                    'direct' => ':attribute :text ile biter',
                    'inverse' => ':attribute :text ile bitmez',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'Eşittir',
                    'inverse' => 'Eşit değildir',
                ],

                'summary' => [
                    'direct' => ':attribute :text değerine eşittir',
                    'inverse' => ':attribute :text değerine eşit değildir',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'İle başlar',
                    'inverse' => 'İle başlamaz',
                ],

                'summary' => [
                    'direct' => ':attribute :text ile başlar',
                    'inverse' => ':attribute :text ile başlamaz',
                ],

            ],

            'form' => [

                'text' => [
                    'label' => 'Metin',
                ],

            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => 'Kural ekle',
        ],

        'add_rule_group' => [
            'label' => 'VEYA ekle',
        ],

    ],

];
