@script
<script>
    function refreshPosterCanvasScale() {
        const canvas = document.getElementById('poster-canvas');
        if (!canvas) return;

        const width = parseInt(canvas.dataset.canvasWidth, 10) || 2480;
        const scale = canvas.clientWidth > 0 ? canvas.clientWidth / width : 0.3;
        canvas.style.setProperty('--canvas-scale', scale);
    }

    if (! window.__posterEditorRegistered) {
        window.__posterEditorRegistered = true;

        document.addEventListener('livewire:init', () => {
            Livewire.hook('morph.updated', () => {
                requestAnimationFrame(refreshPosterCanvasScale);
            });
        });

        document.addEventListener('alpine:init', () => {
            Alpine.data('posterEditor', (canvasWidth, canvasHeight) => ({
        canvasWidth,
        canvasHeight,
        mode: null,
        resizeCorner: null,
        activeFieldId: null,
        startX: 0,
        startY: 0,
        fieldStartX: 0,
        fieldStartY: 0,
        fieldStartW: 0,
        fieldStartH: 0,
        scale: 1,

        init() {
            this.$nextTick(() => {
                refreshPosterCanvasScale();
                this.recalculateScale();
            });
            window.addEventListener('resize', () => {
                refreshPosterCanvasScale();
                this.recalculateScale();
            });
        },

        recalculateScale() {
            const canvas = document.getElementById('poster-canvas');
            if (!canvas) return;
            const width = parseInt(canvas.dataset.canvasWidth, 10) || this.canvasWidth;
            this.scale = canvas.clientWidth / width;
            canvas.style.setProperty('--canvas-scale', this.scale);
        },

        box(fieldId) {
            return document.querySelector(`[data-field-id="${fieldId}"]`);
        },

        startDrag(fieldId, event) {
            const box = this.box(fieldId);
            if (!box || event.target.isContentEditable) return;

            this.mode = 'drag';
            this.activeFieldId = fieldId;
            this.startX = event.clientX;
            this.startY = event.clientY;
            this.fieldStartX = parseInt(box.dataset.x, 10);
            this.fieldStartY = parseInt(box.dataset.y, 10);
            $wire.selectField(fieldId);
        },

        startResize(fieldId, corner, event) {
            const box = this.box(fieldId);
            if (!box) return;

            this.mode = 'resize';
            this.resizeCorner = corner;
            this.activeFieldId = fieldId;
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

            const finish = () => {
                el.contentEditable = 'false';
                if (typeof $wire.updateFieldText === 'function') {
                    $wire.updateFieldText(fieldKey, el.innerText.trim());
                }
                el.removeEventListener('blur', finish);
            };
            el.addEventListener('blur', finish);
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
            if (!this.mode || !this.activeFieldId) return;

            const box = this.box(this.activeFieldId);
            if (box) {
                $wire.updateFieldGeometry(this.activeFieldId, {
                    x: parseInt(box.dataset.x, 10),
                    y: parseInt(box.dataset.y, 10),
                    width: parseInt(box.dataset.width, 10),
                    height: parseInt(box.dataset.height, 10),
                });
            }

            this.mode = null;
            this.resizeCorner = null;
            this.activeFieldId = null;
        },
            }));
        });
    }
</script>
@endscript
