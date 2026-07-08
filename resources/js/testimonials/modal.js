document.addEventListener('alpine:init', () => {
    Alpine.data('testimonialModal', (config = {}) => ({
        open: false,
        rating: Number(config.initialRating) || 0,
        hoverRating: 0,
        labels: config.labels ?? {},
        submitUrl: config.submitUrl ?? '/destekci-deneyimi',
        kvkkText: config.kvkkText ?? '',
        showKvkk: false,
        submitting: false,

        init() {
            if (config.openOnLoad) {
                this.open = true;
                document.body.classList.add('overflow-hidden');
            }
        },

        get displayRating() {
            return this.hoverRating || this.rating;
        },

        setRating(value) {
            this.rating = value;
        },

        openModal() {
            this.open = true;
            document.body.classList.add('overflow-hidden');
        },

        closeModal() {
            this.open = false;
            this.showKvkk = false;
            document.body.classList.remove('overflow-hidden');
        },

        handleBackdrop(event) {
            if (event.target === event.currentTarget) {
                this.closeModal();
            }
        },

        handleKeydown(event) {
            if (event.key === 'Escape' && this.open) {
                this.closeModal();
            }
        },
    }));
});
