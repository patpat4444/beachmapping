'use client';

import React, { useEffect, useState } from 'react';
import Link from 'next/link';
import { ArrowLeft, Compass, Eye, Ban, CheckCircle2 } from 'lucide-react';
import type { BeachRecord } from '@/lib/db/beaches';
import { showToast } from '@/components/ui/feedback-toasts';

export default function AdminBeachesPage() {
  const [beaches, setBeaches] = useState<BeachRecord[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetch('/api/admin/beaches', { cache: 'no-store' })
      .then(async (res) => {
        const data = await res.json();
        if (!res.ok) throw new Error(data.error || 'Failed to load beach listings.');
        if (Array.isArray(data)) setBeaches(data);
      })
      .catch((err: unknown) => showToast(err instanceof Error ? err.message : 'Failed to load beach listings.', 'error'))
      .finally(() => setLoading(false));
  }, []);

  const toggleBeachStatus = async (beach: BeachRecord) => {
    const status = beach.status === 'active' ? 'suspended' : 'active';
    try {
      const response = await fetch('/api/admin/beaches', {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: beach.id, status }),
      });
      const data = await response.json();
      if (!response.ok) throw new Error(data.error || 'Failed to update beach status.');
      setBeaches((current) => current.map((item) => item.id === beach.id ? { ...item, status: data.beach.status } : item));
      showToast(`Beach listing ${status === 'active' ? 'activated' : 'suspended'}.`);
    } catch (error: unknown) {
      showToast(error instanceof Error ? error.message : 'Failed to update beach status.', 'error');
    }
  };

  return (
    <div className="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">
      <Link
        href="/admin/dashboard"
        className="inline-flex items-center gap-1 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors"
      >
        <ArrowLeft className="w-3.5 h-3.5" />
        <span>Back to Admin Dashboard</span>
      </Link>

      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-slate-900">Beach Listings Moderation</h1>
          <p className="text-xs text-slate-500">
            View, moderate, and activate or suspend beach listings across Catmon.
          </p>
        </div>
      </div>

      <div className="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full text-left text-xs">
            <thead className="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider font-semibold">
              <tr>
                <th className="py-3.5 px-4">Beach Name & Location</th>
                <th className="py-3.5 px-4">Rating</th>
                <th className="py-3.5 px-4">Operating Hours</th>
                <th className="py-3.5 px-4">Status</th>
                <th className="py-3.5 px-4 text-right">Moderation Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100 text-slate-700">
              {!loading && beaches.length === 0 && <tr><td colSpan={5} className="py-12 text-center text-slate-500">No beach listings found.</td></tr>}
              {beaches.map((b) => (
                <tr key={b.id} className="hover:bg-slate-50 transition-colors">
                  <td className="py-3.5 px-4">
                    <span className="font-bold text-slate-900 block">{b.name}</span>
                    <span className="text-slate-400 text-[11px]">{b.location}</span>
                  </td>
                  <td className="py-3.5 px-4 font-semibold text-amber-600">
                    ★ {b.average_rating.toFixed(1)}
                  </td>
                  <td className="py-3.5 px-4 text-slate-600">{b.opening_hours}</td>
                  <td className="py-3.5 px-4">
                    <span
                      className={`px-2 py-0.5 rounded text-[11px] font-bold uppercase ${
                        b.status === 'active'
                          ? 'bg-emerald-100 text-emerald-800'
                          : 'bg-red-100 text-red-800'
                      }`}
                    >
                      {b.status}
                    </span>
                  </td>
                  <td className="py-3.5 px-4 text-right space-x-2">
                    <Link
                      href={`/beaches/${b.slug || b.id}`}
                      target="_blank"
                      className="inline-flex items-center gap-1 px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-md font-semibold text-xs transition-colors"
                    >
                      <Eye className="w-3.5 h-3.5" />
                      <span>View</span>
                    </Link>
                    <button
                      type="button"
                      onClick={() => void toggleBeachStatus(b)}
                      className={`inline-flex items-center gap-1 px-2.5 py-1.5 rounded-md font-semibold text-xs transition-colors ${
                        b.status === 'active'
                          ? 'bg-red-50 text-red-700 hover:bg-red-100'
                          : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100'
                      }`}
                    >
                      {b.status === 'active' ? (
                        <>
                          <Ban className="w-3.5 h-3.5" />
                          <span>Suspend</span>
                        </>
                      ) : (
                        <>
                          <CheckCircle2 className="w-3.5 h-3.5" />
                          <span>Activate</span>
                        </>
                      )}
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}
