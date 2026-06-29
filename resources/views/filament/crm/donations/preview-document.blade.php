<x-filament-panels::page wire:init="refreshPreview">
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-12">
        <div class="xl:col-span-7">
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <h3 class="mb-3 text-sm font-semibold text-gray-900 dark:text-white">Canlı Önizleme</h3>
                @if ($previewDataUri)
                    <img src="{{ $previewDataUri }}" alt="Belge önizleme" class="mx-auto w-full max-w-2xl rounded-lg border border-gray-200 shadow" />
                @else
                    <p class="text-sm text-gray-500">Önizleme yükleniyor veya oluşturulamadı.</p>
                @endif
            </div>
        </div>

        <div class="space-y-4 xl:col-span-5">
            @foreach ($fieldStates as $fieldKey => $state)
                <div wire:key="preview-field-{{ $fieldKey }}" class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <h4 class="mb-3 text-sm font-semibold text-gray-900 dark:text-white">{{ $state['label'] ?? $fieldKey }}</h4>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="space-y-1"><span class="text-xs text-gray-500">X</span><input type="number" wire:model.blur="fieldStates.{{ $fieldKey }}.x" class="fi-input block w-full rounded-lg border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900" /></label>
                        <label class="space-y-1"><span class="text-xs text-gray-500">Y</span><input type="number" wire:model.blur="fieldStates.{{ $fieldKey }}.y" class="fi-input block w-full rounded-lg border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900" /></label>
                        <label class="space-y-1"><span class="text-xs text-gray-500">Genişlik</span><input type="number" wire:model.blur="fieldStates.{{ $fieldKey }}.width" class="fi-input block w-full rounded-lg border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900" /></label>
                        <label class="space-y-1"><span class="text-xs text-gray-500">Yükseklik</span><input type="number" wire:model.blur="fieldStates.{{ $fieldKey }}.height" class="fi-input block w-full rounded-lg border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900" /></label>
                        <label class="space-y-1"><span class="text-xs text-gray-500">Boyut</span><input type="number" wire:model.blur="fieldStates.{{ $fieldKey }}.font_size" class="fi-input block w-full rounded-lg border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900" /></label>
                        <label class="space-y-1"><span class="text-xs text-gray-500">Renk</span><input type="color" wire:model.blur="fieldStates.{{ $fieldKey }}.color" class="h-10 w-full rounded-lg border border-gray-300 dark:border-gray-600" /></label>
                        <label class="col-span-2 space-y-1">
                            <span class="text-xs text-gray-500">Font</span>
                            <select wire:model.live="fieldStates.{{ $fieldKey }}.font_family" class="fi-select block w-full rounded-lg border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900">
                                @foreach ($this->getFontOptions() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="space-y-1">
                            <span class="text-xs text-gray-500">Hizalama</span>
                            <select wire:model.live="fieldStates.{{ $fieldKey }}.align" class="fi-select block w-full rounded-lg border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900">
                                <option value="left">Sol</option><option value="center">Orta</option><option value="right">Sağ</option>
                            </select>
                        </label>
                        <label class="space-y-1">
                            <span class="text-xs text-gray-500">Dikey</span>
                            <select wire:model.live="fieldStates.{{ $fieldKey }}.vertical_align" class="fi-select block w-full rounded-lg border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900">
                                <option value="top">Üst</option><option value="middle">Orta</option><option value="bottom">Alt</option>
                            </select>
                        </label>
                        <label class="col-span-2 space-y-1">
                            <span class="text-xs text-gray-500">Özel metin (boş = otomatik)</span>
                            <textarea wire:model.blur="fieldStates.{{ $fieldKey }}.text_override" rows="2" class="fi-input block w-full rounded-lg border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900"></textarea>
                        </label>
                    </div>
                </div>
            @endforeach

            <button type="button" wire:click="refreshPreview" class="w-full rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-medium text-white shadow hover:bg-primary-500">
                Değişiklikleri önizle
            </button>
        </div>
    </div>
</x-filament-panels::page>
