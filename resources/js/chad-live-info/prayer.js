import { DEFAULT_TIMEZONE, getMinutesSinceMidnight, parsePrayerTime } from './time';

const DEFAULT_LOCATION = {
    latitude: 12.1067,
    longitude: 15.0444,
    timezone: DEFAULT_TIMEZONE,
    prayerMethod: 4,
};

const PRAYER_KEYS = ['Fajr', 'Dhuhr', 'Asr', 'Maghrib', 'Isha'];

function buildUrl({ latitude, longitude, prayerMethod }) {
    const params = new URLSearchParams({
        latitude: String(latitude),
        longitude: String(longitude),
        method: String(prayerMethod),
    });

    return `https://api.aladhan.com/v1/timings?${params.toString()}`;
}

export async function fetchAladhanData(location = {}) {
    const target = { ...DEFAULT_LOCATION, ...location };

    const response = await fetch(buildUrl(target), {
        method: 'GET',
        headers: { Accept: 'application/json' },
    });

    if (!response.ok) {
        throw new Error(`Aladhan responded with ${response.status}`);
    }

    const payload = await response.json();
    const data = payload?.data;

    if (!data?.timings || !data?.date?.hijri) {
        throw new Error('Aladhan returned invalid payload');
    }

    return {
        hijri: formatHijri(data.date.hijri),
        hijriRaw: data.date.hijri,
        nextPrayer: resolveNextPrayer(data.timings, target.timezone),
    };
}

function formatHijri(hijri) {
    const day = hijri?.day ?? '';
    const monthNumber = Number(hijri?.month?.number ?? 0);
    const year = hijri?.year ?? '';

    return {
        day,
        monthNumber,
        year,
        display: `${day} ${hijri?.month?.en ?? ''} ${year}`.trim(),
    };
}

export function localizeHijri(hijriRaw, hijriMonths = {}) {
    if (!hijriRaw) {
        return '--';
    }

    const monthName = hijriMonths[String(hijriRaw.monthNumber)] ?? hijriRaw.display;

    return `${hijriRaw.day} ${monthName} ${hijriRaw.year}`.trim();
}

function resolveNextPrayer(timings, timezone = DEFAULT_TIMEZONE) {
    const now = getMinutesSinceMidnight(timezone);

    for (const key of PRAYER_KEYS) {
        const time = timings[key];
        if (!time) {
            continue;
        }

        const minutes = parsePrayerTime(time);
        if (minutes > now) {
            return {
                key,
                time: time.slice(0, 5),
            };
        }
    }

    const fajr = timings.Fajr?.slice(0, 5) ?? '--';

    return {
        key: 'Fajr',
        time: fajr,
        isTomorrow: true,
    };
}

export function localizePrayer(prayer, prayerNames = {}) {
    if (!prayer?.key) {
        return { name: '--', time: '--' };
    }

    return {
        name: prayerNames[prayer.key] ?? prayer.key,
        time: prayer.time ?? '--',
    };
}
