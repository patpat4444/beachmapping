import { NextResponse } from 'next/server';
import { createAdminClient } from '@/lib/supabase/server';
import { hashPin } from '@/lib/services/pin';

export async function POST(request: Request) {
  try {
    const body = await request.json();
    const { username, pin, portal } = body;

    if (!pin || typeof pin !== 'string' || !/^\d{6}$/.test(pin.trim())) {
      return NextResponse.json(
        { error: 'Please enter a valid 6-digit numeric PIN.' },
        { status: 400 }
      );
    }

    const cleanPin = pin.trim();
    const pinHash = hashPin(cleanPin);
    const normalizedUsername = typeof username === 'string' ? username.trim() : '';
    const adminSupabase = createAdminClient();

    // =========================================================================
    // 1. MUNICIPAL ADMIN LOGIN (/admin/login)
    // =========================================================================
    if (portal === 'admin') {
      // The admin username is fixed in the UI and should align with the stored profile.
      if (normalizedUsername && normalizedUsername !== 'Administrator') {
        return NextResponse.json(
          { error: 'Invalid username. Please use "Administrator".' },
          { status: 401 }
        );
      }

      const adminEmail = 'official.dagattabai@gmail.com';
      const previousAdminEmail = 'admin@dagattabai.ph';

      // eslint-disable-next-line @typescript-eslint/no-explicit-any
      let { data: adminProfile } = await (adminSupabase.from('profiles') as any)
        .select('id, email, full_name, role, pin')
        .or(`email.eq.${adminEmail},email.eq.${previousAdminEmail},full_name.eq.Administrator,full_name.eq.administrator`)
        .in('role', ['admin', 'beach_manager'])
        .maybeSingle();

      if (!adminProfile || !adminProfile.id) {
        const initialAdminPin = process.env.ADMIN_INITIAL_PIN;
        if (!initialAdminPin || !/^\d{6}$/.test(initialAdminPin)) {
          return NextResponse.json(
            { error: 'Admin setup is incomplete. Configure ADMIN_INITIAL_PIN and run the Supabase migration.' },
            { status: 503 }
          );
        }
        if (cleanPin !== initialAdminPin) {
          return NextResponse.json({ error: 'Invalid 6-digit Admin PIN. Access denied.' }, { status: 401 });
        }

        const { data: existingUser } = await adminSupabase.auth.admin.listUsers();
        const matched = existingUser?.users?.find(
          (user) => [adminEmail, previousAdminEmail].includes(user.email?.toLowerCase() || '')
        );

        if (matched) {
          adminProfile = {
            id: matched.id,
            email: adminEmail,
            full_name: 'Administrator',
            role: 'admin',
            pin: pinHash,
          };
        } else {
          const { data: authData, error: authError } = await adminSupabase.auth.admin.createUser({
            email: adminEmail,
            password: cleanPin,
            email_confirm: true,
            user_metadata: { name: 'Administrator', role: 'admin' },
          });

          if (authError || !authData?.user) {
            throw new Error(authError?.message || 'Failed to initialize admin account.');
          }

          adminProfile = {
            id: authData.user.id,
            email: adminEmail,
            full_name: 'Administrator',
            role: 'admin',
            pin: pinHash,
          };
        }

        const { error: profileUpsertError } = await (adminSupabase.from('profiles') as any).upsert({
          id: adminProfile.id,
          full_name: 'Administrator',
          email: adminEmail,
          role: 'admin',
          pin: pinHash,
          updated_at: new Date().toISOString(),
        });
        if (profileUpsertError) throw profileUpsertError;
      }

      const storedPin = adminProfile.pin || '';
      if (storedPin !== pinHash && storedPin !== cleanPin) {
        return NextResponse.json(
          { error: 'Invalid 6-digit Admin PIN. Access denied.' },
          { status: 401 }
        );
      }

      if (storedPin !== pinHash) {
        const { error: pinUpdateError } = await (adminSupabase.from('profiles') as any)
          .update({ pin: pinHash, updated_at: new Date().toISOString() })
          .eq('id', adminProfile.id);
        if (pinUpdateError) throw pinUpdateError;
      }

      const { error: passwordSyncError } = await adminSupabase.auth.admin.updateUserById(
        adminProfile.id,
        { email: adminEmail, email_confirm: true, password: cleanPin }
      );
      if (passwordSyncError) {
        throw new Error(passwordSyncError.message || 'Failed to sync admin sign-in.');
      }

      const { error: adminEmailSyncError } = await (adminSupabase.from('profiles') as any)
        .update({ email: adminEmail, updated_at: new Date().toISOString() })
        .eq('id', adminProfile.id);
      if (adminEmailSyncError) throw adminEmailSyncError;

      const email = adminEmail;

      return NextResponse.json({
        success: true,
        email,
        role: 'admin',
        redirect: '/admin/dashboard',
      });
    }

    // =========================================================================
    // 2. BEACH OWNER & RESORT STAFF LOGIN (/owner/login)
    // =========================================================================
    if (portal === 'owner') {
      const ownerEmail = typeof body.email === 'string' ? body.email.trim().toLowerCase() : '';
      if (!ownerEmail || !/^\S+@\S+\.\S+$/.test(ownerEmail)) {
        return NextResponse.json({ error: 'Enter the email address used in your owner application.' }, { status: 400 });
      }

      // eslint-disable-next-line @typescript-eslint/no-explicit-any
      let { data: ownerProfile } = await (adminSupabase.from('profiles') as any)
        .select('id, email, full_name, role, pin')
        .eq('role', 'beach_owner')
        .ilike('email', ownerEmail)
        .eq('pin', pinHash)
        .maybeSingle();

      if (!ownerProfile) {
        const { data: legacyProfile } = await (adminSupabase.from('profiles') as any)
          .select('id, email, full_name, role, pin')
          .eq('role', 'beach_owner')
          .ilike('email', ownerEmail)
          .eq('pin', cleanPin)
          .maybeSingle();
        if (legacyProfile) {
          const { error: pinUpdateError } = await (adminSupabase.from('profiles') as any)
            .update({ pin: pinHash, updated_at: new Date().toISOString() })
            .eq('id', legacyProfile.id);
          if (pinUpdateError) throw pinUpdateError;
          ownerProfile = { ...legacyProfile, pin: pinHash };
        }
      }

      if (!ownerProfile) {
        return NextResponse.json(
          { error: 'Invalid 6-digit PIN. Please check the PIN sent to your email.' },
          { status: 401 }
        );
      }

      const { data: authUser } = await adminSupabase.auth.admin.getUserById(ownerProfile.id);
      const email = authUser?.user?.email || ownerProfile.email;

      if (!email) {
        return NextResponse.json(
          { error: 'Owner account not found. Please contact support.' },
          { status: 404 }
        );
      }

      return NextResponse.json({
        success: true,
        email,
        role: 'beach_owner',
        redirect: '/owner/dashboard',
      });
    }

    return NextResponse.json({ error: 'Invalid portal specified.' }, { status: 400 });
  } catch (err: unknown) {
    const msg = err instanceof Error ? err.message : 'Server error during PIN verification';
    console.error('PIN login error:', msg);
    return NextResponse.json({ error: msg }, { status: 500 });
  }
}
