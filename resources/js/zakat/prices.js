const API_URL = '/api/zekat/fiyatlar';
const GENELPARA_URL = 'https://api.genelpara.com/json';
const CACHE_KEY = 'bkd_zakat_prices';
const CACHE_TTL = 60_000;

export async function fetchZakatPrices() {
    try {
        const cached = readCache();
        if (cached?.has_metals) {
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
    payload = await ensureMetals(payload);
    writeCache(payload);

    return payload;
}

async function ensureMetals(payload) {
    if (payload?.has_metals) {
        return payload;
    }

    const clientMetals = await fetchGenelParaMetalsFromClient();

    if (!clientMetals) {
        return payload;
    }

    return mergeMetalsIntoPayload(payload, clientMetals);
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

            const metals = parseMetalsFromGenelPara(payload.data);

            if (metals) {
                return metals;
            }
        } catch {
            // try next symbol set
        }
    }

    return null;
}

function parseMetalsFromGenelPara(data) {
    const gold24 = parseGenelParaPrice(data.GA);
    const silver = parseGenelParaPrice(data.GAG);

    if (!gold24 || !silver) {
        return null;
    }

    return {
        gold_24_per_gram: gold24,
        gold_22_per_gram: parseGenelParaPrice(data['22']) || roundPrice(gold24 * 0.916),
        gold_18_per_gram: parseGenelParaPrice(data['18']) || roundPrice(gold24 * 0.75),
        gold_14_per_gram: parseGenelParaPrice(data['14']) || roundPrice(gold24 * 0.585),
        silver_per_gram: silver,
        coin_quarter_try: parseGenelParaPrice(data.C),
        coin_half_try: parseGenelParaPrice(data.Y),
        coin_full_try: parseGenelParaPrice(data.T),
        coin_ata_try: parseGenelParaPrice(data.ATA),
        coin_cmr_try: parseGenelParaPrice(data.CMR),
    };
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

function mergeMetalsIntoPayload(payload, metals) {
    const nisapGrams = Number(payload.nisap_grams) || 80;
    const fetchedAt = new Date().toISOString();

    return {
        ...payload,
        ...metals,
        nisap_threshold_try: metals.gold_24_per_gram > 0
            ? roundPrice(nisapGrams * metals.gold_24_per_gram)
            : 0,
        has_metals: true,
        has_data: Boolean(payload.has_forex) && metals.gold_24_per_gram > 0 && metals.silver_per_gram > 0,
        metals_via_client: true,
        sources: {
            ...(payload.sources ?? {}),
            metals: {
                ...(payload.sources?.metals ?? {}),
                name: 'GenelPara',
                label: 'GenelPara (piyasa verisi)',
                url: 'https://www.genelpara.com',
                fetched_at: fetchedAt,
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
