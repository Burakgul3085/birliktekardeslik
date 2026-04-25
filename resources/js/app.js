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

const enablePageTransition = () => {
    const transitionEl = document.getElementById('page-transition');
    if (!transitionEl) {
        return;
    }

    const showTransition = () => {
        document.body.classList.add('page-transition-active');
    };

    const hideTransition = () => {
        document.body.classList.remove('page-transition-active');
    };

    window.addEventListener('pageshow', hideTransition);

    document.addEventListener('click', (event) => {
        const link = event.target.closest('a');
        if (!link) {
            return;
        }

        if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
            return;
        }

        const href = link.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:')) {
            return;
        }

        if (link.target === '_blank' || link.hasAttribute('download')) {
            return;
        }

        const url = new URL(link.href, window.location.origin);
        if (url.origin !== window.location.origin || (url.pathname === window.location.pathname && url.search === window.location.search)) {
            return;
        }

        event.preventDefault();
        showTransition();
        window.setTimeout(() => {
            window.location.assign(url.href);
        }, 180);
    });

    window.addEventListener('beforeunload', showTransition);
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', enablePageTransition);
} else {
    enablePageTransition();
}
