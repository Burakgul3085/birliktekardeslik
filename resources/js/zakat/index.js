import { calculateZakat } from './calculator';
import { fetchZakatPrices, formatMoney, resolveLocale } from './prices';

document.addEventListener('alpine:init', () => {
    Alpine.data('zakatCalculator', (config = {}) => ({
        locale: config.locale ?? 'tr',
        labels: config.labels ?? {},
        settings: config.settings ?? {},
        faqItems: config.faqItems ?? [],
        donateUrl: config.donateUrl ?? '/bagis-yap',
        activitiesUrl: config.activitiesUrl ?? '/faaliyetler',
        pricesLoading: true,
        metalsError: false,
        forexError: false,
        faqOpen: null,
        prices: {},
        sources: {},
        form: {
            gold24: '',
            gold22: '',
            gold18: '',
            gold14: '',
            silver: '',
            coinQuarter: '',
            coinHalf: '',
            coinFull: '',
            coinAta: '',
            coinCmr: '',
            cash: '',
            bank: '',
            usd: '',
            eur: '',
            gbp: '',
            chf: '',
            sar: '',
            aed: '',
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
                this.metalsError = !payload.has_metals;
                this.forexError = !payload.has_forex;
            } catch {
                this.metalsError = true;
                this.forexError = true;
            } finally {
                this.pricesLoading = false;
            }
        },

        get result() {
            return calculateZakat(this.form, this.prices, this.settings);
        },

        get breakdownRows() {
            const map = [
                ['gold24', this.labels.breakdown_gold24],
                ['gold22', this.labels.breakdown_gold22],
                ['gold18', this.labels.breakdown_gold18],
                ['gold14', this.labels.breakdown_gold14],
                ['silver', this.labels.breakdown_silver],
                ['coinQuarter', this.labels.breakdown_coin_quarter],
                ['coinHalf', this.labels.breakdown_coin_half],
                ['coinFull', this.labels.breakdown_coin_full],
                ['coinAta', this.labels.breakdown_coin_ata],
                ['coinCmr', this.labels.breakdown_coin_cmr],
                ['cash', this.labels.breakdown_cash],
                ['bank', this.labels.breakdown_bank],
                ['usd', this.labels.breakdown_usd],
                ['eur', this.labels.breakdown_eur],
                ['gbp', this.labels.breakdown_gbp],
                ['chf', this.labels.breakdown_chf],
                ['sar', this.labels.breakdown_sar],
                ['aed', this.labels.breakdown_aed],
                ['trade', this.labels.breakdown_trade],
                ['receivables', this.labels.breakdown_receivables],
            ];

            return map
                .map(([key, label]) => ({
                    key,
                    label,
                    value: this.result.breakdown[key] ?? 0,
                }))
                .filter((row) => row.value > 0);
        },

        get showStickySummary() {
            return this.result.totalAssets > 0 || this.result.zakatAmount > 0;
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

        toggleFaq(index) {
            this.faqOpen = this.faqOpen === index ? null : index;
        },

        printSummary() {
            window.print();
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
