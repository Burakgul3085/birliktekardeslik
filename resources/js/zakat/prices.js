const API_URL = '/api/zekat/fiyatlar';
const CACHE_KEY = 'bkd_zakat_prices';
const CACHE_TTL = 60_000;

export async function fetchZakatPrices() {
    try {
        const cached = readCache();
        if (cached) {
            return cached;
        }
    } catch {
        // ignore cache read errors
    }

    const response = await fetch(API_URL, {
        headers: { Accept: 'application/json' },
    });

    if (!response.ok) {
        throw new Error(`Fiyat API hatası: ${response.status}`);
    }

    const payload = await response.json();
    writeCache(payload);

    return payload;
}

function readCache() {
    const raw = localStorage.getItem(CACHE_KEY);
    if (!raw) {
        return null;
    }

    const parsed = JSON.parse(raw);
    if (!parsed?.value || !parsed?.fetchedAt) {
        return null;
    }

    if (Date.now() - parsed.fetchedAt > CACHE_TTL) {
        return null;
    }

    return parsed.value;
}

function writeCache(value) {
    try {
        localStorage.setItem(CACHE_KEY, JSON.stringify({
            value,
            fetchedAt: Date.now(),
        }));
    } catch {
        // ignore
    }
}

export function formatMoney(value, locale = 'tr-TR') {
    const amount = Number(value) || 0;

    return new Intl.NumberFormat(locale, {
        style: 'currency',
        currency: 'TRY',
        maximumFractionDigits: 2,
    }).format(amount);
}

export function formatNumber(value, locale = 'tr-TR') {
    const amount = Number(value) || 0;

    return new Intl.NumberFormat(locale, {
        maximumFractionDigits: 2,
    }).format(amount);
}

export function parseAmount(value) {
    const normalized = String(value ?? '').replace(/\./g, '').replace(',', '.').trim();
    const parsed = Number(normalized);

    return Number.isFinite(parsed) && parsed > 0 ? parsed : 0;
}

export function resolveLocale(locale) {
    const map = {
        tr: 'tr-TR',
        en: 'en-GB',
        ar: 'ar-TD',
        ru: 'ru-RU',
    };

    return map[locale] ?? 'tr-TR';
}
