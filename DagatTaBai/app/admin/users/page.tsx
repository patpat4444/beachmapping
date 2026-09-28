'use client';

import React, { useEffect, useState } from 'react';
import Link from 'next/link';
import { ArrowLeft, Loader2, UserCheck, UserX } from 'lucide-react';
import { showToast } from '@/components/ui/feedback-toasts';

interface AdminUser {
  id: string;
  name: string;
  email: string;
  role: string;
  status: 'active' | 'suspended';
  joined: string;
}

export default function AdminUsersPage() {
  const [users, setUsers] = useState<AdminUser[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetch('/api/admin/users', { cache: 'no-store' })
      .then(async (response) => {
        const data = await response.json();
        if (!response.ok) throw new Error(data.error || 'Failed to load users.');
        setUsers(Array.isArray(data.users) ? data.users : []);
      })
      .catch((error: unknown) => showToast(error instanceof Error ? error.message : 'Failed to load users.', 'error'))
      .finally(() => setLoading(false));
  }, []);

  const toggleStatus = async (user: AdminUser) => {
    const suspended = user.status === 'active';
    try {
      const response = await fetch('/api/admin/users', {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: user.id, suspended }),
      });
      const data = await response.json();
      if (!response.ok) throw new Error(data.error || 'Failed to update user status.');
      setUsers((current) => current.map((item) => item.id === user.id ? { ...item, status: data.status } : item));
      showToast(`User ${data.status === 'suspended' ? 'suspended' : 'reactivated'}.`);
    } catch (error: unknown) {
      showToast(error instanceof Error ? error.message : 'Failed to update user status.', 'error');
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
          <h1 className="text-2xl font-bold text-slate-900">User Management</h1>
          <p className="text-xs text-slate-500">
            View all registered users and moderate or suspend accounts.
          </p>
        </div>
      </div>

      <div className="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full text-left text-xs">
            <thead className="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider font-semibold">
              <tr>
                <th className="py-3.5 px-4">Name & Email</th>
                <th className="py-3.5 px-4">Role</th>
                <th className="py-3.5 px-4">Joined Date</th>
                <th className="py-3.5 px-4">Status</th>
                <th className="py-3.5 px-4 text-right">Moderation Action</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100 text-slate-700">
              {loading && <tr><td colSpan={5} className="py-12 text-center"><Loader2 className="mx-auto h-6 w-6 animate-spin" /></td></tr>}
              {!loading && users.length === 0 && <tr><td colSpan={5} className="py-12 text-center text-slate-500">No registered users found.</td></tr>}
              {users.map((u) => (
                <tr key={u.id} className="hover:bg-slate-50 transition-colors">
                  <td className="py-3.5 px-4">
                    <span className="font-bold text-slate-900 block">{u.name}</span>
                    <span className="text-slate-400 text-[11px]">{u.email}</span>
                  </td>
                  <td className="py-3.5 px-4">
                    <span className="px-2.5 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-800 uppercase">
                      {u.role}
                    </span>
                  </td>
                  <td className="py-3.5 px-4 text-slate-500">{new Date(u.joined).toLocaleDateString()}</td>
                  <td className="py-3.5 px-4">
                    <span
                      className={`px-2 py-0.5 rounded text-[11px] font-bold uppercase ${
                        u.status === 'active'
                          ? 'bg-emerald-100 text-emerald-800'
                          : 'bg-red-100 text-red-800'
                      }`}
                    >
                      {u.status}
                    </span>
                  </td>
                  <td className="py-3.5 px-4 text-right">
                    {u.role !== 'admin' && (
                      <button
                        type="button"
                        onClick={() => toggleStatus(u)}
                        className={`inline-flex items-center gap-1 px-3 py-1.5 rounded-md font-semibold text-xs transition-colors ${
                          u.status === 'active'
                            ? 'bg-red-50 text-red-700 hover:bg-red-100'
                            : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100'
                        }`}
                      >
                        {u.status === 'active' ? (
                          <>
                            <UserX className="w-3.5 h-3.5" />
                            <span>Suspend</span>
                          </>
                        ) : (
                          <>
                            <UserCheck className="w-3.5 h-3.5" />
                            <span>Reactivate</span>
                          </>
                        )}
                      </button>
                    )}
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
