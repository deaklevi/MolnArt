<script setup>
// Middleware aktiválása
definePageMeta({
  middleware: 'auth'
})

const config = useRuntimeConfig();
const apiBase = config.public.apiBase;

const getCsrfToken = () => {
  const match = document.cookie.match(new RegExp('(^| )XSRF-TOKEN=([^;]+)'));
  if (match) {
    return decodeURIComponent(match[2]);
  }
  return null;
}

// Fontos: server: false, hogy ne akadjon össze az SSR-el süti hiányában
const { data: user, pending, error } = await useFetch(`${apiBase}/api/user`, {
  credentials: 'include',
  server: false, 
  lazy: false // Megvárjuk, amíg az adat megjön
})

const logout = async () => {
  try {
    await $fetch(`${apiBase}/api/logout`, { 
      method: 'POST',
      credentials: 'include',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        // Ha továbbra is 419-et dobna a logout, ide is kell a CSRF:
        'X-XSRF-TOKEN': getCsrfToken() 
      }
    });
    
    // Siker esetén töröljük a lokális dolgokat és irány a login
    navigateTo('/'); 
  } catch (error) {
    console.error("Logout hiba:", error);
    // Még ha hiba is van (pl. lejárt session), érdemes visszavinni a felhasználót
    navigateTo('/');
  }
}
</script>

<template>
  <ClientOnly>
    <div v-if="pending" class="min-h-screen bg-slate-50 flex items-center justify-center">
      <div class="flex flex-col items-center gap-4">
        <div class="w-12 h-12 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
        <p class="text-slate-500 font-medium animate-pulse">Adatok szinkronizálása...</p>
      </div>
    </div>

    <div v-else-if="user" class="min-h-screen bg-slate-50 font-sans">
      
      <nav class="bg-white border-b border-slate-200 sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between h-16 items-center">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold">M</div>
              <span class="text-xl font-bold text-slate-800 tracking-tight">MolnArt <span class="text-indigo-600">Admin</span></span>
            </div>
            
            <div class="flex items-center gap-4">
              <div class="hidden md:flex flex-col items-end leading-tight">
                <span class="text-sm font-semibold text-slate-700">{{ user.user_name }}</span>
                <span class="text-xs text-slate-500">{{ user.description || 'Adminisztrátor' }}</span>
              </div>
              <button 
                @click="logout" 
                class="flex items-center gap-2 px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 text-sm font-medium rounded-xl transition-colors duration-200 border border-rose-100"
              >
                Kijelentkezés
              </button>
            </div>
          </div>
        </div>
      </nav>

      <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        
        <header class="mb-8">
          <h1 class="text-3xl font-bold text-slate-900">Vezérlőpult</h1>
          <p class="text-slate-500 mt-1">Üdvözöllek újra, {{ user.user_name }}! Itt találod a legfrissebb adatokat.</p>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          
          <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4">
              <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
              </div>
              <div>
                <p class="text-sm font-medium text-slate-500">Saját szolgáltatások</p>
                <p class="text-2xl font-bold text-slate-900">{{ user.services?.length || 0 }} db</p>
              </div>
            </div>
          </div>

          <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4">
              <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
              </div>
              <div>
                <p class="text-sm font-medium text-slate-500">Fiók státusz</p>
                <p class="text-2xl font-bold text-emerald-600">Aktív</p>
              </div>
            </div>
          </div>

          <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4">
              <div class="p-3 bg-amber-50 text-amber-600 rounded-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div>
                <p class="text-sm font-medium text-slate-500">Utolsó belépés</p>
                <p class="text-lg font-bold text-slate-900">Ma, 14:20</p>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-3xl border-2 border-dashed border-slate-200 p-12 text-center">
          <div class="max-w-xs mx-auto">
            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
              </svg>
            </div>
            <h3 class="text-slate-900 font-semibold italic">Még nincs megjeleníthető adat</h3>
            <p class="text-slate-500 text-sm mt-2">Kezdd el kezelni a szolgáltatásokat vagy ügyfeleket az oldalsó menü segítségével.</p>
          </div>
        </div>
      </main>
    </div>

    <div v-else class="min-h-screen flex items-center justify-center bg-slate-50 p-6">
      <div class="text-center bg-white p-8 rounded-2xl shadow-xl max-w-sm border border-rose-100">
        <div class="w-16 h-16 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
        </div>
        <h2 class="text-xl font-bold text-slate-900 mb-2">Hiba a hozzáférésben</h2>
        <p class="text-slate-500 mb-6 text-sm">A munkamenet lejárt vagy érvénytelen adatokkal próbáltál belépni.</p>
        <NuxtLink to="/" class="inline-block w-full py-3 px-4 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition-colors">
          Vissza a bejelentkezéshez
        </NuxtLink>
      </div>
    </div>

    <template #fallback>
      <div class="min-h-screen bg-slate-50 flex items-center justify-center">
        <p class="text-slate-400 font-medium animate-pulse">Betöltés...</p>
      </div>
    </template>
  </ClientOnly>
</template>

<style scoped>
/* A Tailwind mellett csak minimális egyedi CSS-re van szükség */
.animate-spin {
  animation: spin 1s linear infinite;
}
@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
</style>