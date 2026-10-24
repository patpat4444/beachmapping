import { NextResponse } from 'next/server';
import { createAdminClient } from '@/lib/supabase/server';
import { hashPin } from '@/lib/services/pin';
import nodemailer from 'nodemailer';

export async function POST(request: Request) {
  try {
    const { email } = await request.json();

    if (!email || typeof email !== 'string') {
      return NextResponse.json(
        { error: 'Email address is required.' },
        { status: 400 }
      );
    }

    const normalizedEmail = email.trim().toLowerCase();
    const adminEmail = 'official.dagattabai@gmail.com';

    // Only allow reset for the actual admin email
    if (normalizedEmail !== adminEmail) {
      // Return success anyway to prevent email enumeration
      return NextResponse.json({ success: true });
    }

    const adminSupabase = createAdminClient();

    // Find the admin profile
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const { data: adminProfile, error: lookupError } = await (adminSupabase.from('profiles') as any)
      .select('id, email, role')
      .eq('role', 'admin')
      .ilike('email', adminEmail)
      .maybeSingle();

    if (lookupError) {
      console.error('Admin profile lookup error:', lookupError);
      throw lookupError;
    }

    if (!adminProfile) {
      // No admin profile found — return success to prevent enumeration
      return NextResponse.json({ success: true });
    }

    // Generate a random 6-digit PIN
    const newPin = String(Math.floor(100000 + Math.random() * 900000));
    const pinHash = hashPin(newPin);

    // Update the PIN in the profiles table
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const { error: updateError } = await (adminSupabase.from('profiles') as any)
      .update({ pin: pinHash, updated_at: new Date().toISOString() })
      .eq('id', adminProfile.id);

    if (updateError) throw updateError;

    // Also update the Supabase auth password
    const { error: authUpdateError } = await adminSupabase.auth.admin.updateUserById(
      adminProfile.id,
      { password: newPin }
    );

    if (authUpdateError) {
      console.error('Auth password update error:', authUpdateError);
      throw authUpdateError;
    }

    // Send email with the new PIN
    const transporter = nodemailer.createTransport({
      host: process.env.MAIL_HOST || 'smtp.gmail.com',
      port: parseInt(process.env.MAIL_PORT || '587'),
      secure: false,
      auth: {
        user: process.env.MAIL_USERNAME,
        pass: process.env.MAIL_PASSWORD,
      },
    });

    await transporter.sendMail({
      from: `"${process.env.MAIL_FROM_NAME || 'Dagat Ta Bai'}" <${process.env.MAIL_FROM_ADDRESS || process.env.MAIL_USERNAME}>`,
      to: adminEmail,
      subject: 'Admin PIN Reset — Dagat Ta Bai',
      html: `
        <div style="font-family: 'Segoe UI', Arial, sans-serif; max-width: 500px; margin: 0 auto; padding: 32px; background: #0f172a; border-radius: 16px; color: #e2e8f0;">
          <div style="text-align: center; margin-bottom: 24px;">
            <h1 style="color: #ffffff; font-size: 22px; margin: 0 0 8px 0;">Admin PIN Reset</h1>
            <p style="color: #94a3b8; font-size: 13px; margin: 0;">Dagat Ta Bai — Catmon Municipal Portal</p>
          </div>
          <div style="background: #1e293b; border-radius: 12px; padding: 24px; text-align: center; margin-bottom: 20px;">
            <p style="color: #94a3b8; font-size: 12px; margin: 0 0 12px 0; text-transform: uppercase; letter-spacing: 2px;">Your New Admin PIN</p>
            <p style="color: #38bdf8; font-size: 36px; font-weight: bold; letter-spacing: 10px; margin: 0; font-family: monospace;">${newPin}</p>
          </div>
          <div style="background: #1e293b; border: 1px solid #334155; border-radius: 10px; padding: 16px; margin-bottom: 20px;">
            <p style="color: #fbbf24; font-size: 12px; font-weight: bold; margin: 0 0 6px 0;">⚠️ Important</p>
            <p style="color: #94a3b8; font-size: 12px; margin: 0; line-height: 1.6;">
              This PIN is valid immediately. Please log in and change your PIN in the Admin Account settings as soon as possible. If you did not request this reset, please secure your account immediately.
            </p>
          </div>
          <p style="color: #475569; font-size: 11px; text-align: center; margin: 0;">
            This email was sent automatically by the Dagat Ta Bai system.
          </p>
        </div>
      `,
    });

    return NextResponse.json({ success: true });
  } catch (err: unknown) {
    const message = err instanceof Error ? err.message : 'Failed to process PIN reset.';
    console.error('Admin reset PIN error:', message);
    return NextResponse.json({ error: message }, { status: 500 });
  }
}
