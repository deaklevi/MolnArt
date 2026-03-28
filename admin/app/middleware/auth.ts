// middleware/auth.ts
export default defineNuxtRouteMiddleware(async (to) => {
  if (to.path === '/') return;

  try {
    await $fetch('http://localhost:8000/api/user', {
      credentials: 'include',
    });
  } catch (err) {
    return navigateTo('/'); 
  }
});