export default defineNuxtRouteMiddleware(async (to, from) => {
  // Csak kliens oldalon futtatjuk az ellenőrzést, hogy elkerüljük az SSR hibákat
  if (import.meta.server) return;

  try {
    // A $fetch-et használjuk useFetch helyett a middleware-ben!
    await $fetch('http://localhost:8000/api/user', {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      credentials: 'include', // Fontos a süti miatt!
    });
  } catch (error) {
    // Ha a Laravel 401-et vagy hibát dob, nem vagyunk belépve
    console.error("Auth hiba, irány a login...");
    return navigateTo('/');
  }
});