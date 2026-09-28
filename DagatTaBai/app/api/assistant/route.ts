import { NextRequest, NextResponse } from 'next/server';
import { askGeminiAssistant } from '@/lib/services/gemini';
import { getCurrentSession } from '@/lib/db/auth';
import { createAdminClient } from '@/lib/supabase/server';

export async function POST(req: NextRequest) {
  try {
    const body = await req.json();
    const message = body.message;
    const userLocation =
      body.userLocation &&
      typeof body.userLocation.lat === 'number' &&
      typeof body.userLocation.lng === 'number'
        ? { lat: body.userLocation.lat, lng: body.userLocation.lng }
        : undefined;

    if (!message || typeof message !== 'string') {
      return NextResponse.json(
        { error: 'Message content is required.' },
        { status: 400 }
      );
    }

    const user = await getCurrentSession();
    let history: { role: 'user' | 'assistant'; text: string }[] = [];
    const adminClient = user ? createAdminClient() : null;
    if (user && adminClient) {
      const { data: previousMessages, error } = await (adminClient.from('ai_chat_messages') as any)
        .select('role, content')
        .eq('user_id', user.id)
        .order('created_at', { ascending: false })
        .limit(10);
      if (error) throw error;
      history = (previousMessages || []).reverse().map((entry: any) => ({ role: entry.role, text: entry.content }));
    }

    const reply = await askGeminiAssistant(message, userLocation, history, user?.full_name);

    if (user && adminClient) {
      const { error } = await (adminClient.from('ai_chat_messages') as any).insert([
        { user_id: user.id, role: 'user', content: message.trim().slice(0, 500) },
        { user_id: user.id, role: 'assistant', content: reply.slice(0, 8000) },
      ]);
      if (error) throw error;
    }

    return NextResponse.json({ reply, user: user ? { id: user.id, full_name: user.full_name } : null });
  } catch (error: unknown) {
    const msg = error instanceof Error ? error.message : 'Internal server error';
    console.error('API assistant error:', msg);
    return NextResponse.json(
      { error: 'Failed to process assistant request.' },
      { status: 500 }
    );
  }
}
