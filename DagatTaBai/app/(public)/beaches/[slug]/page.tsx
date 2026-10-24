'use client';

import React, { useEffect, useState, use } from 'react';
import Image from 'next/image';
import Link from 'next/link';
import {
  Clock,
  MapPin,
  Star,
  ArrowLeft,
  Phone,
  ExternalLink,
  ChevronRight,
  Images,
  Home,
  Info,
  ShieldCheck,
  Activity,
  Coffee,
  MessageSquare,
} from 'lucide-react';
import type { BeachRecord } from '@/lib/db/beaches';

export default function BeachProfilePage({
  params,
}: {
  params: Promise<{ slug: string }>;
}) {
  const resolvedParams = use(params);
  const slug = resolvedParams.slug;

  const [beach, setBeach] = useState<BeachRecord | null>(null);
  const [reviewCount, setReviewCount] = useState(0);
  const [loading, setLoading] = useState(true);
  const [notFound, setNotFound] = useState(false);
  const [activeTab, setActiveTab] = useState('overview');

  useEffect(() => {
    setLoading(true);
    fetch(`/api/beaches/${encodeURIComponent(slug)}`, { cache: 'no-store' })
      .then((res) => {
        if (!res.ok) throw new Error('Beach not found');
        return res.json();
      })
      .then((data) => {
        setBeach(data);
        setReviewCount(Number(data.review_count) || 0);
      })
      .catch(() => {
        setNotFound(true);
      })
      .finally(() => {
        setLoading(false);
      });
  }, [slug]);

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
      </div>
    );
  }

  if (notFound || !beach) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <p>Beach not found</p>
      </div>
    );
  }

  const tabs = [
    { id: 'overview', label: 'Overview', icon: Info },
    { id: 'general', label: 'General Info', icon: Home },
    { id: 'cottages', label: 'Cottages', icon: Home },
    { id: 'facilities', label: 'Facilities', icon: ShieldCheck },
    { id: 'activities', label: 'Activities', icon: Activity },
    { id: 'dos', label: "Do's & Don'ts", icon: ShieldCheck },
    { id: 'reviews', label: 'Reviews', icon: MessageSquare },
  ];

  return (
    <div className="min-h-screen bg-white dark:bg-gray-900">
      {/* Header */}
      <header className="border-b border-gray-200 dark:border-gray-800">
        <div className="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
          <Link href="/beaches" className="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
            <ArrowLeft size={16} />
            <span>Back to Beaches</span>
          </Link>
          <div className="flex items-center gap-2">
            <button className="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800">
              <MapPin size={20} className="text-gray-600 dark:text-gray-400" />
            </button>
            <button className="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800">
              <Star size={20} className="text-gray-600 dark:text-gray-400" />
            </button>
          </div>
        </div>
      </header>

      {/* Hero Section */}
      <div className="max-w-7xl mx-auto px-4 py-6">
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
          {/* Left: Beach Info */}
          <div className="space-y-6">
            <div>
              <p className="text-sm text-gray-500 dark:text-gray-400 mb-2">{beach.location}</p>
              <h1 className="text-3xl font-bold text-gray-900 dark:text-white mb-3">{beach.name}</h1>
              <p className="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-3">{beach.description}</p>
              {beach.virtual_tour_url && (
                <button className="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                  Explore 360° Tour
                </button>
              )}
            </div>

            {/* Info Cards */}
            <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
              <div className="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <div className="flex items-center gap-1 mb-1">
                  <Star size={16} className="text-yellow-500 fill-yellow-500" />
                  <span className="text-lg font-bold text-gray-900 dark:text-white">{beach.average_rating.toFixed(1)}</span>
                </div>
                <p className="text-xs text-gray-500 dark:text-gray-400">{reviewCount} reviews</p>
              </div>
              <div className="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <p className="text-lg font-bold text-gray-900 dark:text-white mb-1">{beach.entrance_fee || 'Not listed'}</p>
                <p className="text-xs text-gray-500 dark:text-gray-400">Entrance Fee</p>
              </div>
              <div className="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <p className="text-lg font-bold text-gray-900 dark:text-white mb-1">{beach.opening_hours}</p>
                <p className="text-xs text-gray-500 dark:text-gray-400">Today</p>
              </div>
              <div className="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <p className="text-sm font-bold text-gray-900 dark:text-white mb-1">{beach.location}</p>
                <p className="text-xs text-gray-500 dark:text-gray-400">Location</p>
              </div>
            </div>
          </div>

          {/* Right: Image Gallery */}
          <div className="relative h-64 lg:h-80 rounded-2xl overflow-hidden bg-gray-200 dark:bg-gray-800">
            {beach.cover_image ? (
              <Image
                src={beach.cover_image}
                alt={beach.name}
                fill
                className="object-cover"
              />
            ) : (
              <div className="w-full h-full flex items-center justify-center text-gray-400">
                <Images size={48} />
              </div>
            )}
            <div className="absolute bottom-4 right-4 bg-black/50 text-white px-3 py-1 rounded-full text-xs">
              1/6
            </div>
          </div>
        </div>
      </div>

      {/* Navigation Tabs */}
      <div className="max-w-7xl mx-auto px-4 py-4 border-b border-gray-200 dark:border-gray-800">
        <div className="flex gap-2 overflow-x-auto pb-2 scrollbar-hide">
          {tabs.map((tab) => {
            const Icon = tab.icon;
            return (
              <button
                key={tab.id}
                onClick={() => setActiveTab(tab.id)}
                className={`flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap transition-colors ${
                  activeTab === tab.id
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'
                }`}
              >
                <Icon size={16} />
                <span>{tab.label}</span>
              </button>
            );
          })}
        </div>
      </div>

      {/* Main Content */}
      <div className="max-w-7xl mx-auto px-4 py-8">
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {/* Left: Main Content */}
          <div className="lg:col-span-2 space-y-8">
            {/* Overview Section */}
            {activeTab === 'overview' && (
              <div className="space-y-6">
                <div>
                  <h2 className="text-xl font-bold text-gray-900 dark:text-white mb-3">Overview</h2>
                  <p className="text-sm text-gray-600 dark:text-gray-400 mb-3">{beach.description}</p>
                  <button className="text-blue-600 dark:text-blue-400 text-sm font-medium hover:underline">
                    Read more
                  </button>
                </div>

                {/* Gallery */}
                {beach.images && beach.images.length > 0 && (
                  <div>
                    <h3 className="text-lg font-bold text-gray-900 dark:text-white mb-3">Gallery</h3>
                    <div className="grid grid-cols-2 gap-2">
                      <div className="aspect-video rounded-lg bg-gray-200 dark:bg-gray-800 overflow-hidden">
                        {beach.images[0] && (
                          <Image
                            src={beach.images[0].photo_url}
                            alt={beach.images[0].caption}
                            fill
                            className="object-cover"
                          />
                        )}
                      </div>
                      <div className="grid grid-cols-2 gap-2">
                        {beach.images.slice(1, 3).map((img, idx) => (
                          <div key={idx} className="aspect-video rounded-lg bg-gray-200 dark:bg-gray-800 overflow-hidden">
                            <Image
                              src={img.photo_url}
                              alt={img.caption}
                              fill
                              className="object-cover"
                            />
                          </div>
                        ))}
                      </div>
                    </div>
                  </div>
                )}

                {/* What's Available */}
                <div>
                  <h3 className="text-lg font-bold text-gray-900 dark:text-white mb-3">What's available here?</h3>
                  <div className="flex gap-4 overflow-x-auto pb-2">
                    {['Cottages', 'Facilities', 'Activities', 'Food & Drinks'].map((category) => (
                      <div key={category} className="flex-shrink-0 w-40 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <p className="text-sm font-medium text-gray-900 dark:text-white">{category}</p>
                      </div>
                    ))}
                  </div>
                </div>

                {/* Reviews Preview */}
                <div>
                  <h3 className="text-lg font-bold text-gray-900 dark:text-white mb-3">Guest Reviews</h3>
                  <div className="flex items-center gap-2 mb-4">
                    <div className="flex items-center gap-1">
                      <Star size={16} className="text-yellow-500 fill-yellow-500" />
                      <span className="font-bold text-gray-900 dark:text-white">{beach.average_rating.toFixed(1)}</span>
                    </div>
                    <span className="text-sm text-gray-500 dark:text-gray-400">Based on {reviewCount} reviews</span>
                  </div>
                  <div className="flex gap-2 flex-wrap">
                    {['All', '5★', '4★', '3★', '2★', '1★', 'With Photos'].map((filter) => (
                      <button
                        key={filter}
                        className="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700"
                      >
                        {filter}
                      </button>
                    ))}
                  </div>
                </div>
              </div>
            )}

            {/* Other tabs placeholder */}
            {activeTab !== 'overview' && (
              <div className="p-8 bg-gray-50 dark:bg-gray-800 rounded-lg text-center">
                <p className="text-gray-500 dark:text-gray-400">{activeTab} content coming soon</p>
              </div>
            )}
          </div>

          {/* Right: Sidebar */}
          <div className="space-y-6">
            <div className="p-6 bg-gray-50 dark:bg-gray-800 rounded-lg sticky top-4">
              <h3 className="text-lg font-bold text-gray-900 dark:text-white mb-4">General Information</h3>

              <div className="space-y-4">
                <div>
                  <p className="text-xs text-gray-500 dark:text-gray-400 mb-1">Opening Hours</p>
                  <p className="text-sm font-medium text-gray-900 dark:text-white">{beach.opening_hours}</p>
                </div>

                {beach.entrance_fee && (
                  <div>
                    <p className="text-xs text-gray-500 dark:text-gray-400 mb-1">Entrance Fee</p>
                    <p className="text-sm font-medium text-gray-900 dark:text-white">{beach.entrance_fee}</p>
                  </div>
                )}

                <div>
                  <p className="text-xs text-gray-500 dark:text-gray-400 mb-1">Location</p>
                  <p className="text-sm font-medium text-gray-900 dark:text-white mb-1">{beach.location}</p>
                  <button className="text-blue-600 dark:text-blue-400 text-xs font-medium hover:underline flex items-center gap-1">
                    <ExternalLink size={12} />
                    View on Map
                  </button>
                </div>

                {beach.contact_phone && (
                  <div>
                    <p className="text-xs text-gray-500 dark:text-gray-400 mb-1">Contact</p>
                    <a
                      href={`tel:${beach.contact_phone}`}
                      className="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1"
                    >
                      <Phone size={14} />
                      {beach.contact_phone}
                    </a>
                  </div>
                )}

                {beach.cottage_fee && (
                  <div>
                    <p className="text-xs text-gray-500 dark:text-gray-400 mb-1">Cottage Rates</p>
                    <p className="text-sm font-medium text-gray-900 dark:text-white">{beach.cottage_fee}</p>
                    <button className="text-blue-600 dark:text-blue-400 text-xs font-medium hover:underline mt-1">
                      View Cottage Details
                    </button>
                  </div>
                )}
              </div>
            </div>

            {/* Mobile: Quick Links */}
            <div className="lg:hidden space-y-2">
              {tabs.slice(1).map((tab) => {
                const Icon = tab.icon;
                return (
                  <button
                    key={tab.id}
                    onClick={() => setActiveTab(tab.id)}
                    className="w-full flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg"
                  >
                    <div className="flex items-center gap-3">
                      <Icon size={18} className="text-gray-600 dark:text-gray-400" />
                      <span className="text-sm font-medium text-gray-900 dark:text-white">{tab.label}</span>
                    </div>
                    <ChevronRight size={16} className="text-gray-400" />
                  </button>
                );
              })}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
