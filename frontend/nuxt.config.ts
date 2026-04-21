// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: false },
  modules: ['@nuxtjs/tailwindcss','@pinia/nuxt'],

  runtimeConfig: {
    public: {
      // Itt CSAK az alapértelmezett értéket add meg. 
      // A Nuxt automatikusan keresni fogja a NUXT_PUBLIC_API_BASE-t a .env-ben!
      apiBase: 'http://localhost:8000'
    }
  },
})