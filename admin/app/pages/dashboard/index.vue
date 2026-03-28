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
          <p class="text-slate-500 mt-1">Üdvözöllek újra, {{ user.user_name }}! Válassz egy műveletet:</p>
        </header>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          
          <NuxtLink to="/dashboard/profile" class="group bg-white p-8 rounded-3xl border border-slate-200 shadow-sm hover:shadow-xl hover:border-indigo-300 transition-all duration-300 text-left">
            <div class="w-14 h-14 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">Profilom</h3>
            <p class="text-slate-500 text-sm leading-relaxed">Személyes adatok, bemutatkozás és jelszó módosítása.</p>
          </NuxtLink>

          <NuxtLink to="#" class="group bg-white p-8 rounded-3xl border border-slate-200 shadow-sm hover:shadow-xl hover:border-emerald-300 transition-all duration-300 text-left">
            <div class="w-14 h-14 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">Szolgáltatások</h3>
            <p class="text-slate-500 text-sm leading-relaxed">Új szolgáltatások felvétele, árak és leírások szerkesztése.</p>
          </NuxtLink>

          <NuxtLink to="#" class="group bg-white p-8 rounded-3xl border border-slate-200 shadow-sm hover:shadow-xl hover:border-amber-300 transition-all duration-300 text-left">
            <div class="w-14 h-14 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.382-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
              </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">Vélemények</h3>
            <p class="text-slate-500 text-sm leading-relaxed">Ügyfelek visszajelzéseinek kezelése és moderálása.</p>
          </NuxtLink>

          <NuxtLink to="#" class="group bg-white p-8 rounded-3xl border border-slate-200 shadow-sm hover:shadow-xl hover:border-blue-300 transition-all duration-300 text-left">
            <div class="w-14 h-14 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">Foglalások</h3>
            <p class="text-slate-500 text-sm leading-relaxed">Időpontok kezelése, naptár és visszaigazolások.</p>
          </NuxtLink>

          <NuxtLink to="#" class="group bg-white p-8 rounded-3xl border border-slate-200 shadow-sm hover:shadow-xl hover:border-rose-300 transition-all duration-300 text-left">
            <div class="w-14 h-14 bg-rose-100 text-rose-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">Kuncsaftjaid</h3>
            <p class="text-slate-500 text-sm leading-relaxed">Ügyfélkört érintő statisztikák és elérhetőségek listája.</p>
          </NuxtLink>

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