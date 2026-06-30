@php
    $isQr = ($fieldType ?? 'text') === 'qr';
@endphp

<div class="poster-toolbar" wire:key="poster-toolbar-{{ $toolbarKey }}">
    <span class="mr-2 text-sm font-semibold text-gray-700 dark:text-gray-200">{{ $fieldLabel }}</span>

    @if (! $isQr)
        <button type="button" class="poster-toolbar-btn" wire:click="decreaseFontSize" title="Küçült">A−</button>
        <input
            type="number"
            wire:model.live="toolbarFontSize"
            min="8"
            max="120"
            class="h-9 w-14 rounded-lg border border-gray-200 text-center text-sm dark:border-gray-600 dark:bg-gray-900"
            title="Yazı boyutu"
        >
        <button type="button" class="poster-toolbar-btn" wire:click="increaseFontSize" title="Büyüt">A+</button>

        <input
            type="color"
            wire:model.live="toolbarColor"
            class="h-9 w-12 cursor-pointer rounded-lg border border-gray-200 p-0.5"
            title="Renk"
        >

        <select
            wire:model.live="toolbarFontFamily"
            class="h-9 max-w-[10rem] rounded-lg border border-gray-200 bg-white px-2 text-sm dark:border-gray-600 dark:bg-gray-900"
        >
            @foreach ($fontOptions as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>

        <button
            type="button"
            class="poster-toolbar-btn {{ $toolbarAlign === 'left' ? 'is-active' : '' }}"
            wire:click="setToolbarAlign('left')"
            title="Sola hizala"
        >⬅</button>
        <button
            type="button"
            class="poster-toolbar-btn {{ $toolbarAlign === 'center' ? 'is-active' : '' }}"
            wire:click="setToolbarAlign('center')"
            title="Ortala"
        >⬌</button>
        <button
            type="button"
            class="poster-toolbar-btn {{ $toolbarAlign === 'right' ? 'is-active' : '' }}"
            wire:click="setToolbarAlign('right')"
            title="Sağa hizala"
        >➡</button>

        <label class="flex cursor-pointer items-center gap-1 text-xs text-gray-500">
            <input type="checkbox" wire:model.live="toolbarAutoResize" class="rounded"> Oto küçült
        </label>
        <label class="flex cursor-pointer items-center gap-1 text-xs text-gray-500">
            <input type="checkbox" wire:model.live="toolbarWordWrap" class="rounded"> Satır kaydır
        </label>
    @else
        <span class="text-xs text-gray-500">QR alanını sürükleyerek konumlandırın</span>
    @endif

    @if (isset($deleteWireClick))
        <button
            type="button"
            class="poster-toolbar-btn text-danger-600"
            wire:click="{{ $deleteWireClick }}"
            wire:confirm="Bu alanı silmek istediğinize emin misiniz?"
        >Sil</button>
    @endif
</div>
