const PREFIX = 'bkd_live_';

/* Her ülke kendi ad alanında saklanır, böylece veriler birbirine karışmaz */
function buildKey(namespace, key) {
    return `${PREFIX}${namespace}_${key}`;
}

export function getCacheEntry(namespace, key) {
    try {
        const raw = localStorage.getItem(buildKey(namespace, key));
        if (!raw) {
            return null;
        }

        return JSON.parse(raw);
    } catch {
        return null;
    }
}

export function getCachedValue(namespace, key) {
    return getCacheEntry(namespace, key)?.value ?? null;
}

export function setCache(namespace, key, value) {
    try {
        localStorage.setItem(buildKey(namespace, key), JSON.stringify({
            value,
            fetchedAt: Date.now(),
        }));
    } catch {
        // localStorage unavailable — silently ignore
    }
}

export function isExpired(namespace, key, ttlMs) {
    const entry = getCacheEntry(namespace, key);
    if (!entry?.fetchedAt) {
        return true;
    }

    return Date.now() - entry.fetchedAt > ttlMs;
}
