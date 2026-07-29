import { DEFAULT_TIMEZONE } from './time';

const DEFAULT_LOCATION = {
    latitude: 12.1067,
    longitude: 15.0444,
    timezone: DEFAULT_TIMEZONE,
};

function buildUrl({ latitude, longitude, timezone }) {
    const params = new URLSearchParams({
        latitude: String(latitude),
        longitude: String(longitude),
        current: 'temperature_2m',
        timezone,
    });

    return `https://api.open-meteo.com/v1/forecast?${params.toString()}`;
}

export async function fetchWeather(location = {}) {
    const target = { ...DEFAULT_LOCATION, ...location };

    const response = await fetch(buildUrl(target), {
        method: 'GET',
        headers: { Accept: 'application/json' },
    });

    if (!response.ok) {
        throw new Error(`Open-Meteo responded with ${response.status}`);
    }

    const payload = await response.json();
    const temperature = payload?.current?.temperature_2m;

    if (temperature == null || Number.isNaN(Number(temperature))) {
        throw new Error('Open-Meteo returned invalid temperature');
    }

    return {
        temperature: Math.round(Number(temperature)),
    };
}
