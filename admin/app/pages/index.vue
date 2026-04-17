<script setup>
const form = ref({
  user_name: '',
  password: ''
});

const config = useRuntimeConfig();
const apiBase = config.public.apiBase;

// Ez a kis segédfüggvény kiolvassa a böngészőből a Laravel által adott tokent
const getCsrfToken = () => {
  const match = document.cookie.match(new RegExp('(^| )XSRF-TOKEN=([^;]+)'));
  if (match) {
    // A Laravel kódolja az URL-t, ezt vissza kell fejteni
    return decodeURIComponent(match[2]); 
  }
  return null;
}

const login = async () => {
  try {
    // 1. Kérjük a sütiket a Laraveltől
    await $fetch(`${apiBase}/sanctum/csrf-cookie`, { 
      credentials: 'include' 
    });

    // 2. Kiolvassuk a tokent, amit az előbb kaptunk
    const csrfToken = getCsrfToken();

    // 3. Elküldjük a Login kérést, a fejlécbe beletéve a tokent!
    await $fetch(`${apiBase}/login`, {
      method: 'POST',
      body: form.value,
      credentials: 'include',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest', // Szólunk, hogy ez egy AJAX kérés
        'X-XSRF-TOKEN': csrfToken // <--- EZ A KULCS, ami miatt eddig 419-et kaptál!
      }
    });

    
    // Ha idáig eljut, a 419 eltűnt, és sikeres a belépés!
    navigateTo('/dashboard');
    let userlog = await $fetch('http://localhost:8000/api/user', {
  credentials: 'include'
})
  console.log(userlog)
  } catch (error) {
    console.error("Hiba a belépésnél:", error);
    alert("Hibás felhasználónév vagy jelszó!");
  }
};
</script>

<template>
  <div class="flex items-center justify-center min-h-screen p-6">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-slate-100 p-8">
      
      <div class="mb-8 text-center">
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">
          MolnArt <span class="text-indigo-600">Admin</span>
        </h1>
        <p class="text-slate-500 text-sm mt-1">Üdvözöljük! Kérjük, jelentkezzen be.</p>
      </div>

      <form @submit.prevent="login" class="space-y-5">
        
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1.5 ml-1">
            Felhasználónév
          </label>
          <input 
            v-model="form.user_name" 
            type="text" 
            placeholder="admin_felhasznalo"
            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1.5 ml-1">
            Jelszó
          </label>
          <input 
            v-model="form.password" 
            type="password" 
            placeholder="••••••••"
            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200"
          />
        </div>

        <button 
          type="submit"
          :disabled="!form.user_name || !form.password"
          class="w-full py-3.5 px-4 bg-indigo-600 hover:bg-indigo-700 disabled:bg-slate-300 disabled:cursor-not-allowed text-white font-semibold rounded-xl shadow-md shadow-indigo-200 transition-all duration-200 active:scale-[0.98] flex items-center justify-center gap-2"
        >
          Belépés
        </button>
      </form>

      <div class="mt-8 pt-6 border-t border-slate-100 text-center">
        <p class="text-slate-400 text-xs uppercase tracking-widest font-medium">
          Biztonságos Rendszer
        </p>
      </div>
    </div>
  </div>
</template>