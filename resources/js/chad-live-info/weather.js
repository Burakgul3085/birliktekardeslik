const OPEN_METEO_URL = 'https://api.open-meteo.com/v1/forecast?latitude=12.1067&longitude=15.0444&current=temperature_2m&timezone=Africa%2FNdjamena';

export async function fetchWeather() {
    const response = await fetch(OPEN_METEO_URL, {
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
