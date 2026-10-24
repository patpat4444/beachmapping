'use client';

import React, { useState, useRef, useEffect } from 'react';
import { useRouter } from 'next/navigation';
import Image from 'next/image';
import { ShieldCheck, Loader2, Lock, Eye, EyeOff, KeyRound, ArrowLeft, CheckCircle2, AlertCircle, Mail } from 'lucide-react';
import { showToast } from '@/components/ui/feedback-toasts';
import { createClient } from '@/lib/supabase/client';

type View = 'login' | 'forgot' | 'forgot-sent';

export default function AdminLoginPage() {
  const router = useRouter();
  const username = 'Administrator';
  const [adminPassword, setAdminPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const [showPin, setShowPin] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [view, setView] = useState<View>('login');
  const [resetLoading, setResetLoading] = useState(false);
  const [resetEmail, setResetEmail] = useState('');
  const passwordInputRef = useRef<HTMLInputElement | null>(null);
  const submittingRef = useRef(false);

  useEffect(() => {
    // Clear any existing session when visiting login page
    const clearSession = async () => {
      try {
        const supabase = createClient();
        await supabase.auth.signOut();
      } catch (error) {
        console.error('Error clearing session:', error);
      }
    };

    clearSession();
    passwordInputRef.current?.focus();
  }, []);

  const pin = adminPassword;

  const loginWithPin = async (nextPin: string) => {
    if (submittingRef.current || nextPin.length !== 6) return;
    submittingRef.current = true;
    setLoading(true);
    setError(null);

    try {
      const res = await fetch('/api/auth/pin-login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          username,
          pin: nextPin,
          portal: 'admin',
        }),
      });

      const data = await res.json();

      if (!res.ok) {
        throw new Error(data.error || 'Invalid Admin PIN.');
      }

      showToast('Administrator signed in successfully.');
      // Force redirect
      window.location.replace('/admin/dashboard');
    } catch (err: unknown) {
      const msg = err instanceof Error ? err.message : 'Authentication failed';
      setError(msg);
      showToast(msg, 'error');
      submittingRef.current = false;
      setLoading(false);
      setAdminPassword('');
      passwordInputRef.current?.focus();
    }
  };

  const handleAdminLogin = async (e: React.FormEvent) => {
    e.preventDefault();
    if (pin.length !== 6) {
      setError('Please enter all 6 digits of your Administrator PIN.');
      showToast('Please enter all 6 digits of your Administrator password.', 'error');
      return;
    }
    await loginWithPin(pin);
  };

  const handlePinChange = (value: string) => {
    const next = value.replace(/\D/g, '').slice(0, 6);
    setAdminPassword(next);
    setError(null);
    if (next.length === 6) {
      void loginWithPin(next);
    }
  };

  const handleForgotSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setResetLoading(true);
    setError(null);

    try {
      const res = await fetch('/api/admin/reset-pin', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email: resetEmail }),
      });

      const data = await res.json();

      if (!res.ok) {
        throw new Error(data.error || 'Failed to send reset email.');
      }

      setView('forgot-sent');
    } catch (err: unknown) {
      const msg = err instanceof Error ? err.message : 'Failed to send reset email';
      setError(msg);
    } finally {
      setResetLoading(false);
    }
  };

  // PIN dots visualization
  const renderPinDots = () => (
    <div className="flex items-center justify-center gap-2.5 py-3">
      {Array.from({ length: 6 }).map((_, i) => (
        <div
          key={i}
          className={`w-3 h-3 rounded-full transition-all duration-300 ${
            i < pin.length
              ? 'bg-sky-400 shadow-[0_0_10px_rgba(56,189,248,0.5)] scale-110'
              : 'bg-slate-600/50 border border-slate-500/30'
          }`}
        />
      ))}
    </div>
  );

  return (
    <div className="min-h-screen flex items-center justify-center px-4 py-12 bg-slate-950 relative overflow-hidden">
      {/* Animated background elements */}
      <div className="absolute inset-0 overflow-hidden pointer-events-none">
        <div className="absolute -top-40 -right-40 w-80 h-80 bg-sky-500/10 rounded-full blur-3xl animate-pulse" />
        <div className="absolute -bottom-40 -left-40 w-96 h-96 bg-blue-600/8 rounded-full blur-3xl animate-pulse" style={{ animationDelay: '2s' }} />
        <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-sky-900/5 rounded-full blur-3xl" />
        {/* Grid pattern overlay */}
        <div
          className="absolute inset-0 opacity-[0.03]"
          style={{
            backgroundImage: `linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px)`,
            backgroundSize: '40px 40px',
          }}
        />
      </div>

      <div className="w-full max-w-md relative z-10">
        {/* Login View */}
        {view === 'login' && (
          <div
            className="bg-slate-900/60 backdrop-blur-2xl border border-slate-700/50 p-8 sm:p-10 rounded-2xl shadow-2xl shadow-black/30 space-y-7"
            style={{ animation: 'fadeInUp 0.5s ease-out' }}
          >
            {/* Header */}
            <div className="text-center space-y-3">
              <div className="w-16 h-16 rounded-2xl bg-gradient-to-br from-sky-500/20 to-blue-600/20 border border-sky-500/30 flex items-center justify-center mx-auto shadow-lg shadow-sky-500/10">
                <Image
                  src="/images/logo.png"
                  alt="Dagat Ta Bai"
                  width={40}
                  height={40}
                  className="object-contain"
                />
              </div>

              <div>
                <h1 className="text-2xl font-bold tracking-tight text-white">
                  Admin Portal
                </h1>
                <p className="text-xs text-slate-400 mt-1.5 max-w-xs mx-auto leading-relaxed">
                  Catmon Municipal Beach Management Console
                </p>
              </div>

              {/* Security badge */}
              <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-sky-500/10 border border-sky-500/20 text-sky-400">
                <ShieldCheck className="w-3 h-3" />
                <span className="text-[10px] font-semibold tracking-wider uppercase">Secured Access</span>
              </div>
            </div>

            {/* Error Message */}
            {error && (
              <div className="p-3 bg-red-500/10 border border-red-500/20 rounded-xl text-xs text-red-400 flex items-start gap-2 animate-shake">
                <AlertCircle className="w-4 h-4 flex-shrink-0 mt-0.5" />
                <span>{error}</span>
              </div>
            )}

            <form onSubmit={handleAdminLogin} className="space-y-5">
              {/* Fixed Username Field */}
              <div className="space-y-2">
                <label className="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                  Account
                </label>
                <div className="flex items-center gap-3 px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl">
                  <div className="w-8 h-8 rounded-lg bg-sky-500/15 border border-sky-500/20 flex items-center justify-center flex-shrink-0">
                    <span className="text-sky-400 font-bold text-xs">AD</span>
                  </div>
                  <div>
                    <span className="text-sm font-semibold text-white block">{username}</span>
                    <span className="text-[10px] text-slate-500">official.dagattabai@gmail.com</span>
                  </div>
                </div>
              </div>

              {/* Admin PIN */}
              <div className="space-y-2">
                <label htmlFor="admin-password" className="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                  6-Digit PIN
                </label>
                <div className="relative">
                  <Lock className="w-4 h-4 text-slate-500 absolute left-4 top-1/2 -translate-y-1/2" />
                  <input
                    id="admin-password"
                    ref={passwordInputRef}
                    type={showPin ? 'text' : 'password'}
                    inputMode="numeric"
                    autoComplete="current-password"
                    maxLength={6}
                    required
                    value={adminPassword}
                    onChange={(e) => handlePinChange(e.target.value)}
                    suppressHydrationWarning
                    placeholder="• • • • • •"
                    className="w-full pl-11 pr-12 py-3.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-sm font-semibold text-white tracking-[0.4em] text-center focus:outline-none focus:ring-2 focus:ring-sky-500/40 focus:border-sky-500/50 transition-all placeholder:text-slate-600 placeholder:tracking-[0.5em]"
                  />
                  <button
                    type="button"
                    onClick={() => setShowPin(!showPin)}
                    className="absolute right-3 top-1/2 -translate-y-1/2 p-1.5 text-slate-500 hover:text-slate-300 transition-colors rounded-md"
                    tabIndex={-1}
                  >
                    {showPin ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                  </button>
                </div>

                {/* PIN progress dots */}
                {renderPinDots()}
              </div>

              <button
                type="submit"
                disabled={loading || pin.length !== 6}
                className="w-full py-3.5 bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-400 hover:to-blue-500 disabled:opacity-30 disabled:cursor-not-allowed text-white font-bold text-sm tracking-wide rounded-xl transition-all shadow-lg shadow-sky-500/20 hover:shadow-sky-500/30 flex items-center justify-center gap-2 active:scale-[0.98]"
              >
                {loading ? (
                  <>
                    <Loader2 className="w-4 h-4 animate-spin" />
                    <span>Authenticating...</span>
                  </>
                ) : (
                  <>
                    <ShieldCheck className="w-4 h-4" />
                    <span>Access Dashboard</span>
                  </>
                )}
              </button>
            </form>

            {/* Forgot PIN */}
            <div className="text-center">
              <button
                type="button"
                onClick={() => { setView('forgot'); setError(null); setResetEmail(''); }}
                className="text-xs text-slate-500 hover:text-sky-400 transition-colors font-medium"
              >
                Forgot your PIN?
              </button>
            </div>

            {/* Footer */}
            <div className="pt-2 border-t border-slate-800/50 text-center">
              <p className="text-[10px] text-slate-600">
                Dagat Ta Bai — Catmon Beach Mapping Platform
              </p>
            </div>
          </div>
        )}

        {/* Forgot PIN View */}
        {view === 'forgot' && (
          <div
            className="bg-slate-900/60 backdrop-blur-2xl border border-slate-700/50 p-8 sm:p-10 rounded-2xl shadow-2xl shadow-black/30 space-y-6"
            style={{ animation: 'fadeInUp 0.5s ease-out' }}
          >
            <button
              type="button"
              onClick={() => { setView('login'); setError(null); }}
              className="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-sky-400 transition-colors"
            >
              <ArrowLeft className="w-3.5 h-3.5" />
              <span>Back to Login</span>
            </button>

            <div className="text-center space-y-3">
              <div className="w-14 h-14 rounded-2xl bg-amber-500/15 border border-amber-500/25 flex items-center justify-center mx-auto">
                <KeyRound className="w-7 h-7 text-amber-400" />
              </div>
              <h1 className="text-xl font-bold text-white">Reset Admin PIN</h1>
              <p className="text-xs text-slate-400 leading-relaxed max-w-xs mx-auto">
                Enter the administrator email address. A new temporary PIN will be sent to reset your access.
              </p>
            </div>

            {error && (
              <div className="p-3 bg-red-500/10 border border-red-500/20 rounded-xl text-xs text-red-400 flex items-start gap-2">
                <AlertCircle className="w-4 h-4 flex-shrink-0 mt-0.5" />
                <span>{error}</span>
              </div>
            )}

            <form onSubmit={handleForgotSubmit} className="space-y-4">
              <div className="space-y-2">
                <label className="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                  Admin Email
                </label>
                <div className="relative">
                  <Mail className="w-4 h-4 text-slate-500 absolute left-4 top-1/2 -translate-y-1/2" />
                  <input
                    type="email"
                    required
                    value={resetEmail}
                    onChange={(e) => setResetEmail(e.target.value)}
                    placeholder="official.dagattabai@gmail.com"
                    className="w-full pl-11 pr-4 py-3.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-sky-500/40 focus:border-sky-500/50 transition-all placeholder:text-slate-600"
                  />
                </div>
              </div>

              <button
                type="submit"
                disabled={resetLoading}
                className="w-full py-3.5 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 disabled:opacity-40 text-white font-bold text-sm rounded-xl transition-all shadow-lg shadow-amber-500/15 flex items-center justify-center gap-2"
              >
                {resetLoading ? (
                  <>
                    <Loader2 className="w-4 h-4 animate-spin" />
                    <span>Sending...</span>
                  </>
                ) : (
                  <span>Send Reset Email</span>
                )}
              </button>
            </form>
          </div>
        )}

        {/* Forgot PIN - Email Sent Confirmation */}
        {view === 'forgot-sent' && (
          <div
            className="bg-slate-900/60 backdrop-blur-2xl border border-slate-700/50 p-8 sm:p-10 rounded-2xl shadow-2xl shadow-black/30 space-y-6 text-center"
            style={{ animation: 'fadeInUp 0.5s ease-out' }}
          >
            <div className="w-16 h-16 bg-emerald-500/15 border border-emerald-500/25 text-emerald-400 rounded-full flex items-center justify-center mx-auto">
              <CheckCircle2 className="w-8 h-8" />
            </div>
            <div className="space-y-2">
              <h2 className="text-xl font-bold text-white">Reset Email Sent</h2>
              <p className="text-xs text-slate-400 leading-relaxed max-w-xs mx-auto">
                If <strong className="text-slate-300">{resetEmail}</strong> matches the administrator account, a new temporary PIN has been sent to that email.
              </p>
            </div>
            <button
              type="button"
              onClick={() => { setView('login'); setError(null); setResetEmail(''); }}
              className="w-full py-3 bg-slate-800 hover:bg-slate-700 text-white font-semibold text-sm rounded-xl transition-colors border border-slate-700/50"
            >
              Back to Login
            </button>
          </div>
        )}
      </div>

      {/* Inline CSS animations */}
      <style jsx>{`
        @keyframes fadeInUp {
          from {
            opacity: 0;
            transform: translateY(20px);
          }
          to {
            opacity: 1;
            transform: translateY(0);
          }
        }
        @keyframes shake {
          0%, 100% { transform: translateX(0); }
          25% { transform: translateX(-5px); }
          75% { transform: translateX(5px); }
        }
        .animate-shake {
          animation: shake 0.3s ease-in-out;
        }
      `}</style>
    </div>
  );
}
