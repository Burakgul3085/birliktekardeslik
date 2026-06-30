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
            x-on:click="$wire.{{ $previewMethod }}()"
        >
            {{ $previewLabel }}
        </button>
    @endif

    @foreach ($extraButtons as $button)
        @php
            $confirm = $button['confirm'] ?? null;
            $click = $confirm
                ? "if (confirm(" . json_encode($confirm, JSON_UNESCAPED_UNICODE) . ")) { \$wire.{$button['method']}() }"
                : "\$wire.{$button['method']}()";
        @endphp
        <button
            type="button"
            class="poster-page-btn poster-page-btn--{{ $button['color'] ?? 'secondary' }}"
            x-on:click="{{ $click }}"
        >
            {{ $button['label'] }}
        </button>
    @endforeach

    @if ($showReset)
        <button
            type="button"
            class="poster-page-btn poster-page-btn--warning"
            x-on:click="if (confirm({{ json_encode($resetConfirm, JSON_UNESCAPED_UNICODE) }})) { $wire.{{ $resetMethod }}() }"
        >
            {{ $resetLabel }}
        </button>
    @endif

    @if ($showSave)
        <button
            type="button"
            class="poster-page-btn poster-page-btn--primary"
            x-on:click="$wire.{{ $saveMethod }}()"
        >
            {{ $saveLabel }}
        </button>
    @endif
</div>
