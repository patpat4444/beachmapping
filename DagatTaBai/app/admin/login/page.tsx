'use client';

import React, { useState, useRef, useEffect } from 'react';
import { ShieldCheck, Loader2 } from 'lucide-react';
import { useRouter } from 'next/navigation';
import { createClient } from '@/lib/supabase/client';
import { showToast } from '@/components/ui/feedback-toasts';

export default function AdminLoginPage() {
  const router = useRouter();
  const [username, setUsername] = useState('Administrator');
  const [adminPassword, setAdminPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const passwordInputRef = useRef<HTMLInputElement | null>(null);

  useEffect(() => {
    passwordInputRef.current?.focus();
  }, []);

  const pin = adminPassword;

  const handleAdminLogin = async (e: React.FormEvent) => {
    e.preventDefault();
    if (pin.length !== 6) {
      showToast('Please enter all 6 digits of your Administrator password.', 'error');
      return;
    }
    setLoading(true);

    try {
      const res = await fetch('/api/auth/pin-login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          username,
          pin,
          portal: 'admin',
        }),
      });

      const data = await res.json();

      if (!res.ok) {
        throw new Error(data.error || 'Invalid Admin PIN.');
      }

      const supabase = createClient();
      const { error: signInError } = await supabase.auth.signInWithPassword({
        email: data.email,
        password: pin,
      });
      if (signInError) {
        throw new Error(signInError.message || 'Could not sign in to the admin account.');
      }

      showToast('Administrator signed in successfully.');
      router.push(data.redirect || '/admin/dashboard');
    } catch (err: unknown) {
      const msg = err instanceof Error ? err.message : 'Authentication failed';
      showToast(msg, 'error');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen flex items-center justify-center px-4 py-12 bg-slate-50 text-slate-900 font-sans">
      <div className="w-full max-w-md bg-white border border-slate-200 p-8 sm:p-10 rounded-2xl shadow-lg space-y-6">
        {/* Header */}
        <div className="text-center space-y-2">
          <div className="w-14 h-14 rounded-2xl bg-sky-50 border border-sky-100 flex items-center justify-center mx-auto text-sky-600 shadow-xs">
            <ShieldCheck className="w-7 h-7" />
          </div>

          <h1 className="text-2xl font-bold tracking-tight text-slate-900">
            Admin Portal
          </h1>
          <p className="text-xs text-slate-500 max-w-xs mx-auto">
            Enter your Administrator password to access the management console.
          </p>
        </div>

        <form onSubmit={handleAdminLogin} className="space-y-6">
          {/* Fixed Username Field */}
          <div className="space-y-2">
            <label className="block text-xs font-semibold text-slate-700">
              Username
            </label>
            <input
              type="text"
              value={username}
              disabled
              className="w-full px-4 py-3 bg-slate-100 border border-slate-300 rounded-xl text-sm font-semibold text-slate-600 cursor-not-allowed"
            />
          </div>

          {/* Admin password */}
          <div className="space-y-3">
            <label htmlFor="admin-password" className="block text-xs font-semibold text-slate-700">
              Password
            </label>
            <input
              id="admin-password"
              ref={passwordInputRef}
              type="password"
              inputMode="numeric"
              autoComplete="current-password"
              maxLength={6}
              required
              value={adminPassword}
              onChange={(e) => setAdminPassword(e.target.value.replace(/\D/g, '').slice(0, 6))}
              className="w-full px-4 py-3 bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-900 tracking-[0.35em] focus:outline-none focus:ring-2 focus:ring-sky-600/20 focus:border-sky-600"
            />
          </div>

          <button
            type="submit"
            disabled={loading || pin.length !== 6}
            className="w-full py-3 bg-slate-900 hover:bg-slate-800 disabled:opacity-40 disabled:cursor-not-allowed text-white font-bold text-sm tracking-wide rounded-xl transition-all shadow-sm flex items-center justify-center gap-2"
          >
            {loading ? (
              <>
                <Loader2 className="w-4 h-4 animate-spin" />
                <span>Logging in...</span>
              </>
            ) : (
              <span>Login</span>
            )}
          </button>
        </form>
      </div>
    </div>
  );
}
