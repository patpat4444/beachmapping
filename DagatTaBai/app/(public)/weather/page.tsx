'use client';

import React, { useState, useEffect } from 'react';
import Link from 'next/link';
import {
  Waves,
  Wind,
  Droplets,
  Sun,
  Compass,
  ArrowRight,
  ShieldCheck,
  Eye,
  Umbrella,
  Sun as SunIcon,
  FlaskConical,
  Cloud,
  CloudRain,
  AlertTriangle,
} from 'lucide-react';
import type { WeatherData } from '@/lib/services/weather';
import { calculateCurrentTide, type CurrentTideState, type TideExtreme } from '@/lib/services/tide-interpolation';
import type { BeachRecord } from '@/lib/db/beaches';

// Helper to convert wind direction degrees to compass direction
function getWindDirection(degrees: number): string {
  const directions = ['N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE', 'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW'];
  const index = Math.round(degrees / 22.5) % 16;
  return directions[index];
}

// Helper to get weather icon based on weather code
function getWeatherIcon(weatherCode: number) {
  if (weatherCode >= 51 && weatherCode <= 67) return Droplets; // Rain/Drizzle
  if (weatherCode >= 95) return Droplets; // Thunderstorm
  if (weatherCode >= 45 && weatherCode <= 48) return Sun; // Fog
  return Sun; // Clear/Cloudy
}

// Generate smart reminders based on weather conditions
function generateReminders(weather: WeatherData | null, currentTide: CurrentTideState | null) {
  const reminders: { icon: any; text: string; type: 'info' | 'warning' | 'alert' }[] = [];

  if (!weather) return reminders;

  // Rain/Drizzle reminders
  if (weather.weatherCode >= 51 && weather.weatherCode <= 67) {
    reminders.push({
      icon: Umbrella,
      text: 'Bring umbrella or rain gear',
      type: 'warning',
    });
  }

  // Thunderstorm alert
  if (weather.weatherCode >= 95) {
    reminders.push({
      icon: AlertTriangle,
      text: 'Seek shelter - thunderstorm conditions',
      type: 'alert',
    });
  }

  // Hot weather sunscreen reminder
  if (weather.temperature >= 30) {
    reminders.push({
      icon: SunIcon,
      text: 'Apply sunblock/sunscreen regularly',
      type: 'warning',
    });
  }

  // UV protection reminder for clear/partly cloudy
  if (weather.weatherCode <= 2 && weather.temperature >= 28) {
    reminders.push({
      icon: Sun,
      text: 'Wear sunglasses and hat for UV protection',
      type: 'info',
    });
  }

  // Hydration reminder for hot weather
  if (weather.temperature >= 32 || (weather.heatIndex && weather.heatIndex.heatIndexCelsius >= 35)) {
    reminders.push({
      icon: FlaskConical,
      text: 'Stay hydrated - bring plenty of water',
      type: 'warning',
    });
  }

  // Fog/low visibility reminder
  if (weather.weatherCode >= 45 && weather.weatherCode <= 48) {
    reminders.push({
      icon: Cloud,
      text: 'Low visibility - use caution near water',
      type: 'warning',
    });
  }

  // Low tide sandbar window reminder
  if (currentTide && currentTide.currentHeight < 0.8) {
    reminders.push({
      icon: Waves,
      text: 'Low tide - great time for sandbar activities',
      type: 'info',
    });
  }

  // Swimming safety based on tide
  if (currentTide && currentTide.currentHeight > 1.5) {
    reminders.push({
      icon: ShieldCheck,
      text: 'High tide - swim with caution near shore',
      type: 'warning',
    });
  }

  return reminders;
}

