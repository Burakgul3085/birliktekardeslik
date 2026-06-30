@include('filament.crm.components.poster-editor-styles')

<x-filament-panels::page>
    @if (! $previewDocument || ! $backgroundUrl)
        <div class="rounded-xl border border-danger-200 bg-danger-50 p-6 text-danger-700">
            Şablon görseli bulunamadı. Önce Afiş Şablonları bölümünden PNG yükleyin.
        </div>
    @else
        <div class="mb-3 flex flex-wrap items-center gap-2">
            <span class="text-sm text-gray-500 dark:text-gray-400">
                Afiş üzerinde sürükleyin, köşelerden boyutlandırın, çift tıklayarak metni düzenleyin.
            </span>
        </div>

        <div class="mb-4 flex flex-wrap gap-2">
            @foreach ($fieldStates as $fieldKey => $state)
                <button
                    type="button"
                    wire:click="selectField('{{ $fieldKey }}')"
                    class="poster-chip {{ $selectedFieldKey === $fieldKey ? 'is-active' : '' }}"
                >
                    {{ $state['label'] ?? $fieldKey }}
                </button>
            @endforeach
        </div>

        <div class="poster-workspace">
            <div class="poster-canvas-wrap">
                <div
                    id="poster-canvas"
                    class="relative bg-white"
                    style="aspect-ratio: {{ $canvasWidth }} / {{ $canvasHeight }};"
                    x-data="posterEditor(@js($canvasWidth), @js($canvasHeight))"
                    x-init="init()"
                    @mousemove.window="onPointerMove($event)"
                    @mouseup.window="endPointer()"
                    @click.self="$wire.set('selectedFieldKey', null)"
                >
                    <img
                        src="{{ $backgroundUrl }}"
                        alt="Afiş şablonu"
                        class="pointer-events-none absolute inset-0 h-full w-full object-fill"
                        draggable="false"
                    >

                    @foreach ($fieldStates as $fieldKey => $state)
                        @php
                            $isQr = ($state['type'] ?? 'text') === 'qr';
                            $displayText = $this->getDisplayText($fieldKey);
                            $fontWeight = str_contains((string) ($state['font_family'] ?? ''), 'Bold') ? '700' : '400';
                            $fontFamily = $this->getWebFontFamily((string) ($state['font_family'] ?? 'DejaVuSans'));
                            $justify = match ($state['align'] ?? 'center') {
                                'left' => 'flex-start',
                                'right' => 'flex-end',
                                default => 'center',
                            };
                            $items = match ($state['vertical_align'] ?? 'middle') {
                                'top' => 'flex-start',
                                'bottom' => 'flex-end',
                                default => 'center',
                            };
                        @endphp
                        <div
                            wire:key="poster-field-{{ $fieldKey }}"
                            data-field-id="{{ $fieldKey }}"
                            data-x="{{ $state['x'] }}"
                            data-y="{{ $state['y'] }}"
                            data-width="{{ $state['width'] }}"
                            data-height="{{ $state['height'] }}"
                            class="poster-field group {{ $selectedFieldKey === $fieldKey ? 'is-selected' : '' }}"
                            style="
                                left: {{ ($state['x'] / max($canvasWidth, 1)) * 100 }}%;
                                top: {{ ($state['y'] / max($canvasHeight, 1)) * 100 }}%;
                                width: {{ ($state['width'] / max($canvasWidth, 1)) * 100 }}%;
                                height: {{ ($state['height'] / max($canvasHeight, 1)) * 100 }}%;
                            "
                            @mousedown.stop="startDrag('{{ $fieldKey }}', $event)"
                            @click.stop="$wire.selectField('{{ $fieldKey }}')"
                        >
                            @if ($isQr)
                                <div class="flex h-full w-full items-center justify-center bg-white/80 p-1">
                                    <img
                                        src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode($fieldValues['qr_code'] ?? '') }}"
                                        alt="QR"
                                        class="h-full w-full object-contain"
                                        draggable="false"
                                    >
                                </div>
                            @else
                                <div
                                    class="poster-field-content flex"
                                    style="
                                        color: {{ $state['color'] ?? '#1B3A6B' }};
                                        font-family: {!! $fontFamily !!};
                                        font-weight: {{ $fontWeight }};
                                        text-align: {{ $state['align'] ?? 'center' }};
                                        justify-content: {{ $justify }};
                                        align-items: {{ $items }};
                                        --field-font-size: {{ (int) ($state['font_size'] ?? 32) }};
                                        font-size: calc(var(--field-font-size) * var(--canvas-scale, 0.3) * 1px);
                                    "
                                    @dblclick.stop="startTextEdit($event, '{{ $fieldKey }}')"
                                >{{ $displayText }}</div>
                            @endif

                            @if ($selectedFieldKey === $fieldKey)
                                <span class="poster-handle nw" @mousedown.stop="startResize('{{ $fieldKey }}', 'nw', $event)"></span>
                                <span class="poster-handle ne" @mousedown.stop="startResize('{{ $fieldKey }}', 'ne', $event)"></span>
                                <span class="poster-handle sw" @mousedown.stop="startResize('{{ $fieldKey }}', 'sw', $event)"></span>
                                <span class="poster-handle se" @mousedown.stop="startResize('{{ $fieldKey }}', 'se', $event)"></span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            @if ($selectedFieldKey && isset($fieldStates[$selectedFieldKey]))
                @php $sel = $fieldStates[$selectedFieldKey]; @endphp
                <div class="poster-toolbar" wire:key="toolbar-{{ $selectedFieldKey }}">
                    <span class="mr-2 text-sm font-semibold text-gray-700 dark:text-gray-200">{{ $sel['label'] }}</span>

                    @if (($sel['type'] ?? 'text') !== 'qr')
                        <button type="button" class="poster-toolbar-btn" wire:click="nudgeFontSize(-2)" title="Küçült">A−</button>
                        <span class="min-w-[2.5rem] text-center text-sm font-medium text-gray-600 dark:text-gray-300">{{ $sel['font_size'] ?? 32 }}</span>
                        <button type="button" class="poster-toolbar-btn" wire:click="nudgeFontSize(2)" title="Büyüt">A+</button>

                        <input
                            type="color"
                            wire:model.live="fieldStates.{{ $selectedFieldKey }}.color"
                            class="h-9 w-12 cursor-pointer rounded-lg border border-gray-200"
                            title="Renk"
                        >

                        <select
                            wire:model.live="fieldStates.{{ $selectedFieldKey }}.font_family"
                            class="h-9 rounded-lg border border-gray-200 bg-white px-2 text-sm dark:border-gray-600 dark:bg-gray-900"
                        >
                            @foreach ($this->getFontOptions() as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>

                        <button
                            type="button"
                            class="poster-toolbar-btn {{ ($sel['align'] ?? '') === 'left' ? 'is-active' : '' }}"
                            wire:click="$set('fieldStates.{{ $selectedFieldKey }}.align', 'left')"
                            title="Sola hizala"
                        >⬅</button>
                        <button
                            type="button"
                            class="poster-toolbar-btn {{ ($sel['align'] ?? '') === 'center' ? 'is-active' : '' }}"
                            wire:click="$set('fieldStates.{{ $selectedFieldKey }}.align', 'center')"
                            title="Ortala"
                        >⬌</button>
                        <button
                            type="button"
                            class="poster-toolbar-btn {{ ($sel['align'] ?? '') === 'right' ? 'is-active' : '' }}"
                            wire:click="$set('fieldStates.{{ $selectedFieldKey }}.align', 'right')"
                            title="Sağa hizala"
                        >➡</button>
                    @else
                        <span class="text-xs text-gray-500">QR alanını sürükleyerek konumlandırın</span>
                    @endif
                </div>
            @endif
        </div>

        @if ($previewDataUri)
            <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <h3 class="mb-2 text-sm font-semibold text-gray-900 dark:text-white">PDF Render Önizlemesi</h3>
                <p class="mb-3 text-xs text-gray-500">Üstteki düzenleme tuvali canlıdır; bu görüntü kayıt sonrası üretilen gerçek PDF/PNG çıktısıdır.</p>
                <img src="{{ $previewDataUri }}" alt="Render önizleme" class="mx-auto max-h-[480px] rounded-lg border border-gray-200 shadow" />
            </div>
        @endif
    @endif

    @include('filament.crm.components.poster-editor-script')
</x-filament-panels::page>
