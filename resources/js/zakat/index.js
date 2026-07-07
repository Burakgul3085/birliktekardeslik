import { calculateZakat } from './calculator';
import { fetchZakatPrices, formatMoney, formatNumber, formatPercent, resolveLocale } from './prices';

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
        trends: {},
        sources: {},
        priceFlashes: {},
        _prevPrices: {},
        _refreshTimer: null,
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
                this.detectPriceFlashes(payload);
                this.prices = payload;
                this.trends = payload.trends ?? {};
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

        detectPriceFlashes(payload) {
            const keys = [
                'gold_24_per_gram', 'silver_per_gram',
                'usd_try', 'eur_try', 'gbp_try', 'chf_try', 'sar_try', 'aed_try',
                'coin_quarter_try', 'coin_half_try', 'coin_full_try',
            ];

            keys.forEach((key) => {
                const prev = this._prevPrices[key];
                const next = payload[key];
                if (prev != null && next != null && prev !== next) {
                    this.priceFlashes[key] = next > prev ? 'up' : 'down';
                    window.setTimeout(() => {
                        delete this.priceFlashes[key];
                    }, 600);
                }
            });

            this._prevPrices = { ...this._prevPrices, ...payload };
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
            return this.result.zakatAmount > 0;
        },

        get nisapProgress() {
            const threshold = this.result.nisapThreshold || this.prices.nisap_threshold_try || 0;
            if (threshold <= 0) {
                return 0;
            }

            return Math.min(100, Math.round((this.result.netWealth / threshold) * 100));
        },

        get forexRows() {
            return [
                { code: 'USD', key: 'usd_try', trendKey: 'usd', flag: 'us' },
                { code: 'EUR', key: 'eur_try', trendKey: 'eur', flag: 'eu' },
                { code: 'GBP', key: 'gbp_try', trendKey: 'gbp', flag: 'gb' },
                { code: 'CHF', key: 'chf_try', trendKey: 'chf', flag: 'ch' },
                { code: 'SAR', key: 'sar_try', trendKey: 'sar', flag: 'sa' },
                { code: 'AED', key: 'aed_try', trendKey: 'aed', flag: 'ae' },
            ];
        },

        get coinRows() {
            return [
                { label: this.labels.coin_quarter, key: 'coin_quarter_try', trendKey: 'coin_quarter' },
                { label: this.labels.coin_half, key: 'coin_half_try', trendKey: 'coin_half' },
                { label: this.labels.coin_full, key: 'coin_full_try', trendKey: 'coin_full' },
            ];
        },

        money(value) {
            return formatMoney(value, resolveLocale(this.locale));
        },

        number(value) {
            return formatNumber(value, resolveLocale(this.locale));
        },

        percent(value) {
            return formatPercent(value, resolveLocale(this.locale));
        },

        trend(key) {
            return this.trends[key] ?? { change: 0, rate: 0, direction: 'flat' };
        },

        trendClass(key) {
            const dir = this.trend(key).direction;
            if (dir === 'up') {
                return 'text-emerald-600';
            }
            if (dir === 'down') {
                return 'text-rose-600';
            }

            return 'text-slate-400';
        },

        flashClass(priceKey) {
            const flash = this.priceFlashes[priceKey];
            if (flash === 'up') {
                return 'zakat-flash-up';
            }
            if (flash === 'down') {
                return 'zakat-flash-down';
            }

            return '';
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
