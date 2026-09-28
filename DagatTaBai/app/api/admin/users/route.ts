import { NextRequest, NextResponse } from 'next/server';
import { createAdminClient, createServerSupabaseClient } from '@/lib/supabase/server';

async function getAuthorizedAdmin() {
  const sessionClient = await createServerSupabaseClient();
  const { data: authData, error: authError } = await sessionClient.auth.getUser();
  if (authError || !authData.user) return { response: NextResponse.json({ error: 'Authentication required.' }, { status: 401 }) };

  const adminClient = createAdminClient();
  const { data: profile, error } = await (adminClient.from('profiles') as any)
    .select('role')
    .eq('id', authData.user.id)
    .maybeSingle();
  if (error) throw error;
  if (profile?.role !== 'admin') return { response: NextResponse.json({ error: 'Administrator access required.' }, { status: 403 }) };

  return { adminClient };
}

export async function GET() {
  try {
    const auth = await getAuthorizedAdmin();
    if (auth.response) return auth.response;

    const { data: authData, error: usersError } = await auth.adminClient!.auth.admin.listUsers({ page: 1, perPage: 1000 });
    if (usersError) throw usersError;
    const authUsers = authData.users;
    const ids = authUsers.map((user) => user.id);
    const { data: profiles, error: profilesError } = ids.length
      ? await (auth.adminClient!.from('profiles') as any).select('id, full_name, email, role').in('id', ids)
      : { data: [], error: null };
    if (profilesError) throw profilesError;

    const profileById = new Map((profiles || []).map((profile: any) => [profile.id, profile]));
    const users = authUsers.map((user) => {
      const profile = profileById.get(user.id) as any;
      return {
        id: user.id,
        name: profile?.full_name || user.user_metadata?.full_name || user.email?.split('@')[0] || 'User',
        email: profile?.email || user.email || '',
        role: profile?.role || 'user',
        status: user.banned_until && Date.parse(user.banned_until) > Date.now() ? 'suspended' : 'active',
        joined: user.created_at,
      };
    });

    return NextResponse.json({ users }, { headers: { 'Cache-Control': 'no-store, max-age=0' } });
  } catch (error: unknown) {
    const message = error instanceof Error ? error.message : 'Failed to load users.';
    console.error('Admin users GET error:', message);
    return NextResponse.json({ error: message }, { status: 500 });
  }
}

export async function PATCH(request: NextRequest) {
  try {
    const auth = await getAuthorizedAdmin();
    if (auth.response) return auth.response;

    const body = await request.json();
    if (typeof body.id !== 'string' || typeof body.suspended !== 'boolean') {
      return NextResponse.json({ error: 'User ID and suspension status are required.' }, { status: 400 });
    }

    const { data: targetProfile, error: profileError } = await (auth.adminClient!.from('profiles') as any)
      .select('role')
      .eq('id', body.id)
      .maybeSingle();
    if (profileError) throw profileError;
    if (!targetProfile) return NextResponse.json({ error: 'User profile not found.' }, { status: 404 });
    if (targetProfile.role === 'admin') return NextResponse.json({ error: 'Administrator accounts cannot be suspended here.' }, { status: 400 });

    const { data, error } = await auth.adminClient!.auth.admin.updateUserById(body.id, {
      ban_duration: body.suspended ? '876000h' : 'none',
    });
    if (error) throw error;

    return NextResponse.json({
      success: true,
      status: data.user.banned_until && Date.parse(data.user.banned_until) > Date.now() ? 'suspended' : 'active',
    });
  } catch (error: unknown) {
    const message = error instanceof Error ? error.message : 'Failed to update user status.';
    console.error('Admin users PATCH error:', message);
    return NextResponse.json({ error: message }, { status: 500 });
  }
}