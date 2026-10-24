export function formatOpeningHours(openingHours: string | Record<string, string>): string {
  if (typeof openingHours === 'string') {
    return openingHours;
  }

  const hours = Object.values(openingHours).find((value) => value && value !== 'CLOSE');
  return hours ? String(hours) : 'Closed';
}
