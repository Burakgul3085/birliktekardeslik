import { calculateZakat } from './calculator';
import { fetchZakatPrices, formatMoney, resolveLocale } from './prices';

document.addEventListener('alpine:init', () => {
    Alpine.data('zakatCalculator', (config = {}) => ({
        locale: config.locale ?? 'tr',
        labels: config.labels ?? {},
        settings: config.settings ?? {},
        donateUrl: config.donateUrl ?? '/bagis-yap',
        activitiesUrl: config.activitiesUrl ?? '/faaliyetler',
        pricesLoading: true,
        pricesError: false,
        prices: {},
        sources: {},
        form: {
            gold24: '',
            gold22: '',
            gold18: '',
            gold14: '',
            silver: '',
            cash: '',
            bank: '',
            usd: '',
            eur: '',
            gbp: '',
            trade: '',
            receivables: '',
            debts: '',
            hawl: false,
        },
        _refreshTimer: null,

        init() {
            this.restoreForm();
            this.loadPrices();
            this._refreshTimer = window.setInterval(() => this.loadPrices(true), 60_000);
        },

        destroy() {
            if (this._refreshTimer) {
                window.clearInterval(this._refreshTimer);
            }
        },

        async loadPrices(silent = false) {
            if (!silent) {
                this.pricesLoading = true;
            }

            try {
                const payload = await fetchZakatPrices();
                this.prices = payload;
                this.sources = payload.sources ?? {};
                this.pricesError = !payload.has_data;
            } catch {
                this.pricesError = true;
            } finally {
                this.pricesLoading = false;
            }
        },

        get result() {
            return calculateZakat(this.form, this.prices, this.settings);
        },

        money(value) {
            return formatMoney(value, resolveLocale(this.locale));
        },

        get donateLink() {
            const amount = Math.round(this.result.zakatAmount);
            if (amount <= 0) {
                return this.donateUrl;
            }

            const params = new URLSearchParams({
                tutar: String(amount),
                aciklama: 'Zekat',
            });

            return `${this.donateUrl}?${params.toString()}`;
        },

        resetForm() {
            Object.keys(this.form).forEach((key) => {
                if (key === 'hawl') {
                    this.form.hawl = false;
                } else {
                    this.form[key] = '';
                }
            });

            try {
                localStorage.removeItem('bkd_zakat_form');
            } catch {
                // ignore
            }
        },

        saveForm() {
            try {
                localStorage.setItem('bkd_zakat_form', JSON.stringify(this.form));
            } catch {
                // ignore
            }
        },

        restoreForm() {
            try {
                const raw = localStorage.getItem('bkd_zakat_form');
                if (!raw) {
                    return;
                }

                const saved = JSON.parse(raw);
                if (typeof saved === 'object' && saved !== null) {
                    this.form = { ...this.form, ...saved };
                }
            } catch {
                // ignore
            }
        },

        formatFetchedAt(isoString) {
            if (!isoString) {
                return '—';
            }

            try {
                return new Intl.DateTimeFormat(resolveLocale(this.locale), {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                }).format(new Date(isoString));
            } catch {
                return '—';
            }
        },
    }));
});
