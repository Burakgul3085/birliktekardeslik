const API_URL = '/api/zekat/fiyatlar';
const GENELPARA_URL = 'https://api.genelpara.com/json';
const CACHE_KEY = 'bkd_zakat_prices';
const CACHE_TTL = 60_000;

export async function fetchZakatPrices() {
    try {
        const cached = readCache();
        if (cached?.has_data) {
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

    let payload = await response.json();
    payload = await ensurePrices(payload);
    writeCache(payload);

    return payload;
}

async function ensurePrices(payload) {
    let result = { ...payload };

    if (!result.has_metals) {
        const clientMetals = await fetchGenelParaMetalsFromClient();
        if (clientMetals) {
            result = mergeMetalsIntoPayload(result, clientMetals);
        }
    }

    const needsForex = !result.has_forex
        || !result.chf_try
        || !result.sar_try
        || !result.aed_try;

    if (needsForex) {
        const clientForex = await fetchGenelParaForexFromClient();
        if (clientForex) {
            result = mergeForexIntoPayload(result, clientForex);
        }
    }

    return result;
}

async function fetchGenelParaMetalsFromClient() {
    const symbolSets = ['GA,GAG,22,18,14,C,Y,T,ATA,CMR', 'all'];

    for (const symbols of symbolSets) {
        try {
            const url = `${GENELPARA_URL}?${new URLSearchParams({
                list: 'altin',
                sembol: symbols,
            }).toString()}`;

            const response = await fetch(url, {
                headers: { Accept: 'application/json' },
                mode: 'cors',
            });

            if (!response.ok) {
                continue;
            }

            const payload = await response.json();

            if (!payload?.success || !payload?.data) {
                continue;
            }

            const parsed = parseMetalsFromGenelPara(payload.data);

            if (parsed) {
                return parsed;
            }
        } catch {
            // try next symbol set
        }
    }

    return null;
}

async function fetchGenelParaForexFromClient() {
    const primary = await requestForexFromClient(['USD', 'EUR', 'GBP']);
    const supplemental = await requestForexFromClient(['CHF', 'SAR', 'AED']);

    if (!primary && !supplemental) {
        return null;
    }

    return {
        rates: { ...(primary?.rates ?? {}), ...(supplemental?.rates ?? {}) },
        trends: { ...(primary?.trends ?? {}), ...(supplemental?.trends ?? {}) },
    };
}

async function requestForexFromClient(codes) {
    try {
        const url = `${GENELPARA_URL}?${new URLSearchParams({
            list: 'doviz',
            sembol: codes.join(','),
        }).toString()}`;

        const response = await fetch(url, {
            headers: { Accept: 'application/json' },
            mode: 'cors',
        });

        if (!response.ok) {
            return null;
        }

        const payload = await response.json();

        if (!payload?.success || !payload?.data) {
            return null;
        }

        return parseForexFromGenelPara(payload.data, codes);
    } catch {
        return null;
    }
}

function parseMetalsFromGenelPara(data) {
    const gold24Row = parseGenelParaRow(data.GA);
    const silverRow = parseGenelParaRow(data.GAG);

    if (!gold24Row || !silverRow) {
        return null;
    }

    const prices = {
        gold_24_per_gram: gold24Row.price,
        gold_22_per_gram: parseGenelParaPrice(data['22']) || roundPrice(gold24Row.price * 0.916),
        gold_18_per_gram: parseGenelParaPrice(data['18']) || roundPrice(gold24Row.price * 0.75),
        gold_14_per_gram: parseGenelParaPrice(data['14']) || roundPrice(gold24Row.price * 0.585),
        silver_per_gram: silverRow.price,
        coin_quarter_try: parseGenelParaPrice(data.C),
        coin_half_try: parseGenelParaPrice(data.Y),
        coin_full_try: parseGenelParaPrice(data.T),
        coin_ata_try: parseGenelParaPrice(data.ATA),
        coin_cmr_try: parseGenelParaPrice(data.CMR),
    };

    const trends = {
        gold_24: gold24Row.trend,
        silver: silverRow.trend,
        gold_22: parseGenelParaRow(data['22'])?.trend ?? flatTrend(),
        gold_18: parseGenelParaRow(data['18'])?.trend ?? flatTrend(),
        gold_14: parseGenelParaRow(data['14'])?.trend ?? flatTrend(),
        coin_quarter: parseGenelParaRow(data.C)?.trend ?? flatTrend(),
        coin_half: parseGenelParaRow(data.Y)?.trend ?? flatTrend(),
        coin_full: parseGenelParaRow(data.T)?.trend ?? flatTrend(),
        coin_ata: parseGenelParaRow(data.ATA)?.trend ?? flatTrend(),
        coin_cmr: parseGenelParaRow(data.CMR)?.trend ?? flatTrend(),
    };

    return { prices, trends };
}

function parseForexFromGenelPara(data, codes = ['USD', 'EUR', 'GBP', 'CHF', 'SAR', 'AED']) {
    const rates = {};
    const trends = {};

    for (const code of codes) {
        const row = parseGenelParaRow(data[code]);
        if (row) {
            rates[code] = row.price;
            trends[code.toLowerCase()] = row.trend;
        }
    }

    if (Object.keys(rates).length === 0) {
        return null;
    }

    if (codes.includes('USD') && (!rates.USD || !rates.EUR || !rates.GBP)) {
        return null;
    }

    return { rates, trends };
}

function parseGenelParaRow(row) {
    const price = parseGenelParaPrice(row);

    if (!price) {
        return null;
    }

    return {
        price,
        trend: parseGenelParaTrend(row),
    };
}

function parseGenelParaTrend(row) {
    if (!row || typeof row !== 'object') {
        return flatTrend();
    }

    const change = row.degisim != null
        ? Number(String(row.degisim).replace(',', '.'))
        : 0;

    const rate = row.oran != null
        ? Number(String(row.oran).replace(',', '.'))
        : 0;

    const yon = String(row.yon ?? 'moneyNat');

    let direction = 'flat';
    if (yon === 'moneyUp') {
        direction = 'up';
    } else if (yon === 'moneyDown') {
        direction = 'down';
    }

    return {
        change: Number.isFinite(change) ? roundPrice(change) : 0,
        rate: Number.isFinite(rate) ? roundPrice(rate) : 0,
        direction,
    };
}

function flatTrend() {
    return { change: 0, rate: 0, direction: 'flat' };
}

function parseGenelParaPrice(row) {
    if (!row || typeof row !== 'object') {
        return 0;
    }

    const satis = row.satis ?? row.alis ?? null;

    if (satis === null) {
        return 0;
    }

    const value = Number(String(satis).replace(',', '.'));

    return Number.isFinite(value) && value > 0 ? roundPrice(value) : 0;
}

function roundPrice(value) {
    return Math.round(value * 100) / 100;
}

function mergeMetalsIntoPayload(payload, { prices, trends }) {
    const nisapGrams = Number(payload.nisap_grams) || 80;
    const fetchedAt = new Date().toISOString();

    return {
        ...payload,
        ...prices,
        nisap_threshold_try: prices.gold_24_per_gram > 0
            ? roundPrice(nisapGrams * prices.gold_24_per_gram)
            : 0,
        has_metals: true,
        has_data: Boolean(payload.has_forex) && prices.gold_24_per_gram > 0 && prices.silver_per_gram > 0,
        metals_via_client: true,
        trends: { ...(payload.trends ?? {}), ...trends },
        sources: {
            ...(payload.sources ?? {}),
            genelpara: {
                ...(payload.sources?.genelpara ?? {}),
                name: 'GenelPara',
                label: 'GenelPara (piyasa verisi, resmi kur değildir)',
                url: 'https://www.genelpara.com',
                metals_fetched_at: fetchedAt,
                via_client: true,
            },
        },
    };
}

function mergeForexIntoPayload(payload, { rates, trends }) {
    const fetchedAt = new Date().toISOString();

    return {
        ...payload,
        usd_try: rates.USD ?? payload.usd_try ?? 0,
        eur_try: rates.EUR ?? payload.eur_try ?? 0,
        gbp_try: rates.GBP ?? payload.gbp_try ?? 0,
        chf_try: rates.CHF ?? payload.chf_try ?? 0,
        sar_try: rates.SAR ?? payload.sar_try ?? 0,
        aed_try: rates.AED ?? payload.aed_try ?? 0,
        has_forex: Boolean(rates.USD && rates.EUR && rates.GBP),
        has_data: Boolean(payload.has_metals) && Boolean(rates.USD && rates.EUR && rates.GBP),
        forex_via_client: true,
        trends: { ...(payload.trends ?? {}), ...trends },
        sources: {
            ...(payload.sources ?? {}),
            genelpara: {
                ...(payload.sources?.genelpara ?? {}),
                name: 'GenelPara',
                label: 'GenelPara (piyasa verisi, resmi kur değildir)',
                url: 'https://www.genelpara.com',
                forex_fetched_at: fetchedAt,
                via_client: true,
            },
        },
    };
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

export function formatPercent(value, locale = 'tr-TR') {
    const amount = Number(value) || 0;
    const sign = amount > 0 ? '+' : '';

    return `${sign}${new Intl.NumberFormat(locale, {
        maximumFractionDigits: 2,
        minimumFractionDigits: 2,
    }).format(amount)}%`;
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
