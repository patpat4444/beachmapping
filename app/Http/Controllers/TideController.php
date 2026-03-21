<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TideController extends Controller
{
    /**
     * Get tide data for a location
     */
    public function getTides(Request $request)
    {
        $lat = $request->input('lat', 10.6980);
        $lng = $request->input('lng', 124.0020);
        $days = $request->input('days', 30);

        try {
            // Try World Tides API (free, no key needed for basic usage)
            $response = Http::get('https://www.worldtides.info/api/v3', [
                'lat' => $lat,
                'lon' => $lng,
                'days' => $days,
                'datum' => 'CD',
                'step' => 3600,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $tides = $this->processTideData($data);
                
                return response()->json([
                    'location' => ['lat' => $lat, 'lng' => $lng],
                    'period' => $days . ' days',
                    'current' => $tides['current'] ?? null,
                    'predictions' => $tides['predictions'] ?? [],
                    'high_tides' => $tides['high_tides'] ?? [],
                    'low_tides' => $tides['low_tides'] ?? [],
                    'monthly_stats' => $tides['monthly_stats'] ?? null,
                ]);
            }

            // Fallback to simulated data
            return $this->getSimulatedTideData($lat, $lng, $days);

        } catch (\Exception $e) {
            return $this->getSimulatedTideData($lat, $lng, $days);
        }
    }

    private function processTideData($data)
    {
        $predictions = [];
        $highTides = [];
        $lowTides = [];
        $current = null;
        $now = now()->timestamp;

        if (isset($data['predictions'])) {
            foreach ($data['predictions'] as $prediction) {
                $time = $prediction['time'] ?? null;
                $height = $prediction['height'] ?? 0;
                $type = $prediction['type'] ?? null;

                if ($time) {
                    $timestamp = strtotime($time);
                    $predictionData = [
                        'time' => $time,
                        'timestamp' => $timestamp,
                        'height' => round($height, 2),
                        'type' => $type,
                        'formatted_time' => date('M j, g:i A', $timestamp),
                    ];

                    $predictions[] = $predictionData;

                    if (!$current || abs($timestamp - $now) < abs($current['timestamp'] - $now)) {
                        $current = $predictionData;
                    }

                    if ($type === 'High') {
                        $highTides[] = $predictionData;
                    } elseif ($type === 'Low') {
                        $lowTides[] = $predictionData;
                    }
                }
            }
        }

        $heights = array_column($predictions, 'height');
        $monthlyStats = [
            'highest_tide' => !empty($heights) ? max($heights) : 0,
            'lowest_tide' => !empty($heights) ? min($heights) : 0,
            'average_high' => !empty($highTides) ? array_sum(array_column($highTides, 'height')) / count($highTides) : 0,
            'average_low' => !empty($lowTides) ? array_sum(array_column($lowTides, 'height')) / count($lowTides) : 0,
            'total_high_tides' => count($highTides),
            'total_low_tides' => count($lowTides),
        ];

        return [
            'predictions' => $predictions,
            'high_tides' => $highTides,
            'low_tides' => $lowTides,
            'current' => $current,
            'monthly_stats' => $monthlyStats,
        ];
    }

    private function getSimulatedTideData($lat, $lng, $days)
    {
        $predictions = [];
        $highTides = [];
        $lowTides = [];
        $now = now();

        $meanHighWater = 1.4;
        $meanLowWater = 0.2;
        $tideRange = $meanHighWater - $meanLowWater;

        $hours = $days * 24;
        
        for ($i = 0; $i < $hours; $i += 6) {
            $time = $now->copy()->addHours($i);
            
            $dayFraction = ($i % 12.42) / 12.42;
            $height = $meanLowWater + ($tideRange / 2) + ($tideRange / 2) * sin($dayFraction * 2 * M_PI);
            $height += rand(-10, 10) / 100;
            $height = round(max(0, $height), 2);

            $type = ($height > ($meanLowWater + $tideRange / 2)) ? 'High' : 'Low';

            $pred = [
                'time' => $time->toIso8601String(),
                'timestamp' => $time->timestamp,
                'height' => $height,
                'type' => $type,
                'formatted_time' => $time->format('M j, g:i A'),
            ];

            $predictions[] = $pred;

            if ($type === 'High') {
                $highTides[] = $pred;
            } else {
                $lowTides[] = $pred;
            }
        }

        $heights = array_column($predictions, 'height');
        
        return response()->json([
            'location' => ['lat' => $lat, 'lng' => $lng],
            'period' => $days . ' days',
            'source' => 'simulated_philippines_semi_diurnal',
            'current' => $predictions[0] ?? null,
            'predictions' => $predictions,
            'high_tides' => array_slice($highTides, 0, 20),
            'low_tides' => array_slice($lowTides, 0, 20),
            'monthly_stats' => [
                'highest_tide' => !empty($heights) ? max($heights) : 0,
                'lowest_tide' => !empty($heights) ? min($heights) : 0,
                'average_high' => !empty($highTides) ? round(array_sum(array_column($highTides, 'height')) / count($highTides), 2) : 0,
                'average_low' => !empty($lowTides) ? round(array_sum(array_column($lowTides, 'height')) / count($lowTides), 2) : 0,
                'total_high_tides' => count($highTides),
                'total_low_tides' => count($lowTides),
            ],
        ]);
    }
}
