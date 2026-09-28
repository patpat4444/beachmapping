import { createHmac } from 'crypto';

export function hashPin(pin: string) {
  const secret = process.env.PIN_LOOKUP_SECRET || process.env.SUPABASE_SERVICE_ROLE_KEY;
  if (!secret) throw new Error('PIN_LOOKUP_SECRET is required to verify portal PINs.');
  return createHmac('sha256', secret).update(pin).digest('hex');
}