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
            <li><strong>+ Yazı kutusu ekle</strong> ile kutu oluşturun. Kutuyu tuvalde veya sağ paneldeki <strong>genişlik / yükseklik</strong> alanlarından boyutlandırın.</li>
            <li><strong>Uzun metin önizlemesi</strong> açıkken bağış notu ve teşekkür metni gerçek uzunlukta gösterilir; kutuyu buna göre ayarlayın.</li>
            <li>Yazı kutu içinde kalır: satırlara bölünür, sığacak şekilde ölçeklenir ve <strong>clipPath ile kesinlikle dışarı taşmaz</strong>.</li>
            <li>Sağ panelden içerik kaynağı, yazı tipi, istenen boyut, renk ve hizalamayı ayarlayıp <strong>Şablonu kaydet</strong> deyin.</li>
            <li>Afiş üretildiğinde veriler aynı kurallarla kutuya sığdırılır; bağış sayfasındaki düzenleme ekranı da aynı motoru kullanır.</li>
        </ul>
    </x-filament::section>
</x-filament-panels::page>
