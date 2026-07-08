document.addEventListener('alpine:init', () => {
    Alpine.data('testimonialsCarousel', (config = {}) => ({
        items: Array.isArray(config.items) ? config.items : [],
        current: 0,
        slidesPerView: 1,
        paused: false,
        _timer: null,
        _resizeHandler: null,

        init() {
            this.updateSlidesPerView();
            this._resizeHandler = () => this.updateSlidesPerView();
            window.addEventListener('resize', this._resizeHandler);
            this.startAutoplay();
        },

        destroy() {
            this.stopAutoplay();
            if (this._resizeHandler) {
                window.removeEventListener('resize', this._resizeHandler);
            }
        },

        updateSlidesPerView() {
            const width = window.innerWidth;
            if (width >= 1024) {
                this.slidesPerView = 3;
            } else if (width >= 768) {
                this.slidesPerView = 2;
            } else {
                this.slidesPerView = 1;
            }

            const maxIndex = this.maxIndex;
            if (this.current > maxIndex) {
                this.current = maxIndex;
            }
        },

        get maxIndex() {
            return Math.max(0, this.items.length - this.slidesPerView);
        },

        get canPrev() {
            return this.current > 0;
        },

        get canNext() {
            return this.current < this.maxIndex;
        },

        get trackStyle() {
            const shift = (100 / this.slidesPerView) * this.current;

            return `transform: translateX(-${shift}%);`;
        },

        slideWidthClass() {
            if (this.slidesPerView === 3) {
                return 'w-full md:w-1/2 lg:w-1/3';
            }
            if (this.slidesPerView === 2) {
                return 'w-full md:w-1/2';
            }

            return 'w-full';
        },

        prev() {
            if (this.canPrev) {
                this.current -= 1;
            }
        },

        next() {
            if (this.canNext) {
                this.current += 1;
            } else {
                this.current = 0;
            }
        },

        goTo(index) {
            if (index >= 0 && index <= this.maxIndex) {
                this.current = index;
            }
        },

        startAutoplay() {
            this.stopAutoplay();
            if (this.items.length <= this.slidesPerView) {
                return;
            }

            this._timer = window.setInterval(() => {
                if (!this.paused) {
                    this.next();
                }
            }, 6000);
        },

        stopAutoplay() {
            if (this._timer) {
                window.clearInterval(this._timer);
                this._timer = null;
            }
        },

        pause() {
            this.paused = true;
        },

        resume() {
            this.paused = false;
        },
    }));
});
