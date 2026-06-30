<?php

namespace App\Filament\Crm\Concerns;

trait InteractsWithPosterToolbar
{
    public string $toolbarColor = '#1B3A6B';

    public int $toolbarFontSize = 32;

    public string $toolbarFontFamily = 'DejaVuSans';

    public string $toolbarAlign = 'center';

    public bool $toolbarAutoResize = true;

    public bool $toolbarWordWrap = true;

    public function decreaseFontSize(): void
    {
        $this->toolbarFontSize = max(8, $this->toolbarFontSize - 2);
        $this->pushToolbarToField();
    }

    public function increaseFontSize(): void
    {
        $this->toolbarFontSize = min(120, $this->toolbarFontSize + 2);
        $this->pushToolbarToField();
    }

    public function setToolbarAlign(string $align): void
    {
        $this->toolbarAlign = $align;
        $this->pushToolbarToField();
    }

    public function updatedToolbarColor(): void
    {
        $this->pushToolbarToField();
    }

    public function updatedToolbarFontSize(): void
    {
        $this->toolbarFontSize = max(8, min(120, $this->toolbarFontSize));
        $this->pushToolbarToField();
    }

    public function updatedToolbarFontFamily(): void
    {
        $this->pushToolbarToField();
    }

    public function updatedToolbarAutoResize(): void
    {
        $this->pushToolbarToField();
    }

    public function updatedToolbarWordWrap(): void
    {
        $this->pushToolbarToField();
    }

    /**
     * @param  array<string, mixed>  $field
     */
    protected function pullToolbarFromField(array $field): void
    {
        if (($field['type'] ?? 'text') === 'qr') {
            return;
        }

        $this->toolbarColor = (string) ($field['color'] ?? '#1B3A6B');
        $this->toolbarFontSize = (int) ($field['font_size'] ?? 32);
        $this->toolbarFontFamily = (string) ($field['font_family'] ?? 'DejaVuSans');
        $this->toolbarAlign = (string) ($field['align'] ?? 'center');
        $this->toolbarAutoResize = (bool) ($field['auto_resize'] ?? true);
        $this->toolbarWordWrap = (bool) ($field['word_wrap'] ?? true);
    }

    /**
     * @return array<string, mixed>
     */
    protected function toolbarPatch(): array
    {
        return [
            'color' => $this->toolbarColor,
            'font_size' => $this->toolbarFontSize,
            'font_family' => $this->toolbarFontFamily,
            'align' => $this->toolbarAlign,
            'auto_resize' => $this->toolbarAutoResize,
            'word_wrap' => $this->toolbarWordWrap,
        ];
    }

    abstract protected function pushToolbarToField(): void;
}
