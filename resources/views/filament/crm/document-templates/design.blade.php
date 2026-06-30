@include('filament.crm.components.poster-editor-styles')

<x-filament-panels::page>
    @if (! $backgroundUrl)
        <div class="rounded-xl border border-danger-200 bg-danger-50 p-6 text-danger-700">
            Önce şablon PNG görseli yükleyin.
        </div>
    @else
        @include('filament.crm.components.poster-editor-page-actions')

        <div class="mb-3 text-sm text-gray-500 dark:text-gray-400">
            Afiş üzerinde alanları sürükleyin, köşelerden boyutlandırın. Alttaki araç çubuğundan font ve renk ayarlayın, sonra <strong>Kaydet</strong>.
        </div>

        <div class="mb-4 flex flex-wrap items-center gap-2">
            @foreach ($fields as $field)
                <button
                    type="button"
                    wire:click="selectField('{{ $field['id'] }}')"
                    class="poster-chip {{ $selectedFieldId === $field['id'] ? 'is-active' : '' }}"
                >
                    {{ $field['label'] }}
                </button>
            @endforeach

            <span class="mx-1 text-gray-300">|</span>

            @foreach ($this->getFieldKeyOptions() as $key => $label)
                <button
                    type="button"
                    wire:click="addField('{{ $key }}')"
                    class="poster-chip border-dashed text-gray-500 hover:border-primary-400 hover:text-primary-600"
                >
                    + {{ $label }}
                </button>
            @endforeach
        </div>

        <div class="poster-workspace">
            <div class="poster-canvas-wrap">
                <div
                    id="poster-canvas"
                    class="relative bg-white"
                    data-canvas-width="{{ $canvasWidth }}"
                    data-canvas-height="{{ $canvasHeight }}"
                    style="aspect-ratio: {{ $canvasWidth }} / {{ $canvasHeight }};"
                    x-data="posterEditor(@js($canvasWidth), @js($canvasHeight))"
                    x-init="init()"
                    @mousemove.window="onPointerMove($event)"
                    @mouseup.window="endPointer()"
                    @click.self="$wire.set('selectedFieldId', null)"
                >
                    <img
                        src="{{ $backgroundUrl }}"
                        alt="Şablon"
                        class="pointer-events-none absolute inset-0 h-full w-full object-fill"
                        draggable="false"
                    >

                    @foreach ($fields as $field)
                        @php
                            $isQr = ($field['type'] ?? 'text') === 'qr';
                            $displayText = $this->getDisplayText($field);
                            $fontWeight = str_contains((string) ($field['font_family'] ?? ''), 'Bold') ? '700' : '400';
                            $fontFamily = $this->getWebFontFamily((string) ($field['font_family'] ?? 'DejaVuSans'));
                            $justify = match ($field['align'] ?? 'center') {
                                'left' => 'flex-start',
                                'right' => 'flex-end',
                                default => 'center',
                            };
                            $items = match ($field['vertical_align'] ?? 'middle') {
                                'top' => 'flex-start',
                                'bottom' => 'flex-end',
                                default => 'center',
                            };
                        @endphp
                        <div
                            wire:key="template-field-{{ $field['id'] }}"
                            data-field-id="{{ $field['id'] }}"
                            data-x="{{ $field['x'] }}"
                            data-y="{{ $field['y'] }}"
                            data-width="{{ $field['width'] }}"
                            data-height="{{ $field['height'] }}"
                            class="poster-field {{ $selectedFieldId === $field['id'] ? 'is-selected' : '' }}"
                            style="
                                left: {{ ($field['x'] / max($canvasWidth, 1)) * 100 }}%;
                                top: {{ ($field['y'] / max($canvasHeight, 1)) * 100 }}%;
                                width: {{ ($field['width'] / max($canvasWidth, 1)) * 100 }}%;
                                height: {{ ($field['height'] / max($canvasHeight, 1)) * 100 }}%;
                            "
                            @mousedown.stop="startDrag('{{ $field['id'] }}', $event)"
                            @click.stop="$wire.selectField('{{ $field['id'] }}')"
                        >
                            @if ($isQr)
                                <div class="flex h-full w-full items-center justify-center bg-white/80 p-1">
                                    <img
                                        src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode($sampleValues['qr_code'] ?? 'SAMPLE') }}"
                                        alt="QR"
                                        class="h-full w-full object-contain"
                                        draggable="false"
                                    >
                                </div>
                            @else
                                <div
                                    class="poster-field-content flex"
                                    style="
                                        color: {{ $field['color'] ?? '#1B3A6B' }};
                                        font-family: {!! $fontFamily !!};
                                        font-weight: {{ $fontWeight }};
                                        text-align: {{ $field['align'] ?? 'center' }};
                                        justify-content: {{ $justify }};
                                        align-items: {{ $items }};
                                        --field-font-size: {{ (int) ($field['font_size'] ?? 32) }};
                                        font-size: calc(var(--field-font-size) * var(--canvas-scale, 0.3) * 1px);
                                    "
                                >{{ $displayText }}</div>
                            @endif

                            @if ($selectedFieldId === $field['id'])
                                <span class="poster-handle nw" @mousedown.stop="startResize('{{ $field['id'] }}', 'nw', $event)"></span>
                                <span class="poster-handle ne" @mousedown.stop="startResize('{{ $field['id'] }}', 'ne', $event)"></span>
                                <span class="poster-handle sw" @mousedown.stop="startResize('{{ $field['id'] }}', 'sw', $event)"></span>
                                <span class="poster-handle se" @mousedown.stop="startResize('{{ $field['id'] }}', 'se', $event)"></span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            @php $selIndex = $this->getSelectedFieldIndex(); @endphp
            @if ($selIndex !== null && isset($fields[$selIndex]))
                @php $sel = $fields[$selIndex]; @endphp
                @include('filament.crm.components.poster-editor-toolbar', [
                    'toolbarKey' => $sel['id'],
                    'fieldLabel' => $sel['label'],
                    'fieldType' => $sel['type'] ?? 'text',
                    'fontOptions' => $this->getFontOptions(),
                    'deleteWireClick' => "removeField('{$sel['id']}')",
                ])
            @endif
        </div>

        @if ($previewImageUrl || $previewDataUri)
            <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <h3 class="mb-2 text-sm font-semibold">PDF Render Önizlemesi</h3>
                <img
                    src="{{ $previewImageUrl ?? $previewDataUri }}"
                    alt="Render"
                    class="mx-auto max-h-[480px] rounded-lg border shadow"
                />
            </div>
        @endif
    @endif

    @include('filament.crm.components.poster-editor-script')
</x-filament-panels::page>
