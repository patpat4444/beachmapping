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
}
