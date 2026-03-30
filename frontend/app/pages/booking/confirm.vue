<script setup>
const route = useRoute();
const router = useRouter();

onMounted(() => {
  const { g_name, g_email, g_auth } = route.query;

  if (g_auth === 'success' && g_email) {
    // Adatok mentése a naptár számára
    sessionStorage.setItem('google_user_data', JSON.stringify({
      name: g_name || '',
      email: g_email || '',
      authenticated: true
    }));
    
    // Azonnali átlépés a naptárhoz
    router.replace('/booking/calendar'); 
  } else {
    router.replace('/booking');
  }
});
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-stone-50 font-serif">
    <div class="text-center">
      <div class="w-12 h-12 border-4 border-stone-200 border-t-purple-900 rounded-full animate-spin mx-auto mb-6"></div>
      <p class="text-stone-500 uppercase tracking-[0.2em] text-xs">Személyazonosság ellenőrzése...</p>
    </div>
  </div>
</template>