'use client';

import React, { useState, useEffect, use } from 'react';
import Link from 'next/link';
import { useRouter } from 'next/navigation';
import { showToast } from '@/components/ui/feedback-toasts';
import {
  ArrowLeft,
  FileText,
  CheckCircle2,
  XCircle,
  Download,
  AlertCircle,
  Loader2,
  Lock,
  Mail,
  Building,
  Phone,
} from 'lucide-react';

interface ApplicationData {
  id: string;
  applicant_full_name?: string;
  applicant_name?: string;
  applicant_email: string;
  business_name: string;
  beach_name: string;
  contact_phone?: string;
  contact_info?: string;
  business_permit_path?: string;
  business_permit_url?: string | null;
  proof_of_ownership_path?: string;
  proof_of_ownership_url?: string | null;
  ownership_proof_path?: string;
  status: 'pending' | 'approved' | 'rejected';
  rejection_reason?: string;
  created_at: string;
}

export default function AdminApplicationDetailPage({
  params,
}: {
  params: Promise<{ id: string }>;
}) {
  const resolvedParams = use(params);
  const router = useRouter();

  const [application, setApplication] = useState<ApplicationData | null>(null);
  const [status, setStatus] = useState<'pending' | 'approved' | 'rejected'>('pending');
  const [adminNotes, setAdminNotes] = useState('');
  const [actionSuccess, setActionSuccess] = useState<string | null>(null);
  const [actionError, setActionError] = useState<string | null>(null);
  const [documentMessage, setDocumentMessage] = useState<string | null>(null);
  const [loading, setLoading] = useState(true);
  const [submitting, setSubmitting] = useState(false);

  const openDocument = (path?: string, label = 'document') => {
    if (!path) {
      setDocumentMessage(`No uploaded ${label} is available yet.`);
      return;
    }

    const normalized = path.trim();
    const isRemote = /^https?:\/\//i.test(normalized);
    const isLocalPath = /^\/?(uploads|files|storage|permits|titles|public)\//i.test(normalized);

    if (isRemote || isLocalPath) {
      window.open(normalized, '_blank', 'noopener,noreferrer');
      setDocumentMessage(null);
      return;
    }

    setDocumentMessage(`The ${label} is not yet linked to a viewable file in storage.`);
  };

  useEffect(() => {
    const fetchDetail = async () => {
      setLoading(true);
      try {
        const res = await fetch(`/api/admin/applications/${resolvedParams.id}`, { cache: 'no-store' });
        const data = await res.json();
        if (res.ok && data.application) {
          setApplication(data.application);
          setStatus(data.application.status || 'pending');
          if (data.application.rejection_reason) {
            setAdminNotes(data.application.rejection_reason);
          }
        }
      } catch (err) {
        console.error('Failed to load application detail:', err);
      } finally {
        setLoading(false);
      }
    };

    fetchDetail();
  }, [resolvedParams.id]);

  const handleApprove = async () => {
    if (!application) return;
    setSubmitting(true);
    setActionError(null);
    setActionSuccess(null);

    try {
      const res = await fetch(`/api/admin/applications/${resolvedParams.id}/review`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          action: 'approve',
          applicant_email: application.applicant_email,
          applicant_name: application.applicant_full_name || application.applicant_name || 'Beach Owner',
        }),
      });

      const data = await res.json();

      if (!res.ok) {
        throw new Error(data.error || 'Failed to approve application');
      }

      setStatus('approved');
      const successMessage = status === 'approved'
        ? `A new randomized 6-digit PIN was generated and emailed to ${application.applicant_email}.`
        : `Application approved. A randomized 6-digit PIN was generated and emailed to ${application.applicant_email}.`;
      setActionSuccess(successMessage);
      showToast(successMessage);
    } catch (err: unknown) {
      const msg = err instanceof Error ? err.message : 'Approval failed';
      setActionError(msg);
      showToast(msg, 'error');
    } finally {
      setSubmitting(false);
    }
  };

  const handleReject = async () => {
    if (!application) return;
    setSubmitting(true);
    setActionError(null);
    setActionSuccess(null);

    try {
      const res = await fetch(`/api/admin/applications/${resolvedParams.id}/review`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          action: 'reject',
          applicant_email: application.applicant_email,
          applicant_name: application.applicant_full_name || application.applicant_name || 'Beach Owner',
          rejection_reason: adminNotes || 'Incomplete business permits or permit mismatch.',
        }),
      });

      const data = await res.json();

      if (!res.ok) {
        throw new Error(data.error || 'Failed to reject application');
      }

      setStatus('rejected');
      setActionSuccess('Application marked as rejected with recorded administrator notes.');
      showToast('Application rejected.');
    } catch (err: unknown) {
      const msg = err instanceof Error ? err.message : 'Rejection failed';
      setActionError(msg);
      showToast(msg, 'error');
    } finally {
      setSubmitting(false);
    }
  };

  if (loading) {
    return (
      <div className="py-24 text-center">
        <Loader2 className="w-8 h-8 animate-spin mx-auto text-sky-600 mb-3" />
        <p className="text-xs text-slate-500 font-medium">Loading application details...</p>
      </div>
    );
  }

  if (!application) {
    return (
      <div className="py-12 max-w-xl mx-auto text-center space-y-4">
        <AlertCircle className="w-10 h-10 text-amber-500 mx-auto" />
        <h1 className="text-lg font-bold text-slate-800">Application Not Found</h1>
        <p className="text-xs text-slate-500">
          The requested application ID does not exist or may have been deleted.
        </p>
        <Link
          href="/admin/applications"
          className="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-900 text-white rounded-md text-xs font-semibold"
        >
          <ArrowLeft className="w-3.5 h-3.5" />
          <span>Back to Applications</span>
        </Link>
      </div>
    );
  }

  return (
    <div className="py-8 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto space-y-6">
      <Link
        href="/admin/applications"
        className="inline-flex items-center gap-1 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors"
      >
        <ArrowLeft className="w-3.5 h-3.5" />
        <span>Back to Applications List</span>
      </Link>

      <div className="bg-white p-6 sm:p-8 rounded-xl border border-slate-200 shadow-sm space-y-6">
        <div className="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-4">
          <div>
            <h1 className="text-xl font-bold text-slate-900">
              Review Application: {application.beach_name}
            </h1>
            <p className="text-xs text-slate-500">
              Submitted on {new Date(application.created_at).toLocaleString()}
            </p>
          </div>
          <div>
            <span
              className={`px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider ${
                status === 'approved'
                  ? 'bg-emerald-100 text-emerald-800'
                  : status === 'rejected'
                  ? 'bg-red-100 text-red-800'
                  : 'bg-amber-100 text-amber-800'
              }`}
            >
              Status: {status}
            </span>
          </div>
        </div>

        {actionSuccess && (
          <div className="p-4 bg-emerald-50 border border-emerald-200 rounded-lg text-xs text-emerald-800 flex items-start gap-2.5">
            <CheckCircle2 className="w-5 h-5 flex-shrink-0 mt-0.5 text-emerald-600" />
            <div>
              <p className="font-bold">Workflow Executed Successfully</p>
              <p className="mt-0.5 leading-relaxed">{actionSuccess}</p>
            </div>
          </div>
        )}

        {actionError && (
          <div className="p-4 bg-red-50 border border-red-200 rounded-lg text-xs text-red-800 flex items-start gap-2.5">
            <AlertCircle className="w-5 h-5 flex-shrink-0 mt-0.5 text-red-600" />
            <div>
              <p className="font-bold">Action Failed</p>
              <p className="mt-0.5 leading-relaxed">{actionError}</p>
            </div>
          </div>
        )}

        {/* Applicant Details */}
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs text-slate-700 bg-slate-50 p-4 rounded-lg border border-slate-200">
          <div>
            <span className="text-slate-400 font-medium block">Applicant Name</span>
            <span className="font-bold text-slate-900">
              {application.applicant_full_name || application.applicant_name || 'Not provided'}
            </span>
          </div>

          <div>
            <span className="text-slate-400 font-medium block">Applicant Email</span>
            <span className="font-bold text-slate-900">{application.applicant_email}</span>
          </div>

          <div>
            <span className="text-slate-400 font-medium block">Business / Resort Name</span>
            <span className="font-semibold text-slate-900">{application.business_name}</span>
          </div>

          <div>
            <span className="text-slate-400 font-medium block">Contact Phone</span>
            <span className="font-semibold text-slate-900">
              {application.contact_phone || application.contact_info || 'Not provided'}
            </span>
          </div>
        </div>

        {/* Uploaded Documents */}
        <div className="space-y-3">
          <h2 className="text-sm font-bold text-slate-900">
            Legal Ownership &amp; Business Verification Documents
          </h2>

          {documentMessage && (
            <div className="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-[11px] text-amber-800">
              {documentMessage}
            </div>
          )}

          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div className="p-4 bg-white border border-slate-200 rounded-lg flex items-center justify-between gap-2 shadow-xs">
              <div className="flex items-center gap-2.5 min-w-0">
                <div className="w-10 h-10 rounded-lg bg-sky-50 text-sky-700 flex items-center justify-center flex-shrink-0">
                  <FileText className="w-5 h-5" />
                </div>
                <div className="min-w-0">
                  <h3 className="text-sm font-bold text-slate-900 truncate">Mayor&apos;s Business Permit</h3>
                  <p className="text-[11px] text-slate-500">Verified municipality stamp</p>
                </div>
              </div>
              <button
                type="button"
                onClick={() => openDocument(application.business_permit_url || application.business_permit_path, 'business permit')}
                className="p-2 text-sky-700 bg-sky-50 hover:bg-sky-100 rounded-md transition-colors"
                title="Open permit document"
                aria-label="Open Mayor's Business Permit"
              >
                <Download className="w-4 h-4" />
              </button>
            </div>

            <div className="p-4 bg-white border border-slate-200 rounded-lg flex items-center justify-between gap-2 shadow-xs">
              <div className="flex items-center gap-2.5 min-w-0">
                <div className="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center flex-shrink-0">
                  <FileText className="w-5 h-5" />
                </div>
                <div className="min-w-0">
                  <h3 className="text-sm font-bold text-slate-900 truncate">Proof of Ownership / Lease</h3>
                  <p className="text-[11px] text-slate-500">Land title &amp; contract</p>
                </div>
              </div>
              <button
                type="button"
                onClick={() => openDocument(application.proof_of_ownership_url || application.proof_of_ownership_path || application.ownership_proof_path, 'ownership proof')}
                className="p-2 text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-md transition-colors"
                title="Open ownership proof"
                aria-label="Open Proof of Ownership Document"
              >
                <Download className="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>

        {/* Administrator Notes */}
        <div>
          <label className="block text-xs font-semibold text-slate-700 mb-1">
            Administrator Review Notes / Decision Reason
          </label>
          <textarea
            rows={3}
            value={adminNotes}
            onChange={(e) => setAdminNotes(e.target.value)}
            disabled={status !== 'pending'}
            placeholder="Add review notes, reasons for decision, or document validation details..."
            className="w-full px-3 py-2 text-sm border border-slate-200 rounded-md focus:outline-none focus:ring-2 focus:ring-sky-500 disabled:bg-slate-50 disabled:text-slate-500"
          />
        </div>

        {/* Action Buttons */}
        {(status === 'pending' || status === 'approved') && (
          <div className="flex flex-col sm:flex-row items-center justify-end gap-3 pt-4 border-t border-slate-100">
            {status === 'pending' && (
              <button
                type="button"
                disabled={submitting}
                onClick={handleReject}
                className="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-5 py-2.5 border border-red-200 bg-red-50 hover:bg-red-100 text-red-700 font-semibold text-xs rounded-md transition-colors"
              >
                <XCircle className="w-4 h-4" />
                <span>Reject Application</span>
              </button>
            )}

            <button
              type="button"
              disabled={submitting}
              onClick={handleApprove}
              className="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white font-semibold text-xs rounded-md transition-colors shadow-sm"
            >
              {submitting ? (
                <>
                  <Loader2 className="w-4 h-4 animate-spin" />
                  <span>Generating and emailing PIN...</span>
                </>
              ) : (
                <>
                  <CheckCircle2 className="w-4 h-4" />
                  <span>{status === 'approved' ? 'Regenerate & Email PIN' : 'Approve & Dispatch 6-Digit PIN'}</span>
                </>
              )}
            </button>
          </div>
        )}
      </div>
    </div>
  );
}
