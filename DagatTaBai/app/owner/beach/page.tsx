'use client';

import React, { useState, useEffect } from 'react';
import { showToast } from '@/components/ui/feedback-toasts';
import Link from 'next/link';
import Image from 'next/image';
import {
  ArrowLeft,
  Save,
  CheckCircle2,
  AlertCircle,
  Loader2,
  Camera,
  Eye,
  Plus,
  Trash2,
  Image as ImageIcon,
  Sparkles,
  ExternalLink,
  Clock,
  Coins,
  MapPin,
  Phone,
  Mail,
  ShieldCheck,
  Compass,
  Globe,
  MoreHorizontal,
  BadgeCheck,
  Tag,
  Smile,
  Edit,
  X,
  Send,
  SlidersHorizontal,
} from 'lucide-react';
import type { BeachRecord, BeachPhoto } from '@/lib/db/beaches';

const PRESET_COVERS = [
  {
    name: 'Sunset Shoreline',
    url: 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1600&q=85',
  },
  {
    name: 'Tropical Palms & Sand',
    url: 'https://images.unsplash.com/photo-1519046904884-53103b34b206?auto=format&fit=crop&w=1600&q=85',
  },
  {
    name: 'Crystal Turquoise Lagoon',
    url: 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1600&q=85',
  },
  {
    name: 'Catmon Coastal Horizon',
    url: 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=1600&q=85',
  },
];

const PRESET_PROFILES = [
  {
    name: 'Palm Resort Crest',
    url: 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=400&q=80',
  },
  {
    name: 'Blue Lagoon Emblem',
    url: 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=400&q=80',
  },
  {
    name: 'Ocean Sunset Badge',
    url: 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=400&q=80',
  },
];

