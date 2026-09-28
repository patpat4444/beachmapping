import { calculateHeatIndex, type HeatIndexResult } from './heatIndex';

export interface WeatherData {
  temperature: number;
  relativeHumidity: number;
  windSpeed: number;
  windDirection: number;
  weatherCode: number;
  conditionLabel: string;
  heatIndex: HeatIndexResult | null;
  timestamp: string;
  hourly?: HourlyForecast[];
}

export interface HourlyForecast {
  time: string;
  temperature: number;
  weatherCode: number;
}

/**
 * Maps WMO Weather interpretation codes to human-readable labels.
 */
function mapWeatherCode(code: number): string {
  if (code === 0) return 'Clear Sky';
  if (code === 1) return 'Mainly Clear';
  if (code === 2) return 'Partly Cloudy';
  if (code === 3) return 'Overcast';
  if (code >= 45 && code <= 48) return 'Foggy';
  if (code >= 51 && code <= 55) return 'Drizzle';
  if (code >= 61 && code <= 65) return 'Rain Showers';
  if (code >= 80 && code <= 82) return 'Heavy Rain Showers';
  if (code >= 95 && code <= 99) return 'Thunderstorm';
  return 'Fair Weather';
}

/**
 * Fetches live weather data from Open-Meteo for a given latitude and longitude.
 */
export async function fetchLiveWeather(latitude: number, longitude: number): Promise<WeatherData> {
  try {
    const url = `https://api.open-meteo.com/v1/forecast?latitude=${latitude}&longitude=${longitude}&current=temperature_2m,relative_humidity_2m,weather_code,wind_speed_10m,wind_direction_10m&hourly=temperature_2m,weather_code&timezone=Asia%2FManila&forecast_hours=24`;

    const response = await fetch(url, {
      next: { revalidate: 300 }, // Cache on server for 5 minutes
    });

    if (!response.ok) {
      throw new Error(`Open-Meteo HTTP ${response.status}`);
    }

    const data = await response.json();
    const current = data.current;
    const hourly = data.hourly;

    if (
      typeof current?.temperature_2m !== 'number' ||
      typeof current?.relative_humidity_2m !== 'number' ||
      typeof current?.wind_speed_10m !== 'number' ||
      typeof current?.wind_direction_10m !== 'number' ||
      typeof current?.weather_code !== 'number'
    ) {
      throw new Error('Open-Meteo returned incomplete current conditions.');
    }

    const temperature = current.temperature_2m;
    const relativeHumidity = current.relative_humidity_2m;
    const windSpeed = current.wind_speed_10m;
    const windDirection = current.wind_direction_10m;
    const weatherCode = current.weather_code;

    const heatIndex = calculateHeatIndex(temperature, relativeHumidity);

    // Process hourly forecast - take every 3rd hour for 6-hourly display
    const hourlyForecast: HourlyForecast[] = [];
    if (hourly && hourly.time && hourly.temperature_2m && hourly.weather_code) {
      for (let i = 0; i < hourly.time.length; i += 3) {
        hourlyForecast.push({
          time: hourly.time[i],
          temperature: hourly.temperature_2m[i],
          weatherCode: hourly.weather_code[i],
        });
        if (hourlyForecast.length >= 6) break; // Limit to 6 forecast points
      }
    }

    return {
      temperature,
      relativeHumidity,
      windSpeed,
      windDirection,
      weatherCode,
      conditionLabel: mapWeatherCode(weatherCode),
      heatIndex,
      timestamp: current.time || new Date().toISOString(),
      hourly: hourlyForecast,
    };
  } catch (error) {
    console.error('Failed to fetch Open-Meteo weather:', error);
    throw error;
  }
}
