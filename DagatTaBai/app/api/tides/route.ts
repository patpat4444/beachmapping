import { NextRequest, NextResponse } from 'next/server';
import { fetchStormglassTides } from '@/lib/services/tides';
import { calculateCurrentTide } from '@/lib/services/tide-interpolation';

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

    // Fetch tide extremes from Stormglass
    const tideExtremes = await fetchStormglassTides(lat, lon);

    // Calculate current real-time tide state
    const currentTide = calculateCurrentTide(tideExtremes);

    return NextResponse.json({
      extremes: tideExtremes,
      current: currentTide,
    });
  } catch (error: unknown) {
    const msg = error instanceof Error ? error.message : 'Failed to fetch tides';
    return NextResponse.json({ error: msg }, { status: 500 });
  }
}
