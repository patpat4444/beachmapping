-- ==========================================
-- FIX PIN AUTHENTICATION SCRIPT
-- ==========================================
-- This script clears all PINs from the database so users can login again
-- using their original PINs (ADMIN_INITIAL_PIN for admin, or the PINs sent via email for owners)
--
-- Run this in your Supabase SQL Editor
-- ==========================================

-- Clear PINs for all admin users
UPDATE profiles
SET pin = NULL,
    updated_at = NOW()
WHERE role IN ('admin', 'beach_manager');

-- Clear PINs for all beach owners
UPDATE profiles
SET pin = NULL,
    updated_at = NOW()
WHERE role = 'beach_owner';

-- Verify the changes
SELECT
    id,
    email,
    full_name,
    role,
    pin,
    updated_at
FROM profiles
WHERE role IN ('admin', 'beach_manager', 'beach_owner')
ORDER BY role, email;

-- After running this:
-- 1. Admin can login with ADMIN_INITIAL_PIN from .env.local
-- 2. Beach owners can login with the PIN sent to their email
-- 3. The system will automatically hash and store the PINs on first successful login
