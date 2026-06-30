<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Kopyala',
            ],

            'add' => [

                'label' => ':label ekle',

                'modal' => [

                    'heading' => ':label ekle',

                    'actions' => [

                        'add' => [
                            'label' => 'Ekle',
                        ],

                    ],

                ],

            ],

            'add_between' => [

                'label' => 'Bloklar arasına ekle',

                'modal' => [

                    'heading' => ':label ekle',

                    'actions' => [

                        'add' => [
                            'label' => 'Ekle',
                        ],

                    ],

                ],

            ],

            'delete' => [
                'label' => 'Sil',
            ],

            'edit' => [

                'label' => 'Düzenle',

                'modal' => [

                    'heading' => 'Bloğu düzenle',

                    'actions' => [

                        'save' => [
                            'label' => 'Değişiklikleri kaydet',
                        ],

                    ],

                ],

            ],

            'reorder' => [
                'label' => 'Taşı',
            ],

            'move_down' => [
                'label' => 'Aşağı taşı',
            ],

            'move_up' => [
                'label' => 'Yukarı taşı',
            ],

            'collapse' => [
                'label' => 'Daralt',
            ],

            'expand' => [
                'label' => 'Genişlet',
            ],

            'collapse_all' => [
                'label' => 'Tümünü daralt',
            ],

            'expand_all' => [
                'label' => 'Tümünü genişlet',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'Seçimi kaldır',
            ],

            'select_all' => [
                'label' => 'Tümünü seç',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'İptal',
                ],

                'drag_crop' => [
                    'label' => 'Sürükleme modu "kırp"',
                ],

                'drag_move' => [
                    'label' => 'Sürükleme modu "taşı"',
                ],

                'flip_horizontal' => [
                    'label' => 'Görseli yatay çevir',
                ],

                'flip_vertical' => [
                    'label' => 'Görseli dikey çevir',
                ],

                'move_down' => [
                    'label' => 'Görseli aşağı taşı',
                ],

                'move_left' => [
                    'label' => 'Görseli sola taşı',
                ],

                'move_right' => [
                    'label' => 'Görseli sağa taşı',
                ],

                'move_up' => [
                    'label' => 'Görseli yukarı taşı',
                ],

                'reset' => [
                    'label' => 'Sıfırla',
                ],

                'rotate_left' => [
                    'label' => 'Görseli sola döndür',
                ],

                'rotate_right' => [
                    'label' => 'Görseli sağa döndür',
                ],

                'set_aspect_ratio' => [
                    'label' => 'En boy oranını :ratio yap',
                ],

                'save' => [
                    'label' => 'Kaydet',
                ],

                'zoom_100' => [
                    'label' => 'Görseli %100 yakınlaştır',
                ],

                'zoom_in' => [
                    'label' => 'Yakınlaştır',
                ],

                'zoom_out' => [
                    'label' => 'Uzaklaştır',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'Yükseklik',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'Döndürme',
                    'unit' => 'derece',
                ],

                'width' => [
                    'label' => 'Genişlik',
                    'unit' => 'px',
                ],

                'x_position' => [
                    'label' => 'X',
                    'unit' => 'px',
                ],

                'y_position' => [
                    'label' => 'Y',
                    'unit' => 'px',
                ],

            ],

            'aspect_ratios' => [

                'label' => 'En boy oranları',

                'no_fixed' => [
                    'label' => 'Serbest',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'SVG dosyalarını düzenlemek önerilmez; ölçeklendirmede kalite kaybına yol açabilir.\n Devam etmek istediğinizden emin misiniz?',
                    'disabled' => 'SVG dosyalarını düzenleme devre dışı; ölçeklendirmede kalite kaybına yol açabilir.',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'Satır ekle',
            ],

            'delete' => [
                'label' => 'Satır sil',
            ],

            'reorder' => [
                'label' => 'Satırı yeniden sırala',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'Anahtar',
            ],

            'value' => [
                'label' => 'Değer',
            ],

        ],

    ],

    'markdown_editor' => [

        'file_attachments_accepted_file_types_message' => 'Yüklenen dosyalar şu türde olmalıdır: :values.',

        'file_attachments_max_size_message' => 'Yüklenen dosyalar :max kilobayttan büyük olmamalıdır.',

        'tools' => [
            'attach_files' => 'Dosya ekle',
            'blockquote' => 'Alıntı',
            'bold' => 'Kalın',
            'bullet_list' => 'Madde listesi',
            'code_block' => 'Kod bloğu',
            'heading' => 'Başlık',
            'italic' => 'İtalik',
            'link' => 'Bağlantı',
            'ordered_list' => 'Numaralı liste',
            'redo' => 'Yinele',
            'strike' => 'Üstü çizili',
            'table' => 'Tablo',
            'undo' => 'Geri al',
        ],

    ],

    'modal_table_select' => [

        'actions' => [

            'select' => [

                'label' => 'Seç',

                'actions' => [

                    'select' => [
                        'label' => 'Seç',
                    ],

                ],

            ],

        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => 'Evet',
            'false' => 'Hayır',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => ':label ekle',
            ],

            'add_between' => [
                'label' => 'Arasına ekle',
            ],

            'delete' => [
                'label' => 'Sil',
            ],

            'clone' => [
                'label' => 'Kopyala',
            ],

            'reorder' => [
                'label' => 'Taşı',
            ],

            'move_down' => [
                'label' => 'Aşağı taşı',
            ],

            'move_up' => [
                'label' => 'Yukarı taşı',
            ],

            'collapse' => [
                'label' => 'Daralt',
            ],

            'expand' => [
                'label' => 'Genişlet',
            ],

            'collapse_all' => [
                'label' => 'Tümünü daralt',
            ],

            'expand_all' => [
                'label' => 'Tümünü genişlet',
            ],

        ],

    ],

    'rich_editor' => [

        'actions' => [

            'attach_files' => [

                'label' => 'Dosya yükle',

                'modal' => [

                    'heading' => 'Dosya yükle',

                    'form' => [

                        'file' => [

                            'label' => [
                                'new' => 'Dosya',
                                'existing' => 'Dosyayı değiştir',
                            ],

                        ],

                        'alt' => [

                            'label' => [
                                'new' => 'Alternatif metin',
                                'existing' => 'Alternatif metni değiştir',
                            ],

                        ],

                    ],

                ],

            ],

            'custom_block' => [

                'modal' => [

                    'actions' => [

                        'insert' => [
                            'label' => 'Ekle',
                        ],

                        'save' => [
                            'label' => 'Kaydet',
                        ],

                    ],

                ],

            ],

            'grid' => [

                'label' => 'Izgara',

                'modal' => [

                    'heading' => 'Izgara',

                    'form' => [

                        'preset' => [

                            'label' => 'Hazır ayar',

                            'placeholder' => 'Yok',

                            'options' => [
                                'two' => 'İki',
                                'three' => 'Üç',
                                'four' => 'Dört',
                                'five' => 'Beş',
                                'two_start_third' => 'İki (Üçte bir başla)',
                                'two_end_third' => 'İki (Üçte bir bitir)',
                                'two_start_fourth' => 'İki (Dörtte bir başla)',
                                'two_end_fourth' => 'İki (Dörtte bir bitir)',
                            ],
                        ],

                        'columns' => [
                            'label' => 'Sütunlar',
                        ],

                        'from_breakpoint' => [

                            'label' => 'Kırılma noktasından',

                            'options' => [
                                'default' => 'Tümü',
                                'sm' => 'Küçük',
                                'md' => 'Orta',
                                'lg' => 'Büyük',
                                'xl' => 'Çok büyük',
                                '2xl' => 'En büyük',
                            ],

                        ],

                        'is_asymmetric' => [
                            'label' => 'İki asimetrik sütun',
                        ],

                        'start_span' => [
                            'label' => 'Başlangıç genişliği',
                        ],

                        'end_span' => [
                            'label' => 'Bitiş genişliği',
                        ],

                    ],

                ],

            ],

            'link' => [

                'label' => 'Bağlantı',

                'modal' => [

                    'heading' => 'Bağlantı',

                    'form' => [

                        'url' => [
                            'label' => 'URL',
                        ],

                        'should_open_in_new_tab' => [
                            'label' => 'Yeni sekmede aç',
                        ],

                    ],

                ],

            ],

            'text_color' => [

                'label' => 'Metin rengi',

                'modal' => [

                    'heading' => 'Metin rengi',

                    'form' => [

                        'color' => [
                            'label' => 'Renk',

                            'options' => [
                                'slate' => 'Arduvaz',
                                'gray' => 'Gri',
                                'zinc' => 'Çinko',
                                'neutral' => 'Nötr',
                                'stone' => 'Taş',
                                'mauve' => 'Leylak',
                                'olive' => 'Zeytin',
                                'mist' => 'Sis',
                                'taupe' => 'Vizon',
                                'red' => 'Kırmızı',
                                'orange' => 'Turuncu',
                                'amber' => 'Kehribar',
                                'yellow' => 'Sarı',
                                'lime' => 'Limon yeşili',
                                'green' => 'Yeşil',
                                'emerald' => 'Zümrüt',
                                'teal' => 'Deniz mavisi',
                                'cyan' => 'Camgöbeği',
                                'sky' => 'Gök mavisi',
                                'blue' => 'Mavi',
                                'indigo' => 'Çivit',
                                'violet' => 'Menekşe',
                                'purple' => 'Mor',
                                'fuchsia' => 'Fuşya',
                                'pink' => 'Pembe',
                                'rose' => 'Gül',
                            ],
                        ],

                        'custom_color' => [
                            'label' => 'Özel renk',
                        ],

                    ],

                ],

            ],

        ],

        'file_attachments_accepted_file_types_message' => 'Yüklenen dosyalar şu türde olmalıdır: :values.',

        'file_attachments_max_size_message' => 'Yüklenen dosyalar :max kilobayttan büyük olmamalıdır.',

        'no_merge_tag_search_results_message' => 'Birleştirme etiketi sonucu yok.',

        'mentions' => [
            'no_options_message' => 'Seçenek bulunamadı.',
            'no_search_results_message' => 'Aramanızla eşleşen sonuç yok.',
            'search_prompt' => 'Aramak için yazmaya başlayın...',
            'searching_message' => 'Aranıyor...',
        ],

        'tools' => [
            'align_center' => 'Ortala',
            'align_end' => 'Sona hizala',
            'align_justify' => 'İki yana yasla',
            'align_start' => 'Başa hizala',
            'attach_files' => 'Dosya ekle',
            'blockquote' => 'Alıntı',
            'bold' => 'Kalın',
            'bullet_list' => 'Madde listesi',
            'clear_formatting' => 'Biçimlendirmeyi temizle',
            'code' => 'Kod',
            'code_block' => 'Kod bloğu',
            'custom_blocks' => 'Bloklar',
            'details' => 'Detaylar',
            'h1' => 'Başlık',
            'h2' => 'Başlık 2',
            'h3' => 'Başlık 3',
            'h4' => 'Başlık 4',
            'h5' => 'Başlık 5',
            'h6' => 'Başlık 6',
            'grid' => 'Izgara',
            'grid_delete' => 'Izgarayı sil',
            'highlight' => 'Vurgula',
            'horizontal_rule' => 'Yatay çizgi',
            'italic' => 'İtalik',
            'lead' => 'Giriş metni',
            'link' => 'Bağlantı',
            'merge_tags' => 'Birleştirme etiketleri',
            'ordered_list' => 'Numaralı liste',
            'paragraph' => 'Paragraf',
            'redo' => 'Yinele',
            'small' => 'Küçük metin',
            'strike' => 'Üstü çizili',
            'subscript' => 'Alt simge',
            'superscript' => 'Üst simge',
            'table' => 'Tablo',
            'table_delete' => 'Tabloyu sil',
            'table_add_column_before' => 'Önüne sütun ekle',
            'table_add_column_after' => 'Sonuna sütun ekle',
            'table_delete_column' => 'Sütunu sil',
            'table_add_row_before' => 'Üstüne satır ekle',
            'table_add_row_after' => 'Altına satır ekle',
            'table_delete_row' => 'Satırı sil',
            'table_merge_cells' => 'Hücreleri birleştir',
            'table_split_cell' => 'Hücreyi böl',
            'table_toggle_header_row' => 'Başlık satırını aç/kapat',
            'table_toggle_header_cell' => 'Başlık hücresini aç/kapat',
            'text_color' => 'Metin rengi',
            'underline' => 'Altı çizili',
            'undo' => 'Geri al',
        ],

        'uploading_file_message' => 'Dosya yükleniyor...',

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'label' => 'Oluştur',

                'modal' => [

                    'heading' => 'Oluştur',

                    'actions' => [

                        'create' => [
                            'label' => 'Oluştur',
                        ],

                        'create_another' => [
                            'label' => 'Oluştur ve yenisini ekle',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'label' => 'Düzenle',

                'modal' => [

                    'heading' => 'Düzenle',

                    'actions' => [

                        'save' => [
                            'label' => 'Kaydet',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'Evet',
            'false' => 'Hayır',
        ],

        'loading_message' => 'Yükleniyor...',

        'max_items_message' => 'Yalnızca :count tane seçilebilir.',

        'no_options_message' => 'Seçenek bulunamadı.',

        'no_search_results_message' => 'Aramanızla eşleşen seçenek yok.',

        'placeholder' => 'Bir seçenek seçin',

        'searching_message' => 'Aranıyor...',

        'search_prompt' => 'Aramak için yazmaya başlayın...',

    ],

    'tags_input' => [

        'actions' => [

            'delete' => [
                'label' => 'Sil',
            ],

        ],

        'placeholder' => 'Yeni etiket',

    ],

    'text_input' => [

        'actions' => [

            'copy' => [
                'label' => 'Kopyala',
                'message' => 'Kopyalandı',
            ],

            'hide_password' => [
                'label' => 'Şifreyi gizle',
            ],

            'show_password' => [
                'label' => 'Şifreyi göster',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => 'Evet',
            'false' => 'Hayır',
        ],

    ],

];
