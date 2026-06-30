@props([
    'showPreview' => true,
    'showReset' => true,
    'showSave' => true,
    'previewMethod' => 'renderPreview',
    'resetMethod' => 'resetDefaults',
    'saveMethod' => 'saveTemplateFields',
    'previewLabel' => 'Örnek Veriyle Önizle',
    'resetLabel' => 'Varsayılanlara sıfırla',
    'saveLabel' => 'Kaydet',
    'resetConfirm' => 'Varsayılan alan konumları yüklenecek. Mevcut düzenlemeler kaybolur. Emin misiniz?',
    'extraButtons' => [],
])

<div {{ $attributes->merge(['class' => 'poster-page-actions']) }}>
    @if ($showPreview)
        <button
            type="button"
            class="poster-page-btn poster-page-btn--info"
            wire:click="{{ $previewMethod }}"
            wire:loading.attr="disabled"
            wire:target="{{ $previewMethod }}"
        >
            <span wire:loading.remove wire:target="{{ $previewMethod }}">{{ $previewLabel }}</span>
            <span wire:loading wire:target="{{ $previewMethod }}">Önizleniyor…</span>
        </button>
    @endif

    @foreach ($extraButtons as $button)
        <button
            type="button"
            class="poster-page-btn poster-page-btn--{{ $button['color'] ?? 'secondary' }}"
            wire:click="{{ $button['method'] }}"
            @if (! empty($button['confirm'])) wire:confirm="{{ $button['confirm'] }}" @endif
            wire:loading.attr="disabled"
            wire:target="{{ $button['method'] }}"
        >
            <span wire:loading.remove wire:target="{{ $button['method'] }}">{{ $button['label'] }}</span>
            <span wire:loading wire:target="{{ $button['method'] }}">İşleniyor…</span>
        </button>
    @endforeach

    @if ($showReset)
        <button
            type="button"
            class="poster-page-btn poster-page-btn--warning"
            wire:click="{{ $resetMethod }}"
            wire:confirm="{{ $resetConfirm }}"
            wire:loading.attr="disabled"
            wire:target="{{ $resetMethod }}"
        >
            <span wire:loading.remove wire:target="{{ $resetMethod }}">{{ $resetLabel }}</span>
            <span wire:loading wire:target="{{ $resetMethod }}">Sıfırlanıyor…</span>
        </button>
    @endif

    @if ($showSave)
        <button
            type="button"
            class="poster-page-btn poster-page-btn--primary"
            wire:click="{{ $saveMethod }}"
            wire:loading.attr="disabled"
            wire:target="{{ $saveMethod }}"
        >
            <span wire:loading.remove wire:target="{{ $saveMethod }}">{{ $saveLabel }}</span>
            <span wire:loading wire:target="{{ $saveMethod }}">Kaydediliyor…</span>
        </button>
    @endif
</div>
