'use client';

import React, { useState } from 'react';
import { Sidebar } from './Sidebar';
import { Topbar } from './Topbar';

interface AppShellProps {
  children: React.ReactNode;
  operatorName?: string;
  beachName?: string;
  beachSlug?: string;
  isOpenNow?: boolean;
  openingHours?: string;
  reviewCount?: number;
}

export function AppShell({
  children,
  operatorName = 'Resort Operator',
  beachName = 'Catmon Beach Resort',
  beachSlug,
  isOpenNow = true,
  openingHours = '8:00 AM – 6:00 PM',
  reviewCount = 0,
}: AppShellProps) {
  const [mobileNavOpen, setMobileNavOpen] = useState(false);

  return (
    <div
      className="min-h-screen flex font-sans transition-colors duration-200"
      style={{
        backgroundColor: 'var(--color-sand-50)',
        color: 'var(--color-text)',
      }}
    >
      {/* 1. SIDEBAR (264px desktop / off-canvas drawer mobile) */}
      <Sidebar
        mobileOpen={mobileNavOpen}
        onClose={() => setMobileNavOpen(false)}
        reviewCount={reviewCount}
        beachSlug={beachSlug}
        beachName={beachName}
      />

      {/* 2. MAIN APPLICATION CONTENT AREA */}
      <div className="flex-1 flex flex-col min-w-0">
        {/* Topbar Landmark */}
        <Topbar
          onOpenMobileNav={() => setMobileNavOpen(true)}
          operatorName={operatorName}
          isOpenNow={isOpenNow}
          openingHours={openingHours}
        />

        {/* Page Content: 1440px max width, 32px gutters, consistent left edge */}
        <main
          className="flex-1 p-6 sm:p-8 max-w-[1440px] w-full mx-auto"
          style={{
            animation: 'fadeUp 240ms ease-out forwards',
          }}
        >
          {children}
        </main>
      </div>
    </div>
  );
}
