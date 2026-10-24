import { NextRequest, NextResponse } from 'next/server';
import { fetchLiveWeather } from '@/lib/services/weather';

export async function GET(req: NextRequest) {
  try {
    const { searchParams } = new URL(req.url);
    const lat = parseFloat(searchParams.get('lat') || '10.635');
    const lon = parseFloat(searchParams.get('lon') || '124.027');

    if (isNaN(lat) || isNaN(lon)) {
      return NextResponse.json(
        { error: 'Valid latitude and longitude are required.' },
        { status: 400 }
      );
    }

    const weather = await fetchLiveWeather(lat, lon);
    return NextResponse.json(weather);
  } catch (error: unknown) {
    const msg = error instanceof Error ? error.message : 'Failed to fetch weather';
    return NextResponse.json({ error: msg }, { status: 500 });
  }
}
