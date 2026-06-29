<style>
        .poster-workspace {
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            min-height: calc(100vh - 12rem);
            border-radius: 1rem;
            padding: 1.5rem;
        }
        .dark .poster-workspace {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        }
        .poster-canvas-wrap {
            max-width: min(100%, 720px);
            margin: 0 auto;
            box-shadow: 0 25px 50px -12px rgb(0 0 0 / 0.25);
            border-radius: 0.5rem;
            overflow: hidden;
        }
        .poster-field {
            position: absolute;
            border: 2px dashed transparent;
            border-radius: 4px;
            transition: border-color 0.15s, box-shadow 0.15s;
            touch-action: none;
        }
        .poster-field:hover {
            border-color: rgb(59 130 246 / 0.45);
        }
        .poster-field.is-selected {
            border-color: #2563eb;
            border-style: solid;
            box-shadow: 0 0 0 3px rgb(37 99 235 / 0.2);
            z-index: 20;
        }
        .poster-field-content {
            width: 100%;
            height: 100%;
            overflow: hidden;
            padding: 2px 4px;
            line-height: 1.35;
            word-break: break-word;
            pointer-events: none;
        }
        .poster-field.is-selected .poster-field-content {
            pointer-events: auto;
        }
        .poster-field-content[contenteditable="true"] {
            outline: none;
            background: rgb(255 255 255 / 0.85);
            border-radius: 2px;
        }
        .poster-handle {
            position: absolute;
            width: 10px;
            height: 10px;
            background: #2563eb;
            border: 2px solid #fff;
            border-radius: 50%;
            box-shadow: 0 1px 3px rgb(0 0 0 / 0.3);
            z-index: 30;
        }
        .poster-handle.se { bottom: -5px; right: -5px; cursor: se-resize; }
        .poster-handle.sw { bottom: -5px; left: -5px; cursor: sw-resize; }
        .poster-handle.ne { top: -5px; right: -5px; cursor: ne-resize; }
        .poster-handle.nw { top: -5px; left: -5px; cursor: nw-resize; }
        .poster-toolbar {
            position: sticky;
            bottom: 1rem;
            z-index: 40;
            margin-top: 1rem;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            background: rgb(255 255 255 / 0.95);
            backdrop-filter: blur(8px);
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            box-shadow: 0 10px 25px -5px rgb(0 0 0 / 0.15);
        }
        .dark .poster-toolbar {
            background: rgb(15 23 42 / 0.95);
            border-color: #334155;
        }
        .poster-toolbar-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2.25rem;
            height: 2.25rem;
            padding: 0 0.5rem;
            border-radius: 0.5rem;
            border: 1px solid #e2e8f0;
            background: #fff;
            font-size: 0.875rem;
            color: #334155;
            transition: all 0.15s;
        }
        .dark .poster-toolbar-btn {
            background: #1e293b;
            border-color: #475569;
            color: #e2e8f0;
        }
        .poster-toolbar-btn:hover { background: #f8fafc; }
        .poster-toolbar-btn.is-active {
            background: #2563eb;
            border-color: #2563eb;
            color: #fff;
        }
        .poster-chip {
            border-radius: 9999px;
            padding: 0.35rem 0.85rem;
            font-size: 0.75rem;
            font-weight: 500;
            border: 1px solid #cbd5e1;
            background: #fff;
            transition: all 0.15s;
        }
        .poster-chip.is-active {
            background: #2563eb;
            border-color: #2563eb;
            color: #fff;
        }
    </style>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Lora:wght@400;600;700&display=swap" rel="stylesheet">

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
                            data-field-key="{{ $fieldKey }}"
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

    @script
    <script>
        Alpine.data('posterEditor', (canvasWidth, canvasHeight) => ({
            canvasWidth,
            canvasHeight,
            mode: null,
            resizeCorner: null,
            activeFieldKey: null,
            startX: 0,
            startY: 0,
            fieldStartX: 0,
            fieldStartY: 0,
            fieldStartW: 0,
            fieldStartH: 0,
            scale: 1,

            init() {
                this.$nextTick(() => this.recalculateScale());
                window.addEventListener('resize', () => this.recalculateScale());
            },

            recalculateScale() {
                const canvas = document.getElementById('poster-canvas');
                if (!canvas) return;
                this.scale = canvas.clientWidth / this.canvasWidth;
                canvas.style.setProperty('--canvas-scale', this.scale);
            },

            box(fieldKey) {
                return document.querySelector(`[data-field-key="${fieldKey}"]`);
            },

            startDrag(fieldKey, event) {
                const box = this.box(fieldKey);
                if (!box || event.target.isContentEditable) return;

                this.mode = 'drag';
                this.activeFieldKey = fieldKey;
                this.startX = event.clientX;
                this.startY = event.clientY;
                this.fieldStartX = parseInt(box.dataset.x, 10);
                this.fieldStartY = parseInt(box.dataset.y, 10);
                $wire.selectField(fieldKey);
            },

            startResize(fieldKey, corner, event) {
                const box = this.box(fieldKey);
                if (!box) return;

                this.mode = 'resize';
                this.resizeCorner = corner;
                this.activeFieldKey = fieldKey;
                this.startX = event.clientX;
                this.startY = event.clientY;
                this.fieldStartX = parseInt(box.dataset.x, 10);
                this.fieldStartY = parseInt(box.dataset.y, 10);
                this.fieldStartW = parseInt(box.dataset.width, 10);
                this.fieldStartH = parseInt(box.dataset.height, 10);
            },

            startTextEdit(event, fieldKey) {
                const el = event.currentTarget;
                el.contentEditable = 'true';
                el.focus();
                document.execCommand('selectAll', false, null);

                const finish = () => {
                    el.contentEditable = 'false';
                    $wire.updateFieldText(fieldKey, el.innerText.trim());
                    el.removeEventListener('blur', finish);
                };
                el.addEventListener('blur', finish);
            },

            onPointerMove(event) {
                if (!this.mode || !this.activeFieldKey) return;

                const box = this.box(this.activeFieldKey);
                if (!box) return;

                const deltaX = (event.clientX - this.startX) / this.scale;
                const deltaY = (event.clientY - this.startY) / this.scale;

                if (this.mode === 'drag') {
                    const newX = Math.max(0, Math.round(this.fieldStartX + deltaX));
                    const newY = Math.max(0, Math.round(this.fieldStartY + deltaY));
                    box.dataset.x = newX;
                    box.dataset.y = newY;
                    box.style.left = `${(newX / this.canvasWidth) * 100}%`;
                    box.style.top = `${(newY / this.canvasHeight) * 100}%`;
                }

                if (this.mode === 'resize') {
                    let newX = this.fieldStartX;
                    let newY = this.fieldStartY;
                    let newW = this.fieldStartW;
                    let newH = this.fieldStartH;

                    if (this.resizeCorner.includes('e')) {
                        newW = Math.max(40, Math.round(this.fieldStartW + deltaX));
                    }
                    if (this.resizeCorner.includes('w')) {
                        newW = Math.max(40, Math.round(this.fieldStartW - deltaX));
                        newX = Math.max(0, Math.round(this.fieldStartX + deltaX));
                    }
                    if (this.resizeCorner.includes('s')) {
                        newH = Math.max(24, Math.round(this.fieldStartH + deltaY));
                    }
                    if (this.resizeCorner.includes('n')) {
                        newH = Math.max(24, Math.round(this.fieldStartH - deltaY));
                        newY = Math.max(0, Math.round(this.fieldStartY + deltaY));
                    }

                    box.dataset.x = newX;
                    box.dataset.y = newY;
                    box.dataset.width = newW;
                    box.dataset.height = newH;
                    box.style.left = `${(newX / this.canvasWidth) * 100}%`;
                    box.style.top = `${(newY / this.canvasHeight) * 100}%`;
                    box.style.width = `${(newW / this.canvasWidth) * 100}%`;
                    box.style.height = `${(newH / this.canvasHeight) * 100}%`;
                }
            },

            endPointer() {
                if (!this.mode || !this.activeFieldKey) return;

                const box = this.box(this.activeFieldKey);
                if (box) {
                    $wire.updateFieldGeometry(this.activeFieldKey, {
                        x: parseInt(box.dataset.x, 10),
                        y: parseInt(box.dataset.y, 10),
                        width: parseInt(box.dataset.width, 10),
                        height: parseInt(box.dataset.height, 10),
                    });
                }

                this.mode = null;
                this.resizeCorner = null;
                this.activeFieldKey = null;
            },
        }));
    </script>
    @endscript
</x-filament-panels::page>
