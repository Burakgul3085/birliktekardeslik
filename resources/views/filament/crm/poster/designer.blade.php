@php
    $config = $this->getDesignerConfig();
@endphp

<x-filament-panels::page>
    @unless($config['backgroundUrl'])
        <x-filament::section>
            <div class="text-sm text-amber-700">
                Bu şablonun arka plan görseli yok. Tasarıma başlamak için önce
                <strong>Düzenle</strong> ekranından afişin boş halini (logolu/çerçeveli arka plan) yükleyin.
            </div>
        </x-filament::section>
    @endunless

    <div
        x-data
        x-on:poster-save.window="$wire.saveLayout($event.detail.layout, $event.detail.width, $event.detail.height)"
        wire:ignore
    >
        <div data-poster-editor>
            <script type="application/json" data-poster-config>@json($config)</script>

            <div data-poster-toolbar style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:14px;"></div>

            <div style="display:flex;gap:18px;align-items:flex-start;flex-wrap:wrap;">
                <div
                    data-poster-stage
                    style="flex:1 1 520px;min-width:300px;background:#f1f5f9;border-radius:14px;padding:14px;overflow:auto;display:flex;justify-content:center;"
                ></div>
                <div
                    data-poster-props
                    style="width:300px;flex:0 0 300px;background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:16px;"
                ></div>
            </div>
        </div>
    </div>

    <x-filament::section class="mt-4">
        <x-slot name="heading">Nasıl çalışır?</x-slot>
        <ul class="text-sm text-gray-600 list-disc ps-5 space-y-1">
            <li><strong>+ Yazı ekle</strong> ile metin katmanı ekleyin, sürükleyerek konumlandırın.</li>
            <li>Sağ panelden <strong>İçerik kaynağı</strong> seçin: sabit metin ya da bağıştan gelen bir alan (ör. Bağışçı ad soyad, Proje / Faaliyet, Tarih).</li>
            <li>Yazı tipi, boyut, renk ve hizalamayı ayarlayıp <strong>Şablonu kaydet</strong> deyin.</li>
            <li>Afiş üretildiğinde bağlı alanlar ilgili bağışın verisiyle otomatik doldurulur.</li>
        </ul>
    </x-filament::section>
</x-filament-panels::page>
