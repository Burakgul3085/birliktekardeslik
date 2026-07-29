export const DEFAULT_TIMEZONE = 'Africa/Ndjamena';

const localeMap = {
    tr: 'tr-TR',
    en: 'en-GB',
    ar: 'ar-TD',
    ru: 'ru-RU',
};

export function resolveLocale(locale) {
    return localeMap[locale] ?? 'tr-TR';
}

export function formatLocalTime(locale = 'tr', timezone = DEFAULT_TIMEZONE) {
    return new Intl.DateTimeFormat(resolveLocale(locale), {
        timeZone: timezone,
        hour: '2-digit',
        minute: '2-digit',
        hour12: false,
    }).format(new Date());
}

/* Verilen saat diliminde gün başından bu yana geçen dakika sayısı */
export function getMinutesSinceMidnight(timezone = DEFAULT_TIMEZONE) {
    const parts = new Intl.DateTimeFormat('en-GB', {
        timeZone: timezone,
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
