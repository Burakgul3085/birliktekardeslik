import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.data('homeHeroSlider', (config = {}) => ({
        slides: Array.isArray(config.slides) ? config.slides : [],
        logoUrl: typeof config.logoUrl === 'string' ? config.logoUrl : '',
        idx: 0,
        touchStartX: null,

        get current() {
            return this.slides[this.idx] ?? {};
        },

        get total() {
            return this.slides.length;
        },

        next() {
            if (this.total < 2) {
                return;
            }
            this.idx = (this.idx + 1) % this.total;
        },

        prev() {
            if (this.total < 2) {
                return;
            }
            this.idx = (this.idx - 1 + this.total) % this.total;
        },

        go(i) {
            if (i >= 0 && i < this.total) {
                this.idx = i;
            }
        },

        startTouch(e) {
            this.touchStartX = e.touches[0]?.clientX ?? null;
        },

        endTouch(e) {
            if (this.touchStartX == null) {
                return;
            }
            const endX = e.changedTouches[0]?.clientX ?? this.touchStartX;
            const dx = endX - this.touchStartX;
            if (dx < -48) {
                this.next();
            } else if (dx > 48) {
                this.prev();
            }
            this.touchStartX = null;
        },
    }));
});

Alpine.start();
