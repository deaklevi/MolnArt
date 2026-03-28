<script setup>
// Middleware aktiválása
definePageMeta({
  middleware: 'auth'
})

const getCsrfToken = () => {
  const match = document.cookie.match(new RegExp('(^| )XSRF-TOKEN=([^;]+)'));
  if (match) {
    return decodeURIComponent(match[2]);
  }
  return null;
}

// Fontos: server: false, hogy ne akadjon össze az SSR-el süti hiányában
const { data: user, pending, error } = await useFetch('http://localhost:8000/api/user', {
  credentials: 'include',
  server: false, 
  lazy: false // Megvárjuk, amíg az adat megjön
})

const logout = async () => {
  try {
    await $fetch('http://localhost:8000/api/logout', { 
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
    <div v-if="pending">
      <p>Betöltés...</p>
    </div>

    <div v-else-if="user" class="admin-container">
      <header>
        <h1>Üdvözöllek, {{ user.user_name }}!</h1>
        <button @click="logout">Kijelentkezés</button>
      </header>
      <main>
        <h2>Admin Vezérlőpult</h2>
        <p>Saját szolgáltatások: {{ user.services?.length || 0 }}</p>
      </main>
    </div>

    <div v-else>
      <p>Hiba történt a belépés során. <NuxtLink to="/">Jelentkezz be újra!</NuxtLink></p>
    </div>

    <template #fallback>
      <p>Oldal betöltése...</p>
    </template>
  </ClientOnly>
</template>

<style scoped>
.admin-container { font-family: sans-serif; padding: 20px; }
header { border-bottom: 2px solid #eee; padding-bottom: 20px; }
img { width: 80px; border-radius: 50%; }
button { background: #ff4444; color: white; border: none; padding: 10px; cursor: pointer; }
</style>