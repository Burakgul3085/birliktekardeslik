const TIMEZONE = 'Africa/Ndjamena';

const localeMap = {
    tr: 'tr-TR',
    en: 'en-GB',
    ar: 'ar-TD',
    ru: 'ru-RU',
};

export function resolveLocale(locale) {
    return localeMap[locale] ?? 'tr-TR';
}

export function formatLocalTime(locale = 'tr') {
    return new Intl.DateTimeFormat(resolveLocale(locale), {
        timeZone: TIMEZONE,
        hour: '2-digit',
        minute: '2-digit',
        hour12: false,
    }).format(new Date());
}

export function getChadMinutesSinceMidnight() {
    const parts = new Intl.DateTimeFormat('en-GB', {
        timeZone: TIMEZONE,
        hour: '2-digit',
        minute: '2-digit',
        hour12: false,
    }).formatToParts(new Date());

    const hour = Number(parts.find((part) => part.type === 'hour')?.value ?? 0);
    const minute = Number(parts.find((part) => part.type === 'minute')?.value ?? 0);

    return hour * 60 + minute;
}

export function parsePrayerTime(time) {
    const [hour, minute] = String(time).split(':').map(Number);

    return (hour * 60) + (minute || 0);
}
