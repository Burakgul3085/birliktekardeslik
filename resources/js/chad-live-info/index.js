import { getCachedValue, isExpired, setCache } from './cache';
import { fetchAladhanData, localizeHijri, localizePrayer } from './prayer';
import { DEFAULT_TIMEZONE, formatLocalTime } from './time';
import { fetchWeather } from './weather';

const TTL = {
    weather: 30 * 60 * 1000,
    hijri: 24 * 60 * 60 * 1000,
    prayer: 12 * 60 * 60 * 1000,
};

/* Konum bilgisi verilmezse Çad varsayılan olarak kullanılır */
const DEFAULT_LOCATION = {
    cacheKey: 'chad',
    latitude: 12.1067,
    longitude: 15.0444,
    timezone: DEFAULT_TIMEZONE,
    prayerMethod: 4,
};

document.addEventListener('alpine:init', () => {
    Alpine.data('chadLiveInfo', (config = {}) => ({
        loading: true,
        ready: false,
        locale: config.locale ?? 'tr',
        labels: config.labels ?? {},
        prayerNames: config.prayerNames ?? {},
        hijriMonths: config.hijriMonths ?? {},
        donateUrl: config.donateUrl ?? '/bagis-yap',
        location: { ...DEFAULT_LOCATION, ...(config.location ?? {}) },
        weather: null,
        weatherError: false,
        localTime: '--',
        hijri: '--',
        prayerName: '--',
        prayerTime: '--',
        _clockTimer: null,
        _refreshTimer: null,

        init() {
            this.bootstrap();
            this._clockTimer = window.setInterval(() => this.tickClock(), 60_000);
            this._refreshTimer = window.setInterval(() => this.refreshStaleData(), 60_000);
        },

        destroy() {
            if (this._clockTimer) {
                window.clearInterval(this._clockTimer);
            }
            if (this._refreshTimer) {
                window.clearInterval(this._refreshTimer);
            }
        },

        get cacheKey() {
            return this.location.cacheKey;
        },

        async bootstrap() {
            this.loading = true;
            this.applyWeatherFromCache();
            this.applyHijriFromCache();
            this.applyPrayerFromCache();
            this.tickClock();

            await Promise.allSettled([
                this.loadWeather(),
                this.loadAladhan(),
            ]);

            this.loading = false;
            this.ready = true;
        },

        async refreshStaleData() {
            this.tickClock();

            const tasks = [];
            if (isExpired(this.cacheKey, 'weather', TTL.weather)) {
                tasks.push(this.loadWeather());
            }
            if (isExpired(this.cacheKey, 'hijri', TTL.hijri) || isExpired(this.cacheKey, 'prayer', TTL.prayer)) {
                tasks.push(this.loadAladhan());
            }

            if (tasks.length) {
                await Promise.allSettled(tasks);
            }
        },

        tickClock() {
            this.localTime = formatLocalTime(this.locale, this.location.timezone);
        },

        applyWeatherFromCache() {
            const cached = getCachedValue(this.cacheKey, 'weather');
            if (cached?.temperature != null) {
                this.weather = `${cached.temperature}°C`;
                this.weatherError = false;
            }
        },

        applyHijriFromCache() {
            const cached = getCachedValue(this.cacheKey, 'hijri');
            if (cached) {
                this.hijri = localizeHijri(cached, this.hijriMonths);
            }
        },

        applyPrayerFromCache() {
            const cached = getCachedValue(this.cacheKey, 'prayer');
            if (cached) {
                const localized = localizePrayer(cached, this.prayerNames);
                this.prayerName = localized.name;
                this.prayerTime = localized.time;
            }
        },

        async loadWeather() {
            if (!isExpired(this.cacheKey, 'weather', TTL.weather)) {
                this.applyWeatherFromCache();
                return;
            }

            try {
                const data = await fetchWeather(this.location);
                setCache(this.cacheKey, 'weather', data);
                this.weather = `${data.temperature}°C`;
                this.weatherError = false;
            } catch {
                const cached = getCachedValue(this.cacheKey, 'weather');
                if (cached?.temperature != null) {
                    this.weather = `${cached.temperature}°C`;
                    this.weatherError = false;
                } else {
                    this.weather = null;
                    this.weatherError = true;
                }
            }
        },

        async loadAladhan() {
            const hijriFresh = !isExpired(this.cacheKey, 'hijri', TTL.hijri);
            const prayerFresh = !isExpired(this.cacheKey, 'prayer', TTL.prayer);

            if (hijriFresh && prayerFresh) {
                this.applyHijriFromCache();
                this.applyPrayerFromCache();
                return;
            }

            try {
                const data = await fetchAladhanData(this.location);

                if (!hijriFresh || !getCachedValue(this.cacheKey, 'hijri')) {
                    setCache(this.cacheKey, 'hijri', data.hijri);
                    this.hijri = localizeHijri(data.hijri, this.hijriMonths);
                }

                if (!prayerFresh || !getCachedValue(this.cacheKey, 'prayer')) {
                    setCache(this.cacheKey, 'prayer', data.nextPrayer);
                    const localized = localizePrayer(data.nextPrayer, this.prayerNames);
                    this.prayerName = localized.name;
                    this.prayerTime = localized.time;
                }
            } catch {
                this.applyHijriFromCache();
                this.applyPrayerFromCache();

                if (!getCachedValue(this.cacheKey, 'hijri')) {
                    this.hijri = '--';
                }
                if (!getCachedValue(this.cacheKey, 'prayer')) {
                    this.prayerName = '--';
                    this.prayerTime = '--';
                }
            }
        },

        get weatherDisplay() {
            if (this.weatherError) {
                return this.labels.weather_error ?? 'Veri Alınamadı';
            }

            return this.weather ?? '--';
        },
    }));
});
