<script setup>
const form = ref({
  user_name: '',
  password: ''
});

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
    await $fetch('http://localhost:8000/sanctum/csrf-cookie', { 
      credentials: 'include' 
    });

    // 2. Kiolvassuk a tokent, amit az előbb kaptunk
    const csrfToken = getCsrfToken();

    // 3. Elküldjük a Login kérést, a fejlécbe beletéve a tokent!
    await $fetch('http://localhost:8000/api/login', {
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
    
  } catch (error) {
    console.error("Hiba a belépésnél:", error);
    alert("Hibás felhasználónév vagy jelszó!");
  }
};
</script>

<template>
  <div>
    <h1>Admin Belépés</h1>
    <form @submit.prevent="login">
      <input v-model="form.user_name" placeholder="Felhasználónév" />
      <input v-model="form.password" type="password" placeholder="Jelszó" />
      <button type="submit">Belépés</button>
    </form>
  </div>
</template>