<script setup>
// Middleware aktiválása
definePageMeta({
  middleware: 'auth'
})

const { data: user, error } = await useFetch('http://localhost:8000/api/user', {
  credentials: 'include', // Kiemelten fontos: küldi a sütiket!
})

const logout = async () => {
  await $fetch('http://localhost:8000/logout', { 
    method: 'POST',
    credentials: 'include' 
  });
  navigateTo('/login');
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