export default function ManageBeachPage() {
  const [activeTab, setActiveTab] = useState<'posts' | 'about' | 'photos'>('posts');
  const [beachId, setBeachId] = useState<string | null>(null);
  const [beachSlug, setBeachSlug] = useState<string | null>(null);

  const [formData, setFormData] = useState({
    name: '',
    description: '',
    location: '',
    latitude: '10.6348',
    longitude: '124.0275',
    cover_image: '',
    profile_image: '',
    opening_hours: '',
    entrance_fee: '',
    cottage_fee: '',
    rules: '',
    contact_phone: '',
    contact_email: '',
  });

  const [images, setImages] = useState<BeachPhoto[]>([]);
  const [loading, setLoading] = useState(true);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [savedMessage, setSavedMessage] = useState<string | null>(null);
  const [errorMessage, setErrorMessage] = useState<string | null>(null);

  // Modals
  const [showCreatePostModal, setShowCreatePostModal] = useState(false);
  const [postText, setPostText] = useState('');
  const [postPhotoUrl, setPostPhotoUrl] = useState('');
  const [postCategory, setPostCategory] = useState('Shoreline');

  const [showCoverModal, setShowCoverModal] = useState(false);
  const [tempCoverInput, setTempCoverInput] = useState('');

  const [showProfileModal, setShowProfileModal] = useState(false);
  const [tempProfileInput, setTempProfileInput] = useState('');

  const [photoFilter, setPhotoFilter] = useState('All');

  useEffect(() => {
    fetch('/api/owner/beach', { cache: 'no-store' })
      .then((res) => res.json())
      .then((data: BeachRecord[]) => {
        if (Array.isArray(data) && data.length > 0) {
          const b = data[0];
          setBeachId(b.id);
          setBeachSlug(b.slug || b.id);
          setFormData({
            name: b.name || '',
            description: b.description || '',
            location: b.location || '',
            latitude: b.latitude?.toString() || '10.6348',
            longitude: b.longitude?.toString() || '124.0275',
            cover_image:
              b.cover_image || '',
            profile_image:
              b.profile_image || '',
            opening_hours: b.opening_hours || '',
            entrance_fee: b.entrance_fee || '',
            cottage_fee: b.cottage_fee || '',
            rules: b.rules || '',
            contact_phone: b.contact_phone || '',
            contact_email: b.contact_email || '',
          });
          if (Array.isArray(b.images)) {
            setImages(b.images);
          }
        }
      })
      .catch((err) => console.error('Failed to load beach info:', err))
      .finally(() => setLoading(false));
  }, []);

  // Save changes to DB
  const saveBeachData = async (updatedFields: Partial<typeof formData>, updatedImages?: BeachPhoto[]) => {
    setIsSubmitting(true);
    setErrorMessage(null);

    try {
      const payload = {
        ...formData,
        ...updatedFields,
        id: beachId,
        images: updatedImages !== undefined ? updatedImages : images,
      };

      const res = await fetch('/api/owner/beach', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(payload),
      });

      const data = await res.json();
      if (!res.ok) {
        throw new Error(data.error || 'Failed to save changes');
      }

      if (data.beach?.id) setBeachId(data.beach.id);
      if (data.beach?.slug) setBeachSlug(data.beach.slug);

      setSavedMessage('Page published & updated successfully!');
      showToast('Beach information saved successfully.');
      setTimeout(() => setSavedMessage(null), 4000);
    } catch (err: any) {
      const message = err.message || 'Failed to save beach information.';
      setErrorMessage(message);
      showToast(message, 'error');
    } finally {
      setIsSubmitting(false);
    }
  };

  // Handle Publish Post
  const handlePublishPost = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!postPhotoUrl.trim() && !postText.trim()) return;

    const newPost: BeachPhoto = {
      photo_url: postPhotoUrl.trim(),
      caption: postText.trim() || `${formData.name || 'Resort'} update`,
      category: postCategory,
      created_at: new Date().toISOString(),
    };

    const newImages = [newPost, ...images];
    setImages(newImages);
    setPostText('');
    setPostPhotoUrl('');
    setShowCreatePostModal(false);

    await saveBeachData({}, newImages);
  };

  // Handle Delete Post
  const handleDeletePost = async (indexToDelete: number) => {
    const updated = images.filter((_, idx) => idx !== indexToDelete);
    setImages(updated);
    await saveBeachData({}, updated);
  };

  if (loading) {
    return (
      <div className="py-28 flex flex-col items-center justify-center gap-3 text-slate-500 dark:text-slate-400">
        <Loader2 className="w-8 h-8 animate-spin text-[#0866FF]" />
        <p className="text-xs font-semibold">Loading Facebook Page in Meta Business Suite...</p>
      </div>
    );
  }

  const filteredPhotos =
    photoFilter === 'All'
      ? images
      : images.filter((img) => img.category.toLowerCase() === photoFilter.toLowerCase());

  return (
    <div className="max-w-6xl mx-auto space-y-5 pb-16">
      {/* ========================================================================= */}
      {/* META BUSINESS SUITE TOP ACTION BAR                                        */}
      {/* ========================================================================= */}
      <div className="bg-white dark:bg-slate-900 px-4 sm:px-6 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div className="flex items-center gap-3">
          <div className="w-8 h-8 rounded-lg bg-[#0866FF] text-white flex items-center justify-center font-black text-xs shadow-sm">
            f
          </div>
          <div>
            <div className="flex items-center gap-2">
              <h2 className="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">
                {formData.name || 'Your Beach Resort'}
              </h2>
              <span className="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/80 px-2 py-0.5 rounded-full border border-emerald-200 dark:border-emerald-800">
                <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" />
                <span>Page Published</span>
              </span>
            </div>
            <p className="text-[11px] text-slate-400">
              Facebook Page Management &bull; Meta Business Suite
            </p>
          </div>
        </div>

        <div className="flex items-center gap-2">
          <button
            type="button"
            onClick={() => setShowCreatePostModal(true)}
            className="inline-flex items-center gap-1.5 px-4 py-2 bg-[#0866FF] hover:bg-[#0866FF]/90 text-white text-xs font-bold rounded-xl transition-all shadow-sm shadow-[#0866FF]/20"
          >
            <Plus className="w-4 h-4" />
            <span>Create Post</span>
          </button>

          {beachSlug && (
            <Link
              href={`/beaches/${beachSlug}`}
              target="_blank"
              className="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl transition-colors border border-slate-200 dark:border-slate-700"
            >
              <Eye className="w-3.5 h-3.5" />
              <span>View as Visitor</span>
              <ExternalLink className="w-3 h-3 opacity-60 ml-0.5" />
            </Link>
          )}
        </div>
      </div>

      {/* Save / Error Feedback Alert */}
      {savedMessage && (
        <div className="p-4 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 rounded-2xl text-xs font-medium text-emerald-800 dark:text-emerald-300 flex items-center gap-2.5 shadow-xs">
          <CheckCircle2 className="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" />
          <span>{savedMessage}</span>
        </div>
      )}
      {errorMessage && (
        <div className="p-4 bg-red-50 dark:bg-red-950/50 border border-red-200 dark:border-red-800 rounded-2xl text-xs font-medium text-red-800 dark:text-red-300 flex items-center gap-2.5">
          <AlertCircle className="w-4 h-4 text-red-600 flex-shrink-0" />
          <span>{errorMessage}</span>
        </div>
      )}

      {/* ========================================================================= */}
      {/* 1. AUTHENTIC FACEBOOK PAGE HEADER & BANNER                                */}
      {/* ========================================================================= */}
      <div className="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        {/* Cover Photo Banner */}
        <div className="relative w-full h-56 sm:h-72 md:h-80 bg-slate-100 dark:bg-slate-800 overflow-hidden group">
          {formData.cover_image ? (
            <Image
              src={formData.cover_image}
              alt="Cover Photo"
              fill
              className="object-cover"
              priority
            />
          ) : (
            <div className="w-full h-full bg-gradient-to-r from-sky-600 to-indigo-600" />
          )}

          <div className="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent pointer-events-none" />

          {/* Edit Cover Photo Button (Facebook Style) */}
          <div className="absolute bottom-4 right-4 z-10">
            <button
              type="button"
              onClick={() => {
                setTempCoverInput(formData.cover_image);
                setShowCoverModal(true);
              }}
              className="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white/95 dark:bg-slate-900/95 hover:bg-white text-slate-800 dark:text-white text-xs font-bold shadow-md backdrop-blur-md border border-slate-200 dark:border-slate-700 transition-all hover:scale-105"
            >
              <Camera className="w-4 h-4 text-[#0866FF]" />
              <span>Edit cover photo</span>
            </button>
          </div>
        </div>

        {/* Page Identity & Profile Picture */}
        <div className="px-6 sm:px-8 pb-4 pt-0">
          <div className="flex flex-col sm:flex-row sm:items-end justify-between gap-4 -mt-16 sm:-mt-20">
            {/* Avatar & Title */}
            <div className="flex flex-col sm:flex-row items-center sm:items-end gap-4 text-center sm:text-left">
              {/* Profile Avatar */}
              <div className="relative group flex-shrink-0">
                <div className="w-32 h-32 sm:w-36 sm:h-36 rounded-full overflow-hidden ring-4 ring-white dark:ring-slate-900 bg-white dark:bg-slate-800 shadow-2xl relative">
                  {formData.profile_image ? (
                    <Image
                      src={formData.profile_image}
                      alt="Profile Avatar"
                      fill
                      className="object-cover"
                    />
                  ) : (
                    <div className="w-full h-full flex items-center justify-center text-slate-400">
                      <ImageIcon className="w-12 h-12" />
                    </div>
                  )}
                </div>

                {/* Camera icon button overlay on profile */}
                <button
                  type="button"
                  onClick={() => {
                    setTempProfileInput(formData.profile_image);
                    setShowProfileModal(true);
                  }}
                  className="absolute bottom-1 right-1 p-2 rounded-full bg-slate-900/90 text-white hover:bg-[#0866FF] ring-2 ring-white dark:ring-slate-900 shadow-lg transition-colors"
                  title="Update profile picture"
                >
                  <Camera className="w-4 h-4" />
                </button>
              </div>

              {/* Page Name & Category */}
              <div className="pb-1 sm:pb-3 space-y-1">
                <div className="flex items-center justify-center sm:justify-start gap-2">
                  <h1 className="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white font-heading">
                    {formData.name || 'Beach listing'}
                  </h1>
                  <BadgeCheck className="w-6 h-6 text-[#0866FF] fill-[#0866FF]/15" />
                </div>
                <p className="text-xs text-slate-500 dark:text-slate-400 font-medium">
                  {[formData.location, 'Beach listing'].filter(Boolean).join(' · ')}
                </p>
                <div className="flex items-center justify-center sm:justify-start gap-3 text-xs text-slate-500 dark:text-slate-400 font-semibold pt-0.5">
                  <span>{images.length} published photos</span>
                </div>
              </div>
            </div>

            {/* Header Right Action Buttons */}
            <div className="flex items-center justify-center sm:justify-end gap-2 pb-2">
              <button
                type="button"
                onClick={() => setShowCreatePostModal(true)}
                className="inline-flex items-center gap-2 px-4 py-2 bg-[#0866FF] hover:bg-[#0866FF]/90 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-[#0866FF]/20"
              >
                <Plus className="w-4 h-4" />
                <span>Create post</span>
              </button>

              <button
                type="button"
                onClick={() => setActiveTab('about')}
                className="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-800 dark:text-slate-200 text-xs font-bold rounded-xl transition-colors"
              >
                <Edit className="w-3.5 h-3.5" />
                <span>Edit details</span>
              </button>
            </div>
          </div>

          {/* Facebook Page Navigation Tabs */}
          <div className="flex items-center gap-2 border-t border-slate-200 dark:border-slate-800 mt-5 pt-1 overflow-x-auto">
            <button
              type="button"
              onClick={() => setActiveTab('posts')}
              className={`px-4 py-3 text-xs font-bold border-b-2 transition-all flex items-center gap-1.5 ${
                activeTab === 'posts'
                  ? 'border-[#0866FF] text-[#0866FF]'
                  : 'border-transparent text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'
              }`}
            >
              <span>Posts</span>
              <span className="text-[10px] px-1.5 py-0.2 rounded-full bg-slate-100 dark:bg-slate-800">
                {images.length}
              </span>
            </button>

            <button
              type="button"
              onClick={() => setActiveTab('about')}
              className={`px-4 py-3 text-xs font-bold border-b-2 transition-all flex items-center gap-1.5 ${
                activeTab === 'about'
                  ? 'border-[#0866FF] text-[#0866FF]'
                  : 'border-transparent text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'
              }`}
            >
              <span>About / Page Info</span>
            </button>

            <button
              type="button"
              onClick={() => setActiveTab('photos')}
              className={`px-4 py-3 text-xs font-bold border-b-2 transition-all flex items-center gap-1.5 ${
                activeTab === 'photos'
                  ? 'border-[#0866FF] text-[#0866FF]'
                  : 'border-transparent text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'
              }`}
            >
              <span>Photos</span>
              <span className="text-[10px] px-1.5 py-0.2 rounded-full bg-slate-100 dark:bg-slate-800">
                {images.length}
              </span>
            </button>

            <Link
              href="/owner/reviews"
              className="px-4 py-3 text-xs font-bold border-b-2 border-transparent text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-all flex items-center gap-1.5"
            >
              <span>Reviews &amp; Ratings</span>
              <ExternalLink className="w-3 h-3 opacity-60" />
            </Link>
          </div>
        </div>
      </div>

      {/* ========================================================================= */}
      {/* 2. TAB CONTENT                                                            */}
      {/* ========================================================================= */}

      {/* ----------------- TAB A: POSTS / THE FACEBOOK WALL ---------------------- */}
      {activeTab === 'posts' && (
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
          {/* LEFT COLUMN: INTRO / ABOUT SUMMARY & PHOTOS PREVIEW (Desktop 5/12 cols) */}
          <div className="lg:col-span-5 space-y-5">
            {/* Intro Card (FB Style) */}
            <div className="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
              <h3 className="font-bold text-sm text-slate-900 dark:text-white font-heading">
                Intro
              </h3>

              <p className="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                {formData.description ||
                  'Welcome to our verified coastal haven in Catmon, Cebu. Crystal shoreline, comfortable bamboo cottages, and family-friendly swimming.'}
              </p>

              <div className="space-y-3 pt-2 text-xs text-slate-700 dark:text-slate-300 border-t border-slate-100 dark:border-slate-800">
                <div className="flex items-start gap-2.5">
                  <MapPin className="w-4 h-4 text-slate-400 flex-shrink-0 mt-0.5" />
                  <span>{formData.location || 'Barangay Binongkalan, Catmon, Cebu'}</span>
                </div>

                <div className="flex items-center gap-2.5">
                  <Clock className="w-4 h-4 text-slate-400 flex-shrink-0" />
                  <span>
                    Open daily: <strong>{formData.opening_hours}</strong>
                  </span>
                </div>

                <div className="flex items-center gap-2.5">
                  <Coins className="w-4 h-4 text-slate-400 flex-shrink-0" />
                  <span>Entrance: {formData.entrance_fee || '₱50.00 / adult'}</span>
                </div>

                <div className="flex items-center gap-2.5">
                  <Phone className="w-4 h-4 text-slate-400 flex-shrink-0" />
                  <span>{formData.contact_phone || '+63 917 555 3829'}</span>
                </div>

                <div className="flex items-center gap-2.5">
                  <Mail className="w-4 h-4 text-slate-400 flex-shrink-0" />
                  <span className="truncate">{formData.contact_email || 'info@catmonbeach.ph'}</span>
                </div>
              </div>

              <button
                type="button"
                onClick={() => setActiveTab('about')}
                className="w-full py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-800 dark:text-slate-200 font-bold text-xs rounded-xl transition-colors"
              >
                Edit bio &amp; details
              </button>
            </div>

            {/* Photos Preview Card (FB Style 3x2 grid) */}
            <div className="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs space-y-3">
              <div className="flex items-center justify-between">
                <h3 className="font-bold text-sm text-slate-900 dark:text-white font-heading">
                  Photos
                </h3>
                <button
                  type="button"
                  onClick={() => setActiveTab('photos')}
                  className="text-xs font-semibold text-[#0866FF] hover:underline"
                >
                  See all photos ({images.length})
                </button>
              </div>

              {images.length === 0 ? (
                <p className="text-xs text-slate-400 py-3 text-center">
                  No photos uploaded yet. Post your first beach update!
                </p>
              ) : (
                <div className="grid grid-cols-3 gap-1.5 rounded-xl overflow-hidden">
                  {images.slice(0, 6).map((img, idx) => (
                    <div
                      key={idx}
                      className="relative aspect-square bg-slate-100 dark:bg-slate-800 overflow-hidden cursor-pointer hover:opacity-90 transition-opacity"
                      onClick={() => setActiveTab('photos')}
                    >
                      <Image
                        src={img.photo_url}
                        alt={img.caption || 'Beach photo'}
                        fill
                        className="object-cover"
                      />
                    </div>
                  ))}
                </div>
              )}
            </div>
          </div>

          {/* RIGHT COLUMN: CREATE POST & THE FACEBOOK WALL FEED (7/12 cols) */}
          <div className="lg:col-span-7 space-y-5">
            {/* FB COMPOSER: "What's on your mind?" */}
            <div className="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs space-y-3">
              <div className="flex items-center gap-3">
                <div className="w-10 h-10 rounded-full overflow-hidden ring-1 ring-slate-200 relative flex-shrink-0 bg-slate-100">
                  {formData.profile_image && (
                    <Image
                      src={formData.profile_image}
                      alt="Avatar"
                      fill
                      className="object-cover"
                    />
                  )}
                </div>

                <button
                  type="button"
                  onClick={() => setShowCreatePostModal(true)}
                  className="flex-1 bg-[#F0F2F5] dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700/80 text-left px-4 py-2.5 rounded-full text-xs text-slate-500 dark:text-slate-400 transition-colors"
                >
                  What&apos;s new at {formData.name || 'your resort'}? Post an update or photo...
                </button>
              </div>

              <div className="border-t border-slate-100 dark:border-slate-800 pt-3 flex items-center justify-between text-xs font-semibold text-slate-600 dark:text-slate-400">
                <button
                  type="button"
                  onClick={() => setShowCreatePostModal(true)}
                  className="flex-1 py-1.5 flex items-center justify-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-xl transition-colors"
                >
                  <ImageIcon className="w-4 h-4 text-emerald-500" />
                  <span>Photo/video</span>
                </button>

                <button
                  type="button"
                  onClick={() => setShowCreatePostModal(true)}
                  className="flex-1 py-1.5 flex items-center justify-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-xl transition-colors"
                >
                  <Tag className="w-4 h-4 text-sky-500" />
                  <span>Tag amenity</span>
                </button>

                <button
                  type="button"
                  onClick={() => setShowCreatePostModal(true)}
                  className="flex-1 py-1.5 flex items-center justify-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-xl transition-colors"
                >
                  <Smile className="w-4 h-4 text-amber-500" />
                  <span>Feeling/activity</span>
                </button>
              </div>
            </div>

            {/* FEED OF PUBLISHED FACEBOOK POSTS */}
            {images.length === 0 ? (
              <div className="bg-white dark:bg-slate-900 p-8 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs text-center space-y-4">
                <div className="w-12 h-12 rounded-full bg-[#0866FF]/10 text-[#0866FF] flex items-center justify-center mx-auto">
                  <Send className="w-6 h-6" />
                </div>
                <div>
                  <h4 className="font-bold text-sm text-slate-900 dark:text-white">
                    No posts on your page wall yet
                  </h4>
                  <p className="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
                    Post updates, shoreline photos, and cottage announcements. Tourists will see these on your verified listing!
                  </p>
                </div>

              </div>
            ) : (
              <div className="space-y-4">
                {images.map((post, idx) => {
                  return (
                    <div
                      key={idx}
                      className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden"
                    >
                      {/* Post Header */}
                      <div className="p-4 flex items-center justify-between">
                        <div className="flex items-center gap-3">
                          <div className="w-10 h-10 rounded-full overflow-hidden ring-1 ring-slate-200 relative flex-shrink-0">
                            {formData.profile_image && (
                              <Image
                                src={formData.profile_image}
                                alt="Profile"
                                fill
                                className="object-cover"
                              />
                            )}
                          </div>
                          <div>
                            <div className="flex items-center gap-1.5">
                              <span className="font-bold text-xs sm:text-sm text-slate-900 dark:text-white">
                                {formData.name || 'Beach listing'}
                              </span>
                              <BadgeCheck className="w-4 h-4 text-[#0866FF]" />
                            </div>
                            <div className="flex items-center gap-1 text-[11px] text-slate-400">
                              <span>{post.created_at ? new Date(post.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : 'Recently'}</span>
                              <span>&bull;</span>
                              <span className="inline-flex items-center gap-0.5">
                                <Globe className="w-3 h-3" />
                                <span>Public</span>
                              </span>
                              <span>&bull;</span>
                              <span className="text-[#0866FF] font-semibold">#{post.category}</span>
                            </div>
                          </div>
                        </div>

                        {/* Delete Post Button */}
                        <button
                          type="button"
                          onClick={() => handleDeletePost(idx)}
                          className="p-1.5 text-slate-400 hover:text-red-600 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                          title="Delete Post"
                        >
                          <Trash2 className="w-4 h-4" />
                        </button>
                      </div>

                      {/* Post Caption / Text */}
                      {post.caption && (
                        <div className="px-4 pb-3">
                          <p className="text-xs sm:text-sm text-slate-800 dark:text-slate-200 leading-relaxed whitespace-pre-line">
                            {post.caption}
                          </p>
                        </div>
                      )}

                      {/* Post Image (Full Bleed Edge-to-Edge like Facebook) */}
                      {post.photo_url && (
                        <div className="relative w-full aspect-16/10 sm:aspect-16/9 bg-slate-100 dark:bg-slate-800 overflow-hidden">
                          <Image
                            src={post.photo_url}
                            alt={post.caption || 'Post image'}
                            fill
                            className="object-cover"
                          />
                        </div>
                      )}

                    </div>
                  );
                })}
              </div>
            )}
          </div>
        </div>
      )}

      {/* ----------------- TAB B: ABOUT / RESORT DETAILS ------------------------- */}
      {activeTab === 'about' && (
        <div className="bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-6">
          <div className="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
            <div>
              <h3 className="text-lg font-bold text-slate-900 dark:text-white font-heading">
                Facebook Page Details &amp; Operational Rates
              </h3>
              <p className="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Update your official resort profile info visible to visitors and tourists.
              </p>
            </div>

            <button
              type="button"
              onClick={() => saveBeachData(formData)}
              disabled={isSubmitting}
              className="inline-flex items-center gap-2 px-5 py-2.5 bg-[#0866FF] hover:bg-[#0866FF]/90 text-white font-bold text-xs rounded-xl transition-all shadow-md shadow-[#0866FF]/20 disabled:opacity-50"
            >
              {isSubmitting ? <Loader2 className="w-4 h-4 animate-spin" /> : <Save className="w-4 h-4" />}
              <span>Save Page Changes</span>
            </button>
          </div>

          <form
            onSubmit={(e) => {
              e.preventDefault();
              saveBeachData(formData);
            }}
            className="space-y-4"
          >
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div className="sm:col-span-2">
                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                  Facebook Page / Resort Name *
                </label>
                <input
                  type="text"
                  required
                  value={formData.name}
                  onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                  className="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0866FF] text-slate-900 dark:text-white font-semibold"
                />
              </div>

              <div className="sm:col-span-2">
                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                  Location Address *
                </label>
                <input
                  type="text"
                  required
                  value={formData.location}
                  onChange={(e) => setFormData({ ...formData, location: e.target.value })}
                  className="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0866FF] text-slate-900 dark:text-white"
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                  Latitude
                </label>
                <input
                  type="number"
                  step="any"
                  value={formData.latitude}
                  onChange={(e) => setFormData({ ...formData, latitude: e.target.value })}
                  className="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0866FF] text-slate-900 dark:text-white"
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                  Longitude
                </label>
                <input
                  type="number"
                  step="any"
                  value={formData.longitude}
                  onChange={(e) => setFormData({ ...formData, longitude: e.target.value })}
                  className="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0866FF] text-slate-900 dark:text-white"
                />
              </div>

              <div className="sm:col-span-2">
                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                  Page Bio &amp; Shoreline Highlights
                </label>
                <textarea
                  rows={3}
                  value={formData.description}
                  onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                  className="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0866FF] text-slate-900 dark:text-white leading-relaxed"
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                  Daily Operating Hours
                </label>
                <input
                  type="text"
                  value={formData.opening_hours}
                  onChange={(e) => setFormData({ ...formData, opening_hours: e.target.value })}
                  className="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0866FF] text-slate-900 dark:text-white"
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                  Day Entrance Fee
                </label>
                <input
                  type="text"
                  value={formData.entrance_fee}
                  onChange={(e) => setFormData({ ...formData, entrance_fee: e.target.value })}
                  className="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0866FF] text-slate-900 dark:text-white"
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                  Cottage &amp; Table Rates
                </label>
                <input
                  type="text"
                  value={formData.cottage_fee}
                  onChange={(e) => setFormData({ ...formData, cottage_fee: e.target.value })}
                  className="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0866FF] text-slate-900 dark:text-white"
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                  Inquiry Phone Number
                </label>
                <input
                  type="text"
                  value={formData.contact_phone}
                  onChange={(e) => setFormData({ ...formData, contact_phone: e.target.value })}
                  className="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0866FF] text-slate-900 dark:text-white"
                />
              </div>
            </div>

            <div className="pt-3">
              <button
                type="submit"
                disabled={isSubmitting}
                className="inline-flex items-center gap-2 px-6 py-3 bg-[#0866FF] hover:bg-[#0866FF]/90 text-white font-bold text-xs rounded-xl transition-all shadow-md shadow-[#0866FF]/20"
              >
                {isSubmitting ? <Loader2 className="w-4 h-4 animate-spin" /> : <Save className="w-4 h-4" />}
                <span>Save All Changes</span>
              </button>
            </div>
          </form>
        </div>
      )}

      {/* ----------------- TAB C: PHOTOS GALLERY VIEW --------------------------- */}
      {activeTab === 'photos' && (
        <div className="bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-6">
          <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
              <h3 className="text-lg font-bold text-slate-900 dark:text-white font-heading">
                All Resort Photos &amp; Media ({images.length})
              </h3>
              <p className="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Every photo uploaded here appears in your photo gallery and on your public Facebook Page wall.
              </p>
            </div>

            <button
              type="button"
              onClick={() => setShowCreatePostModal(true)}
              className="inline-flex items-center gap-1.5 px-4 py-2 bg-[#0866FF] hover:bg-[#0866FF]/90 text-white text-xs font-bold rounded-xl transition-all shadow-sm"
            >
              <Plus className="w-4 h-4" />
              <span>Add Photos</span>
            </button>
          </div>

          {/* Category Filter Pills */}
          <div className="flex items-center gap-2 overflow-x-auto pb-1">
            {['All', 'Shoreline', 'Cottages', 'Sunset', 'Drone', 'Amenities'].map((cat) => (
              <button
                key={cat}
                type="button"
                onClick={() => setPhotoFilter(cat)}
                className={`px-3.5 py-1.5 rounded-full text-xs font-bold transition-colors ${
                  photoFilter === cat
                    ? 'bg-[#0866FF] text-white shadow-xs'
                    : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200'
                }`}
              >
                {cat}
              </button>
            ))}
          </div>

          {filteredPhotos.length === 0 ? (
            <div className="py-16 border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-2xl text-center space-y-2">
              <ImageIcon className="w-8 h-8 text-slate-400 mx-auto" />
              <p className="text-xs font-bold text-slate-700 dark:text-slate-300">
                No photos in this category
              </p>
              <button
                type="button"
                onClick={() => setShowCreatePostModal(true)}
                className="text-xs font-bold text-[#0866FF] hover:underline"
              >
                Upload your first photo
              </button>
            </div>
          ) : (
            <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
              {filteredPhotos.map((img, idx) => (
                <div
                  key={idx}
                  className="group relative rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 aspect-square shadow-xs"
                >
                  <Image
                    src={img.photo_url}
                    alt={img.caption || 'Resort Photo'}
                    fill
                    className="object-cover group-hover:scale-105 transition-transform duration-300"
                  />
                  <span className="absolute top-2 left-2 px-2 py-0.5 rounded-md text-[10px] font-bold bg-black/60 text-white backdrop-blur-xs">
                    {img.category}
                  </span>
                  <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-end p-2.5 text-white">
                    <p className="text-[11px] font-medium line-clamp-2">{img.caption}</p>
                    <button
                      type="button"
                      onClick={() => handleDeletePost(idx)}
                      className="self-end mt-1 p-1.5 rounded-lg bg-red-600 text-white hover:bg-red-700 transition-colors"
                      title="Delete photo"
                    >
                      <Trash2 className="w-3.5 h-3.5" />
                    </button>
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>
      )}

      {/* ========================================================================= */}
      {/* 3. FACEBOOK "CREATE POST" MODAL (Exact Facebook Style)                     */}
      {/* ========================================================================= */}
      {showCreatePostModal && (
        <div className="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4">
          <div className="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full overflow-hidden border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4">
            {/* Modal Header */}
            <div className="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
              <div className="w-6" />
              <h3 className="text-base font-bold text-slate-900 dark:text-white font-heading text-center">
                Create post
              </h3>
              <button
                type="button"
                onClick={() => setShowCreatePostModal(false)}
                className="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-600 dark:text-slate-300 flex items-center justify-center text-sm font-bold"
              >
                ✕
              </button>
            </div>

            {/* Modal Body */}
            <form onSubmit={handlePublishPost} className="px-6 pb-6 space-y-4">
              {/* Identity Bar */}
              <div className="flex items-center gap-3">
                <div className="w-10 h-10 rounded-full overflow-hidden ring-1 ring-slate-200 relative bg-slate-100">
                  {formData.profile_image && (
                    <Image
                      src={formData.profile_image}
                      alt="Avatar"
                      fill
                      className="object-cover"
                    />
                  )}
                </div>
                <div>
                  <div className="flex items-center gap-1.5">
                    <span className="font-bold text-xs sm:text-sm text-slate-900 dark:text-white">
                      {formData.name || 'Beach listing'}
                    </span>
                    <BadgeCheck className="w-4 h-4 text-[#0866FF]" />
                  </div>
                  <div className="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-[10px] font-bold text-slate-600 dark:text-slate-300 mt-0.5">
                    <Globe className="w-3 h-3" />
                    <span>Public</span>
                  </div>
                </div>
              </div>

              {/* Textarea */}
              <div>
                <textarea
                  rows={3}
                  required
                  placeholder={`What's on your mind, ${formData.name || 'resort operator'}?`}
                  value={postText}
                  onChange={(e) => setPostText(e.target.value)}
                  className="w-full text-xs sm:text-sm placeholder-slate-400 bg-transparent border-none focus:outline-none resize-none text-slate-900 dark:text-white leading-relaxed"
                />
              </div>

              {/* Photo Input Box */}
              <div className="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-3">
                <div className="flex items-center justify-between">
                  <span className="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                    <ImageIcon className="w-4 h-4 text-emerald-500" />
                    <span>Add Photo to Post</span>
                  </span>
                  <select
                    value={postCategory}
                    onChange={(e) => setPostCategory(e.target.value)}
                    className="text-[11px] font-bold bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg px-2 py-1 text-slate-800 dark:text-slate-200"
                  >
                    <option value="Shoreline">Shoreline</option>
                    <option value="Cottages">Cottages &amp; Cabanas</option>
                    <option value="Sunset">Sunset &amp; Twilight</option>
                    <option value="Drone">Drone &amp; Aerial</option>
                    <option value="Amenities">Amenities &amp; Dining</option>
                  </select>
                </div>

                <input
                  type="url"
                  placeholder="Paste direct image URL (https://...)"
                  value={postPhotoUrl}
                  onChange={(e) => setPostPhotoUrl(e.target.value)}
                  className="w-full px-3 py-2 text-xs bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0866FF] text-slate-900 dark:text-white"
                />

                <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                  Or upload a beach image (JPEG, PNG, WebP; up to 10 MB)
                  <input
                    type="file"
                    accept="image/jpeg,image/png,image/webp"
                    className="mt-2 block w-full text-xs"
                    onChange={async (event) => {
                      const file = event.target.files?.[0];
                      if (!file) return;
                      const upload = new FormData();
                      upload.set('image', file);
                      try {
                        const response = await fetch('/api/owner/beach/images', { method: 'POST', body: upload });
                        const result = await response.json();
                        if (!response.ok) throw new Error(result.error || 'Image upload failed.');
                        setPostPhotoUrl(result.url);
                        showToast('Image uploaded. Publish the post to save it to your beach profile.');
                      } catch (error: unknown) {
                        showToast(error instanceof Error ? error.message : 'Image upload failed.', 'error');
                      } finally {
                        event.target.value = '';
                      }
                    }}
                  />
                </label>

                {/* Quick Presets */}
                <div>
                  <span className="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">
                    Or pick from sample high-res beach photos:
                  </span>
                  <div className="grid grid-cols-3 gap-1.5">
                    {PRESET_COVERS.slice(0, 3).map((p, idx) => (
                      <button
                        key={idx}
                        type="button"
                        onClick={() => setPostPhotoUrl(p.url)}
                        className="relative h-14 rounded-lg overflow-hidden border border-slate-200 dark:border-slate-700 text-left"
                      >
                        <Image
                          src={p.url}
                          alt={p.name}
                          fill
                          className="object-cover"
                        />
                        <span className="absolute inset-0 bg-black/40 flex items-end p-1 text-[9px] font-bold text-white leading-none">
                          {p.name}
                        </span>
                      </button>
                    ))}
                  </div>
                </div>
              </div>

              {/* Submit Button */}
              <button
                type="submit"
                className="w-full py-2.5 bg-[#0866FF] hover:bg-[#0866FF]/90 text-white font-bold text-xs rounded-xl transition-all shadow-md shadow-[#0866FF]/20"
              >
                Post to Page Feed
              </button>
            </form>
          </div>
        </div>
      )}

      {/* ========================================================================= */}
      {/* COVER PHOTO MODAL                                                         */}
      {/* ========================================================================= */}
      {showCoverModal && (
        <div className="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4">
          <div className="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full p-6 space-y-5 border border-slate-200 dark:border-slate-800 shadow-2xl">
            <div className="flex items-center justify-between">
              <h3 className="text-base font-bold text-slate-900 dark:text-white font-heading flex items-center gap-2">
                <Camera className="w-4 h-4 text-[#0866FF]" />
                <span>Update Facebook Cover Photo</span>
              </h3>
              <button
                type="button"
                onClick={() => setShowCoverModal(false)}
                className="text-slate-400 hover:text-slate-700 text-xs font-bold"
              >
                ✕
              </button>
            </div>

            <div>
              <label className="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1.5">
                Cover Photo URL
              </label>
              <input
                type="url"
                value={tempCoverInput}
                onChange={(e) => setTempCoverInput(e.target.value)}
                placeholder="https://..."
                className="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0866FF] text-slate-900 dark:text-white"
              />
            </div>

            <div>
              <span className="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-2">
                Or pick from curated Catmon coastal presets:
              </span>
              <div className="grid grid-cols-2 gap-2">
                {PRESET_COVERS.map((preset, idx) => (
                  <button
                    key={idx}
                    type="button"
                    onClick={() => setTempCoverInput(preset.url)}
                    className="group relative h-20 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 text-left"
                  >
                    <Image
                      src={preset.url}
                      alt={preset.name}
                      fill
                      className="object-cover group-hover:scale-105 transition-transform"
                    />
                    <span className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent flex items-end p-1.5 text-[10px] font-bold text-white">
                      {preset.name}
                    </span>
                  </button>
                ))}
              </div>
            </div>

            <div className="flex justify-end gap-2 pt-2">
              <button
                type="button"
                onClick={() => setShowCoverModal(false)}
                className="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 rounded-xl"
              >
                Cancel
              </button>
              <button
                type="button"
                onClick={() => {
                  setFormData({ ...formData, cover_image: tempCoverInput });
                  setShowCoverModal(false);
                  saveBeachData({ cover_image: tempCoverInput });
                }}
                className="px-5 py-2 text-xs font-bold bg-[#0866FF] hover:bg-[#0866FF]/90 text-white rounded-xl shadow-xs"
              >
                Apply &amp; Save Cover
              </button>
            </div>
          </div>
        </div>
      )}

      {/* ========================================================================= */}
      {/* PROFILE AVATAR MODAL                                                      */}
      {/* ========================================================================= */}
      {showProfileModal && (
        <div className="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4">
          <div className="bg-white dark:bg-slate-900 rounded-3xl max-w-md w-full p-6 space-y-5 border border-slate-200 dark:border-slate-800 shadow-2xl">
            <div className="flex items-center justify-between">
              <h3 className="text-base font-bold text-slate-900 dark:text-white font-heading flex items-center gap-2">
                <Camera className="w-4 h-4 text-[#0866FF]" />
                <span>Update Facebook Page Profile Picture</span>
              </h3>
              <button
                type="button"
                onClick={() => setShowProfileModal(false)}
                className="text-slate-400 hover:text-slate-700 text-xs font-bold"
              >
                ✕
              </button>
            </div>

            <div>
              <label className="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1.5">
                Profile Avatar URL
              </label>
              <input
                type="url"
                value={tempProfileInput}
                onChange={(e) => setTempProfileInput(e.target.value)}
                placeholder="https://..."
                className="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0866FF] text-slate-900 dark:text-white"
              />
            </div>

            <div>
              <span className="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-2">
                Or pick a resort emblem preset:
              </span>
              <div className="grid grid-cols-3 gap-2">
                {PRESET_PROFILES.map((preset, idx) => (
                  <button
                    key={idx}
                    type="button"
                    onClick={() => setTempProfileInput(preset.url)}
                    className="group relative aspect-square rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 text-left"
                  >
                    <Image
                      src={preset.url}
                      alt={preset.name}
                      fill
                      className="object-cover group-hover:scale-105 transition-transform"
                    />
                    <span className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent flex items-end p-1.5 text-[9px] font-bold text-white text-center w-full">
                      {preset.name}
                    </span>
                  </button>
                ))}
              </div>
            </div>

            <div className="flex justify-end gap-2 pt-2">
              <button
                type="button"
                onClick={() => setShowProfileModal(false)}
                className="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 rounded-xl"
              >
                Cancel
              </button>
              <button
                type="button"
                onClick={() => {
                  setFormData({ ...formData, profile_image: tempProfileInput });
                  setShowProfileModal(false);
                  saveBeachData({ profile_image: tempProfileInput });
                }}
                className="px-5 py-2 text-xs font-bold bg-[#0866FF] hover:bg-[#0866FF]/90 text-white rounded-xl shadow-xs"
              >
                Apply &amp; Save Avatar
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
