const PREFIX = 'bkd_chad_';

export function getCacheEntry(key) {
    try {
        const raw = localStorage.getItem(PREFIX + key);
        if (!raw) {
            return null;
        }

        return JSON.parse(raw);
    } catch {
        return null;
    }
}

export function getCachedValue(key) {
    return getCacheEntry(key)?.value ?? null;
}

export function setCache(key, value) {
    try {
        localStorage.setItem(PREFIX + key, JSON.stringify({
            value,
            fetchedAt: Date.now(),
        }));
    } catch {
        // localStorage unavailable — silently ignore
    }
}

export function isExpired(key, ttlMs) {
    const entry = getCacheEntry(key);
    if (!entry?.fetchedAt) {
        return true;
    }

    return Date.now() - entry.fetchedAt > ttlMs;
}
