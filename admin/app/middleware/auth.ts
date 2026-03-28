// middleware/auth.ts
export default defineNuxtRouteMiddleware(async (to, from) => {
  // Nagyon fontos: Szerver oldalon (SSR) ne csináljon semmit, 
  // mert ott nincsenek sütik, és mindig hibát dobna!
  if (import.meta.server) return;

  try {
    await $fetch('http://localhost:8000/api/user', {
      credentials: 'include',
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
  } catch (error) {
    // Csak ha valóban hiba van (lejárt session), akkor vigyen a loginra
    return navigateTo('/');
  }
});