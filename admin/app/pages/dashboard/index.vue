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

const { data: user, error } = await useFetch('http://localhost:8000/api/user', {
  credentials: 'include', // Kiemelten fontos: küldi a sütiket!
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
  <div v-if="user" class="admin-container">
    <header>
      <img :src="user.profile_image || 'https://via.placeholder.com/150'" alt="Profile">
      <h1>Üdvözöllek, {{ user.user_name }}!</h1>
      <p>Jogosultság: {{ user.description }}</p>
      <button @click="logout">Kijelentkezés</button>
    </header>

    <main>
      <h2>Admin Vezérlőpult</h2>
      <p>Ezt a felületet csak a 3 előre létrehozott admin látja.</p>
      
      <div class="stats">
        <p>Saját szolgáltatások száma: {{ user.services?.length || 0 }}</p>
      </div>
    </main>
  </div>
</template>

<style scoped>
.admin-container { font-family: sans-serif; padding: 20px; }
header { border-bottom: 2px solid #eee; padding-bottom: 20px; }
img { width: 80px; border-radius: 50%; }
button { background: #ff4444; color: white; border: none; padding: 10px; cursor: pointer; }
</style>