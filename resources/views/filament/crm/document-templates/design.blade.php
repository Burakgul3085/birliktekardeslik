<x-filament-panels::page>
    <div class="mb-4 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900 dark:border-amber-800 dark:bg-amber-950/40 dark:text-amber-100">
        <strong>Adımlar:</strong> Alanları sürükleyerek taşıyın, sağ alt köşeden boyutlandırın, sağ panelden font/renk ayarlayın → <strong>Örnek Veriyle Önizle</strong> → <strong>Kaydet</strong>.
        Kendi boş PNG'nizi yüklediyseniz konumları buna göre hizalayın.
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-12">
        <div class="xl:col-span-8">
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <p class="mb-3 text-sm text-gray-500 dark:text-gray-400">
                    Orijinal PNG boyutu: {{ $canvasWidth }}×{{ $canvasHeight }} px — koordinatlar bu ölçeğe göre kaydedilir.
                </p>
                <div
                    id="template-canvas"
                    class="relative mx-auto overflow-hidden rounded-xl border border-dashed border-gray-300 bg-gray-50 dark:border-gray-600 dark:bg-gray-800"
                    style="max-width: 100%; aspect-ratio: {{ $canvasWidth }} / {{ $canvasHeight }};"
                    x-data="templateDesigner(@js($canvasWidth), @js($canvasHeight))"
                    x-init="init()"
                    @mousemove.window="onPointerMove($event)"
                    @mouseup.window="endPointer()"
                >
                    <img
                        src="{{ $backgroundUrl }}"
                        alt="Şablon"
                        class="pointer-events-none absolute inset-0 h-full w-full object-fill"
                        draggable="false"
                    >

                    @foreach ($fields as $field)
                        <div
                            wire:key="canvas-field-{{ $field['id'] }}"
                            data-field-id="{{ $field['id'] }}"
                            data-x="{{ $field['x'] }}"
                            data-y="{{ $field['y'] }}"
                            data-width="{{ $field['width'] }}"
                            data-height="{{ $field['height'] }}"
                            class="template-field-box group absolute cursor-move rounded border-2 bg-primary-500/10 transition-shadow {{ ($selectedFieldId ?? '') === $field['id'] ? 'border-primary-500 shadow-lg ring-2 ring-primary-300/50' : 'border-primary-300/70' }}"
                            style="
                                left: {{ ($field['x'] / max($canvasWidth, 1)) * 100 }}%;
                                top: {{ ($field['y'] / max($canvasHeight, 1)) * 100 }}%;
                                width: {{ ($field['width'] / max($canvasWidth, 1)) * 100 }}%;
                                height: {{ ($field['height'] / max($canvasHeight, 1)) * 100 }}%;
                            "
                            @mousedown.stop="startDrag('{{ $field['id'] }}', $event)"
                            wire:click="selectField('{{ $field['id'] }}')"
                        >
                            <span class="absolute -top-6 left-0 rounded bg-primary-600 px-2 py-0.5 text-[10px] font-medium text-white">
                                {{ $field['label'] }}
                            </span>
                            <span
                                class="absolute bottom-0 right-0 h-3 w-3 translate-x-1/2 translate-y-1/2 cursor-se-resize rounded-full border-2 border-white bg-primary-600 opacity-0 shadow group-hover:opacity-100"
                                @mousedown.stop="startResize('{{ $field['id'] }}', $event)"
                            ></span>
                        </div>
                    @endforeach
                </div>
            </div>

            @if ($previewDataUri)
                <div class="mt-4 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <h3 class="mb-3 text-sm font-semibold text-gray-900 dark:text-white">Canlı Render Önizlemesi</h3>
                    <p class="mb-3 text-xs text-gray-500">Bu görüntü belge üretiminde kullanılan motor ile oluşturulmuştur.</p>
                    <img src="{{ $previewDataUri }}" alt="Önizleme" class="mx-auto max-w-full rounded-lg border border-gray-200 shadow" />
                </div>
            @endif
        </div>

        <div class="space-y-4 xl:col-span-4">
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <h3 class="mb-3 text-sm font-semibold text-gray-900 dark:text-white">Alanlar</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach ($fields as $field)
                        <button
                            type="button"
                            wire:click="selectField('{{ $field['id'] }}')"
                            class="rounded-lg border px-3 py-1.5 text-xs font-medium transition {{ ($selectedFieldId ?? '') === $field['id'] ? 'border-primary-500 bg-primary-50 text-primary-700 dark:bg-primary-950 dark:text-primary-200' : 'border-gray-200 text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-800' }}"
                        >
                            {{ $field['label'] }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <h3 class="mb-3 text-sm font-semibold text-gray-900 dark:text-white">Alan Ekle</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach ($this->getFieldKeyOptions() as $key => $label)
                        <button
                            type="button"
                            wire:click="addField('{{ $key }}')"
                            class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-800"
                        >
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            @if ($fields !== [])
                <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <h3 class="mb-3 text-sm font-semibold text-gray-900 dark:text-white">Alan Özellikleri</h3>

                    @foreach ($fields as $index => $field)
                        <div
                            wire:key="field-panel-{{ $field['id'] }}"
                            class="{{ ($selectedFieldId ?? '') === $field['id'] ? 'block' : 'hidden' }}"
                        >
                            <div class="mb-3 flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $field['label'] }}</span>
                                <button type="button" wire:click="removeField('{{ $field['id'] }}')" class="text-xs text-danger-600 hover:underline">Sil</button>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <label class="space-y-1"><span class="text-xs text-gray-500">X</span><input type="number" wire:model.blur="fields.{{ $index }}.x" class="fi-input block w-full rounded-lg border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900" /></label>
                                <label class="space-y-1"><span class="text-xs text-gray-500">Y</span><input type="number" wire:model.blur="fields.{{ $index }}.y" class="fi-input block w-full rounded-lg border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900" /></label>
                                <label class="space-y-1"><span class="text-xs text-gray-500">Genişlik</span><input type="number" wire:model.blur="fields.{{ $index }}.width" class="fi-input block w-full rounded-lg border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900" /></label>
                                <label class="space-y-1"><span class="text-xs text-gray-500">Yükseklik</span><input type="number" wire:model.blur="fields.{{ $index }}.height" class="fi-input block w-full rounded-lg border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900" /></label>
                            </div>

                            @if (($field['type'] ?? 'text') === 'text')
                                <div class="mt-3 grid grid-cols-2 gap-3">
                                    <label class="col-span-2 space-y-1">
                                        <span class="text-xs text-gray-500">Font</span>
                                        <select wire:model.live="fields.{{ $index }}.font_family" class="fi-select block w-full rounded-lg border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900">
                                            @foreach ($this->getFontOptions() as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </label>
                                    <label class="space-y-1"><span class="text-xs text-gray-500">Boyut</span><input type="number" wire:model.blur="fields.{{ $index }}.font_size" class="fi-input block w-full rounded-lg border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900" /></label>
                                    <label class="space-y-1"><span class="text-xs text-gray-500">Renk</span><input type="color" wire:model.blur="fields.{{ $index }}.color" class="h-10 w-full rounded-lg border border-gray-300 dark:border-gray-600" /></label>
                                    <label class="space-y-1">
                                        <span class="text-xs text-gray-500">Hizalama</span>
                                        <select wire:model.live="fields.{{ $index }}.align" class="fi-select block w-full rounded-lg border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900">
                                            <option value="left">Sol</option><option value="center">Orta</option><option value="right">Sağ</option>
                                        </select>
                                    </label>
                                    <label class="space-y-1">
                                        <span class="text-xs text-gray-500">Dikey</span>
                                        <select wire:model.live="fields.{{ $index }}.vertical_align" class="fi-select block w-full rounded-lg border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900">
                                            <option value="top">Üst</option><option value="middle">Orta</option><option value="bottom">Alt</option>
                                        </select>
                                    </label>
                                    <label class="space-y-1"><span class="text-xs text-gray-500">Satır yüksekliği</span><input type="number" step="0.05" wire:model.blur="fields.{{ $index }}.line_height" class="fi-input block w-full rounded-lg border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900" /></label>
                                    <label class="space-y-1"><span class="text-xs text-gray-500">Maks. satır</span><input type="number" wire:model.blur="fields.{{ $index }}.max_lines" class="fi-input block w-full rounded-lg border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900" /></label>
                                    <label class="col-span-2 flex items-center gap-2 text-sm"><input type="checkbox" wire:model.live="fields.{{ $index }}.auto_resize" class="rounded border-gray-300" /> Otomatik küçült</label>
                                    <label class="col-span-2 flex items-center gap-2 text-sm"><input type="checkbox" wire:model.live="fields.{{ $index }}.word_wrap" class="rounded border-gray-300" /> Satır kaydır</label>
                                </div>
                            @else
                                <p class="mt-3 text-xs text-gray-500">QR alanı — konum ve boyut yeterlidir.</p>
                            @endif
                        </div>
                    @endforeach

                    @if (! $selectedFieldId)
                        <p class="text-sm text-gray-500">Düzenlemek için tuvalde veya listeden bir alan seçin.</p>
                    @endif
                </div>
            @endif
        </div>
    </div>

    @script
    <script>
        Alpine.data('templateDesigner', (canvasWidth, canvasHeight) => ({
            canvasWidth,
            canvasHeight,
            mode: null,
            activeFieldId: null,
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
                const canvas = document.getElementById('template-canvas');
                if (!canvas) return;
                this.scale = canvas.clientWidth / this.canvasWidth;
            },

            box(fieldId) {
                return document.querySelector(`[data-field-id="${fieldId}"]`);
            },

            startDrag(fieldId, event) {
                const box = this.box(fieldId);
                if (!box) return;

                this.mode = 'drag';
                this.activeFieldId = fieldId;
                this.startX = event.clientX;
                this.startY = event.clientY;
                this.fieldStartX = parseInt(box.dataset.x, 10);
                this.fieldStartY = parseInt(box.dataset.y, 10);
                $wire.selectField(fieldId);
            },

            startResize(fieldId, event) {
                const box = this.box(fieldId);
                if (!box) return;

                this.mode = 'resize';
                this.activeFieldId = fieldId;
                this.startX = event.clientX;
                this.startY = event.clientY;
                this.fieldStartX = parseInt(box.dataset.x, 10);
                this.fieldStartY = parseInt(box.dataset.y, 10);
                this.fieldStartW = parseInt(box.dataset.width, 10);
                this.fieldStartH = parseInt(box.dataset.height, 10);
                $wire.selectField(fieldId);
            },

            onPointerMove(event) {
                if (!this.mode || !this.activeFieldId) return;

                const box = this.box(this.activeFieldId);
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
                    const newW = Math.max(40, Math.round(this.fieldStartW + deltaX));
                    const newH = Math.max(24, Math.round(this.fieldStartH + deltaY));
                    box.dataset.width = newW;
                    box.dataset.height = newH;
                    box.style.width = `${(newW / this.canvasWidth) * 100}%`;
                    box.style.height = `${(newH / this.canvasHeight) * 100}%`;
                }
            },

            endPointer() {
                if (!this.mode || !this.activeFieldId) return;

                const box = this.box(this.activeFieldId);
                if (box) {
                    const payload = {
                        x: parseInt(box.dataset.x, 10),
                        y: parseInt(box.dataset.y, 10),
                        width: parseInt(box.dataset.width, 10),
                        height: parseInt(box.dataset.height, 10),
                    };
                    $wire.updateFieldGeometry(this.activeFieldId, payload);
                }

                this.mode = null;
                this.activeFieldId = null;
            },
        }));
    </script>
    @endscript
</x-filament-panels::page>