export default function WeatherPage() {
  const [beaches, setBeaches] = useState<BeachRecord[]>([]);
  const [selectedBeachId, setSelectedBeachId] = useState<string>('');
  const [weather, setWeather] = useState<WeatherData | null>(null);
  const [tideExtremes, setTideExtremes] = useState<TideExtreme[]>([]);
  const [currentTide, setCurrentTide] = useState<CurrentTideState | null>(null);
  const [loading, setLoading] = useState(true);
  const [dataError, setDataError] = useState<string | null>(null);

  useEffect(() => {
    fetch('/api/beaches')
      .then((res) => {
        if (!res.ok) throw new Error('Could not load beach locations.');
        return res.json();
      })
      .then((data: BeachRecord[]) => {
        if (Array.isArray(data) && data.length > 0) {
          setBeaches(data);
          setSelectedBeachId((prev) => prev || data[0].id);
        } else {
          setDataError('No active beach locations are available yet.');
          setLoading(false);
        }
      })
      .catch((err) => {
        console.error('Failed to load beaches for weather:', err);
        setDataError('Could not load beach locations.');
        setLoading(false);
      });
  }, []);

  const selectedBeach = beaches.find((b) => b.id === selectedBeachId) || beaches[0] || null;

  useEffect(() => {
    if (!selectedBeach) return;
    setLoading(true);

    setDataError(null);
    const loadConditions = async () => {
      const [weatherResult, tideResult] = await Promise.allSettled([
        fetch(`/api/weather?lat=${selectedBeach.latitude}&lon=${selectedBeach.longitude}`, { cache: 'no-store' })
          .then(async (response) => {
            if (!response.ok) throw new Error('Weather data is unavailable.');
            return response.json() as Promise<WeatherData>;
          }),
        fetch(`/api/tides?lat=${selectedBeach.latitude}&lon=${selectedBeach.longitude}`, { cache: 'no-store' })
          .then(async (response) => {
            if (!response.ok) throw new Error('Tide data is unavailable.');
            return response.json();
          }),
      ]);

      if (weatherResult.status === 'fulfilled') setWeather(weatherResult.value);
      else setWeather(null);
      if (tideResult.status === 'fulfilled') {
        setTideExtremes(Array.isArray(tideResult.value.extremes) ? tideResult.value.extremes : []);
        setCurrentTide(tideResult.value.current || null);
      } else {
        setTideExtremes([]);
        setCurrentTide(null);
      }

      const errors = [weatherResult, tideResult]
        .filter((result): result is PromiseRejectedResult => result.status === 'rejected')
        .map((result) => result.reason instanceof Error ? result.reason.message : 'Some marine readings are unavailable.');
      setDataError(errors.length ? errors.join(' ') : null);
      setLoading(false);
    };

    void loadConditions();
  }, [selectedBeach]);

  return (
    <div className="min-h-screen bg-[#fdfbf8] dark:bg-[#09090b] text-slate-900 dark:text-zinc-100 transition-colors duration-200">
      
      {/* Hero Header */}
      <section className="bg-gradient-to-b from-[#0e0e12] via-[#09090b] to-[#050505] text-white pt-16 pb-20 px-4 sm:px-6 lg:px-8 border-b border-neutral-800">
        <div className="max-w-6xl mx-auto space-y-4">
          <h1 className="text-3xl sm:text-4xl lg:text-5xl font-sans font-bold tracking-tight text-white leading-tight">
            Barangay Binongkalan Weather &amp; Marine Tides
          </h1>

          <p className="text-slate-300 text-sm max-w-2xl leading-relaxed">
            Real-time meteorological readings, Camotes Sea swell conditions, safe low-tide sandbar schedules, and heat indexes across all 5 municipal coastline sanctuaries.
          </p>

          {/* Beach Switcher Pills */}
          <div className="pt-4 flex flex-wrap items-center gap-2">
            {beaches.map((b) => (
              <button
                key={b.id}
                type="button"
                onClick={() => setSelectedBeachId(b.id)}
                className={`px-4 py-2 rounded-full text-xs font-bold transition-all duration-150 ${
                  selectedBeachId === b.id
                    ? 'bg-[#0284c7] text-white shadow-md scale-105'
                    : 'bg-white/10 text-slate-300 hover:bg-white/20 hover:text-white border border-white/10'
                }`}
              >
                {b.name}
              </button>
            ))}
          </div>
        </div>
      </section>

      {/* Main Observatory Content */}
      <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-10">
        {dataError && (
          <p role="status" className="border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            {dataError}
          </p>
        )}
        
        {/* Top Highlight Cards */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          
          {/* 1. Main Temperature & Conditions */}
          <div className="p-6 rounded-2xl bg-white dark:bg-[#121215] border border-slate-200/90 dark:border-neutral-800 shadow-sm space-y-4">
            <div className="flex items-center justify-between">
              <span className="text-xs font-bold uppercase tracking-wider text-slate-400">Current Atmosphere</span>
              <Sun className="w-5 h-5 text-sky-500" />
            </div>

            <div className="flex items-baseline gap-3">
              <span className="text-4xl sm:text-5xl font-serif font-bold text-slate-900 dark:text-white">
                {weather ? `${weather.temperature.toFixed(1)}°` : loading ? 'Loading...' : 'Unavailable'}
              </span>
              <span className="text-sm font-semibold text-slate-500">
                {weather?.conditionLabel || (loading ? 'Loading...' : 'Unavailable')}
              </span>
            </div>

            <div className="pt-3 border-t border-slate-100 dark:border-slate-800 grid grid-cols-2 gap-2 text-xs text-slate-600 dark:text-slate-400">
              <div>
                <span className="block text-[10px] text-slate-400">Feels Like (Heat Index)</span>
                <span className="font-bold text-slate-900 dark:text-white">
                  {weather?.heatIndex ? `${weather.heatIndex.heatIndexCelsius.toFixed(1)}°C` : loading ? 'Loading...' : 'Unavailable'}
                </span>
              </div>
              <div>
                <span className="block text-[10px] text-slate-400">Heat Advisory</span>
                <span className="font-bold text-amber-600 dark:text-amber-400">
                  {weather?.heatIndex ? weather.heatIndex.category : loading ? 'Loading...' : 'Unavailable'}
                </span>
              </div>
            </div>
          </div>

          {/* 2. Sea State & Tides */}
          <div className="p-6 rounded-2xl bg-white dark:bg-[#121215] border border-slate-200/90 dark:border-neutral-800 shadow-sm space-y-4">
            <div className="flex items-center justify-between">
              <span className="text-xs font-bold uppercase tracking-wider text-slate-400">Binongkalan Catmon Cebu Tides</span>
              <Waves className="w-5 h-5 text-[#0284c7]" />
            </div>

            <div className="flex items-baseline gap-3">
              <span className="text-3xl sm:text-4xl font-serif font-bold text-slate-900 dark:text-white">
                {currentTide ? `${currentTide.currentHeight.toFixed(1)}m ${currentTide.status}` : loading ? 'Loading...' : 'Unavailable'}
              </span>
            </div>

            <div className="pt-3 border-t border-slate-100 dark:border-slate-800 grid grid-cols-2 gap-2 text-xs text-slate-600 dark:text-slate-400">
              <div>
                <span className="block text-[10px] text-slate-400">Next Tide</span>
                <span className="font-bold text-emerald-600 dark:text-emerald-400">
                  {currentTide ? `${new Date(currentTide.nextExtreme.time).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })} · ${currentTide.nextExtreme.type}` : loading ? 'Loading...' : 'Unavailable'}
                </span>
              </div>
              <div>
                <span className="block text-[10px] text-slate-400">Water Temperature</span>
                <span className="font-bold text-slate-900 dark:text-white">Unavailable</span>
              </div>
            </div>
          </div>

          {/* 3. Wind & Marine Safety */}
          <div className="p-6 rounded-2xl bg-white dark:bg-[#121215] border border-slate-200/90 dark:border-neutral-800 shadow-sm space-y-4">
            <div className="flex items-center justify-between">
              <span className="text-xs font-bold uppercase tracking-wider text-slate-400">Marine &amp; Swim Flag</span>
              <ShieldCheck className="w-5 h-5 text-emerald-500" />
            </div>

            <div className="flex items-center gap-2">
              <span className="w-3 h-3 rounded-full bg-emerald-500 animate-pulse" />
              <span className="text-xl font-bold text-emerald-700 dark:text-emerald-400">
                Safety data unavailable
              </span>
            </div>

            <div className="pt-3 border-t border-slate-100 dark:border-slate-800 grid grid-cols-2 gap-2 text-xs text-slate-600 dark:text-slate-400">
              <div>
                <span className="block text-[10px] text-slate-400">Wind Velocity</span>
                <span className="font-bold text-slate-900 dark:text-white">
                  {weather ? `${weather.windSpeed.toFixed(1)} km/h · ${getWindDirection(weather.windDirection)}` : loading ? 'Loading...' : 'Unavailable'}
                </span>
              </div>
              <div>
                <span className="block text-[10px] text-slate-400">Swell Height</span>
                <span className="font-bold text-slate-900 dark:text-white">Unavailable</span>
              </div>
            </div>
          </div>

        </div>

        {/* Smart Reminders Section */}
        <div className="p-6 rounded-2xl bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-950/30 dark:to-orange-950/30 border border-amber-200/90 dark:border-amber-800/50 shadow-sm space-y-4">
          <div className="flex items-center justify-between">
            <h3 className="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">
              Smart Reminders
            </h3>
            <span className="text-xs text-amber-600 dark:text-amber-400">Based on current conditions</span>
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            {generateReminders(weather, currentTide).length > 0 ? (
              generateReminders(weather, currentTide).map((reminder, i) => {
                const IconComp = reminder.icon;
                const typeColors = {
                  info: 'bg-blue-50 dark:bg-blue-950/30 border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-300',
                  warning: 'bg-amber-50 dark:bg-amber-950/30 border-amber-200 dark:border-amber-800 text-amber-700 dark:text-amber-300',
                  alert: 'bg-red-50 dark:bg-red-950/30 border-red-200 dark:border-red-800 text-red-700 dark:text-red-300',
                };
                return (
                  <div
                    key={i}
                    className={`p-4 rounded-xl border ${typeColors[reminder.type]} flex items-start gap-3`}
                  >
                    <IconComp className="w-5 h-5 flex-shrink-0 mt-0.5" />
                    <span className="text-xs font-semibold leading-snug">{reminder.text}</span>
                  </div>
                );
              })
            ) : (
              <div className="col-span-full p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-center">
                <span className="text-xs text-slate-500 dark:text-slate-400">
                  {loading ? 'Loading weather data...' : 'No specific weather alerts at this time'}
                </span>
              </div>
            )}
          </div>
        </div>

        {/* Hourly Forecast Strip */}
        <div className="p-6 rounded-2xl bg-white dark:bg-[#121215] border border-slate-200/90 dark:border-neutral-800 shadow-sm space-y-4">
          <div className="flex items-center justify-between">
            <h3 className="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">
              Today&apos;s Coastal Progression
            </h3>
            <span className="text-xs text-slate-400">Updated hourly from Open-Meteo API</span>
          </div>

          <div className="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3 pt-2">
            {weather?.hourly && weather.hourly.length > 0 ? (
              weather.hourly.map((hour, i) => {
                const time = new Date(hour.time);
                const timeStr = time.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                const IconComp = getWeatherIcon(hour.weatherCode);

                // Calculate estimated tide at this hour (simple approximation)
                const hourIndex = Math.floor((time.getHours() / 24) * (tideExtremes.length || 1));
                const tideEstimate = tideExtremes[hourIndex] ? `${tideExtremes[hourIndex].height.toFixed(1)}m (${tideExtremes[hourIndex].type})` : 'N/A';

                return (
                  <div
                    key={i}
                    className="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 text-center space-y-1.5"
                  >
                    <span className="text-[11px] font-bold text-slate-400 block">{timeStr}</span>
                    <IconComp className="w-5 h-5 text-sky-500 mx-auto my-1" />
                    <span className="text-sm font-bold text-slate-900 dark:text-white block">{hour.temperature.toFixed(0)}°C</span>
                    <span className="text-[10px] text-[#0284c7] font-semibold block">{tideEstimate}</span>
                  </div>
                );
              })
            ) : (
              // Fallback while loading
              Array.from({ length: 6 }).map((_, i) => (
                <div
                  key={i}
                  className="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 text-center space-y-1.5 animate-pulse"
                >
                  <div className="h-3 bg-slate-200 dark:bg-slate-700 rounded w-12 mx-auto" />
                  <div className="w-5 h-5 bg-slate-200 dark:bg-slate-700 rounded mx-auto my-1" />
                  <div className="h-4 bg-slate-200 dark:bg-slate-700 rounded w-8 mx-auto" />
                  <div className="h-3 bg-slate-200 dark:bg-slate-700 rounded w-10 mx-auto" />
                </div>
              ))
            )}
          </div>
        </div>

        {/* Selected Resort Details & CTA */}
        {selectedBeach && (
          <div className="p-8 rounded-3xl bg-gradient-to-r from-[#08182b] to-[#0f2942] text-white flex flex-col md:flex-row items-center justify-between gap-6 shadow-xl">
            <div className="space-y-2 max-w-xl">
              <span className="text-xs uppercase tracking-widest text-[#38bdf8] font-bold">
                OBSERVING {selectedBeach.name.toUpperCase()}
              </span>
              <h3 className="text-2xl sm:text-3xl font-serif font-bold text-white">
                Ready to explore {selectedBeach.name}?
              </h3>
              <p className="text-xs sm:text-sm text-slate-300 leading-relaxed">
                Check entrance rates ({selectedBeach.entrance_fee}), cottages ({selectedBeach.cottage_fee}), 360° shoreline scans, and route instructions.
              </p>
            </div>

            <div className="flex flex-wrap items-center gap-3 flex-shrink-0">
              <Link
                href={`/beaches/${selectedBeach.slug || selectedBeach.id}`}
                className="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-[#0284c7] hover:bg-[#0369a1] text-white text-xs sm:text-sm font-bold transition-all shadow-md hover:scale-105"
              >
                <Eye className="w-4 h-4" />
                <span>Inspect Profile</span>
              </Link>
              <Link
                href={`/map?selected=${selectedBeach.slug || selectedBeach.id}`}
                className="inline-flex items-center gap-2 px-5 py-3 rounded-full bg-white/10 hover:bg-white/20 text-white text-xs sm:text-sm font-semibold border border-white/20 transition-all"
              >
                <Compass className="w-4 h-4" />
                <span>Open on Map</span>
              </Link>
            </div>
          </div>
        )}

      </div>

    </div>
  );
}
