<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    /**
     * Get current weather from OpenWeather API by latitude and longitude.
     * Proxies the request so the API key is never exposed to the frontend.
     */
    public function current(Request $request)
    {
        $lat = $request->query('lat');
        $lng = $request->query('lon');

        if ($lat === null || $lng === null) {
            return response()->json(['error' => 'lat and lon are required'], 400);
        }

        $lat = (float) $lat;
        $lng = (float) $lng;

        $apiKey = config('services.openweather.api_key');
        if (! $apiKey) {
            return response()->json(['error' => 'Weather service not configured'], 503);
        }

        $url = 'https://api.openweathermap.org/data/2.5/weather';
        $response = Http::withoutVerifying()->get($url, [
            'lat' => $lat,
            'lon' => $lng,
            'appid' => $apiKey,
            'units' => 'metric',
        ]);

        if (! $response->successful()) {
            return response()->json(
                ['error' => 'Unable to fetch weather', 'details' => $response->json()],
                $response->status()
            );
        }

        $data = $response->json();

        $uvIndex = null;
        $oneCallUrl = 'https://api.openweathermap.org/data/3.0/onecall';
        $oneCall = Http::withoutVerifying()->get($oneCallUrl, [
            'lat' => $lat,
            'lon' => $lng,
            'appid' => $apiKey,
            'units' => 'metric',
            'exclude' => 'minutely,hourly,daily,alerts',
        ]);
        if ($oneCall->successful()) {
            $one = $oneCall->json();
            $uvIndex = $one['current']['uvi'] ?? null;
        }

        return response()->json([
            'temp' => $data['main']['temp'] ?? null,
            'feels_like' => $data['main']['feels_like'] ?? null,
            'humidity' => $data['main']['humidity'] ?? null,
            'description' => $data['weather'][0]['description'] ?? null,
            'icon' => $data['weather'][0]['icon'] ?? null,
            'wind_speed' => $data['wind']['speed'] ?? null,
            'name' => $data['name'] ?? null,
            'pressure' => $data['main']['pressure'] ?? null,
            'clouds' => $data['clouds']['all'] ?? null,
            'visibility' => $data['visibility'] ?? null,
            'uv_index' => $uvIndex,
            'updated_at' => now()->toIso8601String(),
        ]);
    }

    /**
     * Get comprehensive weather data including current, forecast, and marine data.
     * Combines OpenWeather (current + forecast) + Open-Meteo (tides + waves).
     */
    public function comprehensive(Request $request)
    {
        $lat = $request->query('lat', 10.725);
        $lng = $request->query('lon', 124.009);

        $lat = (float) $lat;
        $lng = (float) $lng;

        $data = [
            'location' => ['lat' => $lat, 'lng' => $lng, 'name' => 'Catmon, Cebu'],
            'current' => null,
            'forecast' => null,
            'marine' => null,
            'tides' => null,
        ];

        // 1. Fetch OpenWeather current data
        $apiKey = config('services.openweather.api_key');
        if ($apiKey) {
            try {
                $response = Http::withoutVerifying()->get('https://api.openweathermap.org/data/2.5/weather', [
                    'lat' => $lat,
                    'lon' => $lng,
                    'appid' => $apiKey,
                    'units' => 'metric',
                ]);

                if ($response->successful()) {
                    $weather = $response->json();
                    $data['current'] = [
                        'temp' => $weather['main']['temp'] ?? null,
                        'feels_like' => $weather['main']['feels_like'] ?? null,
                        'humidity' => $weather['main']['humidity'] ?? null,
                        'pressure' => $weather['main']['pressure'] ?? null,
                        'description' => $weather['weather'][0]['description'] ?? null,
                        'icon' => $weather['weather'][0]['icon'] ?? null,
                        'wind_speed' => $weather['wind']['speed'] ?? null,
                        'wind_direction' => $weather['wind']['deg'] ?? null,
                        'clouds' => $weather['clouds']['all'] ?? null,
                        'visibility' => $weather['visibility'] ?? null,
                    ];
                }

                // Fetch UV index
                $oneCall = Http::withoutVerifying()->get('https://api.openweathermap.org/data/3.0/onecall', [
                    'lat' => $lat,
                    'lon' => $lng,
                    'appid' => $apiKey,
                    'units' => 'metric',
                    'exclude' => 'minutely,hourly,daily,alerts',
                ]);
                if ($oneCall->successful()) {
                    $data['current']['uv_index'] = $oneCall->json()['current']['uvi'] ?? null;
                }

                // Fetch 5-day forecast
                $forecastRes = Http::withoutVerifying()->get('https://api.openweathermap.org/data/2.5/forecast', [
                    'lat' => $lat,
                    'lon' => $lng,
                    'appid' => $apiKey,
                    'units' => 'metric',
                ]);
                if ($forecastRes->successful()) {
                    $forecastData = $forecastRes->json();
                    $daily = [];
                    foreach ($forecastData['list'] ?? [] as $item) {
                        $date = date('Y-m-d', $item['dt']);
                        if (!isset($daily[$date])) {
                            $daily[$date] = [
                                'date' => $date,
                                'day_name' => date('l', $item['dt']),
                                'temps' => [],
                                'humidity' => [],
                                'icons' => [],
                                'descriptions' => [],
                            ];
                        }
                        $daily[$date]['temps'][] = $item['main']['temp'];
                        $daily[$date]['humidity'][] = $item['main']['humidity'];
                        $daily[$date]['icons'][] = $item['weather'][0]['icon'] ?? '01d';
                        $daily[$date]['descriptions'][] = $item['weather'][0]['description'] ?? '';
                    }

                    $forecast = [];
                    $count = 0;
                    foreach ($daily as $day) {
                        if ($count >= 7) break;
                        $forecast[] = [
                            'date' => $day['date'],
                            'day_name' => $count === 0 ? 'Today' : substr($day['day_name'], 0, 3),
                            'temp_max' => round(max($day['temps'])),
                            'temp_min' => round(min($day['temps'])),
                            'humidity' => round(array_sum($day['humidity']) / count($day['humidity'])),
                            'icon' => $day['icons'][4] ?? $day['icons'][0], // midday icon
                            'description' => $day['descriptions'][4] ?? $day['descriptions'][0],
                        ];
                        $count++;
                    }
                    $data['forecast'] = $forecast;
                }
            } catch (\Exception $e) {
                // Log error but continue
            }
        }

        // 2. Fetch Open-Meteo Marine data (FREE - no API key!)
        try {
            $marineRes = Http::withoutVerifying()->get('https://marine-api.open-meteo.com/v1/marine', [
                'latitude' => $lat,
                'longitude' => $lng,
                'current' => 'wave_height,sea_surface_temperature,wave_direction,wave_period',
                'timezone' => 'auto',
            ]);
            if ($marineRes->successful()) {
                $marine = $marineRes->json();
                $current = $marine['current'] ?? [];
                $data['marine'] = [
                    'wave_height' => $current['wave_height'] ?? null,
                    'sea_temp' => $current['sea_surface_temperature'] ?? null,
                    'wave_direction' => $current['wave_direction'] ?? null,
                    'wave_period' => $current['wave_period'] ?? null,
                ];
            }
        } catch (\Exception $e) {
            // Continue without marine data
        }

        // 3. Calculate approximate tides
        $data['tides'] = $this->calculateApproximateTides();

        return response()->json($data);
    }

    /**
     * Get 7-day forecast for a location
     */
    public function forecast(Request $request)
    {
        $lat = $request->query('lat', 10.725);
        $lng = $request->query('lon', 124.009);

        $apiKey = config('services.openweather.api_key');
        if (! $apiKey) {
            return response()->json(['error' => 'Weather service not configured'], 503);
        }

        $response = Http::withoutVerifying()->get('https://api.openweathermap.org/data/2.5/forecast', [
            'lat' => $lat,
            'lon' => $lng,
            'appid' => $apiKey,
            'units' => 'metric',
        ]);

        if (! $response->successful()) {
            return response()->json(['error' => 'Unable to fetch forecast'], $response->status());
        }

        $data = $response->json();
        $daily = [];
        foreach ($data['list'] ?? [] as $item) {
            $date = date('Y-m-d', $item['dt']);
            if (!isset($daily[$date])) {
                $daily[$date] = [
                    'date' => $date,
                    'day_name' => date('l', $item['dt']),
                    'temps' => [],
                    'icons' => [],
                    'descriptions' => [],
                ];
            }
            $daily[$date]['temps'][] = $item['main']['temp'];
            $daily[$date]['icons'][] = $item['weather'][0]['icon'] ?? '01d';
            $daily[$date]['descriptions'][] = $item['weather'][0]['description'] ?? '';
        }

        $forecast = [];
        $count = 0;
        foreach ($daily as $day) {
            if ($count >= 7) break;
            $forecast[] = [
                'date' => $day['date'],
                'day_name' => $count === 0 ? 'Today' : substr($day['day_name'], 0, 3),
                'temp_max' => round(max($day['temps'])),
                'temp_min' => round(min($day['temps'])),
                'icon' => $day['icons'][4] ?? $day['icons'][0],
                'description' => $day['descriptions'][4] ?? $day['descriptions'][0],
            ];
            $count++;
        }

        return response()->json([
            'location' => $data['city']['name'] ?? 'Unknown',
            'forecast' => $forecast,
        ]);
    }

    /**
     * Calculate approximate tide times
     */
    private function calculateApproximateTides()
    {
        $now = time();
        $date = date('Y-m-d', $now);
        
        // Approximate tide times for Philippines (semi-diurnal tides)
        // High tides around 6am and 6pm, low tides around 12pm and 12am
        $baseTimes = [
            'high' => [strtotime($date . ' 06:00'), strtotime($date . ' 18:00')],
            'low' => [strtotime($date . ' 12:00'), strtotime($date . ' 00:00')],
        ];

        $currentHour = date('G', $now);
        
        // Determine current tide status
        $tideStatus = 'rising';
        $nextHigh = '';
        $nextLow = '';
        
        foreach ($baseTimes['high'] as $highTime) {
            if ($highTime > $now) {
                $nextHigh = date('g:i A', $highTime);
                break;
            }
        }
        if (empty($nextHigh)) {
            $nextHigh = date('g:i A', strtotime($date . ' +1 day 06:00'));
        }

        foreach ($baseTimes['low'] as $lowTime) {
            if ($lowTime > $now) {
                $nextLow = date('g:i A', $lowTime);
                break;
            }
        }
        if (empty($nextLow)) {
            $nextLow = date('g:i A', strtotime($date . ' +1 day 12:00'));
        }

        // Determine if rising or falling based on nearest tide
        $nextHighTime = strtotime($nextHigh);
        $nextLowTime = strtotime($nextLow);
        
        if ($nextHighTime < $nextLowTime) {
            $tideStatus = 'rising';
        } else {
            $tideStatus = 'falling';
        }

        return [
            'status' => $tideStatus,
            'next_high' => $nextHigh,
            'next_low' => $nextLow,
            'note' => 'Approximate times. Add Stormglass API key for precise tide data.',
        ];
    }
}
