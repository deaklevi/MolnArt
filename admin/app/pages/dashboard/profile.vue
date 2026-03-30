<script setup>
definePageMeta({ middleware: 'auth' })

const config = useRuntimeConfig()
const apiBase = config.public.apiBase

// 1. User lekérése - server: false kulcsfontosságú a hydration hiba elkerüléséhez
const { data: user, pending } = await useFetch(`${apiBase}/api/user`, { 
  credentials: 'include',
  server: false,
  lazy: true
})

const form = ref({
  user_name: '',
  description: '',
  avatar: null
})

const previewUrl = ref(null)
const loading = ref(false)
const message = ref({ type: '', text: '' })

// 2. Adatok betöltése az űrlapba, amint megérkeznek
watch(user, (newUser) => {
  if (newUser) {
    form.value.user_name = newUser.user_name || ''
    form.value.description = newUser.description || ''
    
    // A Seeder profile_image-et használ, a controller avatar-t. Kezeljük mindkettőt.
    const dbImage = newUser.profile_image || newUser.avatar
    
    if (dbImage) {
      // Ha a seederben '/storage/avatars/...' van, az apiBase-t elé rakjuk
      previewUrl.value = dbImage.startsWith('http') 
        ? dbImage 
        : `${apiBase}${dbImage.startsWith('/') ? '' : '/'}${dbImage}`
    }
  }
}, { immediate: true })

const getCsrfToken = () => {
  if (process.server) return null
  const match = document.cookie.match(new RegExp('(^| )XSRF-TOKEN=([^;]+)'));
  return match ? decodeURIComponent(match[2]) : null;
}

const handleFileChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    form.value.avatar = file
    previewUrl.value = URL.createObjectURL(file)
  }
}

const updateProfile = async () => {
  loading.value = true
  message.value = { type: '', text: '' }

  const formData = new FormData()
  formData.append('user_name', form.value.user_name)
  formData.append('description', form.value.description || '')
  if (form.value.avatar) {
    formData.append('avatar', form.value.avatar)
  }

  try {
    await $fetch(`${apiBase}/api/user/update`, {
      method: 'POST',
      body: formData,
      credentials: 'include',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-XSRF-TOKEN': getCsrfToken()
      }
    })
    
    message.value = { type: 'success', text: 'Profil sikeresen frissítve!' }
    setTimeout(() => window.location.reload(), 1000)
  } catch (err) {
    message.value = { type: 'error', text: 'Hiba történt a mentés során.' }
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen bg-slate-50 p-4 md:p-8 font-sans">
    <div class="max-w-2xl mx-auto">
      
      <NuxtLink to="/dashboard" class="group text-slate-500 flex items-center gap-2 mb-6 hover:text-indigo-600 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Vissza a vezérlőpultra
      </NuxtLink>

      <ClientOnly>
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
          
          <div v-if="pending" class="p-20 flex flex-col items-center justify-center gap-4">
            <div class="w-12 h-12 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
            <p class="text-slate-400 animate-pulse">Profil adatok betöltése...</p>
          </div>

          <template v-else>
            <div class="p-8 border-b border-slate-100 bg-indigo-50/30">
              <h1 class="text-2xl font-bold text-slate-900">Profil szerkesztése</h1>
              <p class="text-slate-500">Módosítsd a nyilvános adataidat.</p>
            </div>

            <form @submit.prevent="updateProfile" class="p-8 space-y-6">
              <div class="flex flex-col items-center gap-4 py-4">
                <div class="relative group">
                  <img 
                    :src="previewUrl || `https://ui-avatars.com/api/?name=${form.user_name}&background=EEF2FF&color=4F46E5`" 
                    class="w-32 h-32 rounded-3xl object-cover border-4 border-white shadow-lg group-hover:ring-4 group-hover:ring-indigo-100 transition-all" 
                  />
                  <label class="absolute inset-0 flex items-center justify-center bg-black/40 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer text-white text-xs font-bold backdrop-blur-[2px]">
                    Kép csere
                    <input type="file" class="hidden" @change="handleFileChange" accept="image/*" />
                  </label>
                </div>
                <p class="text-xs text-slate-400">Kattints a képre a módosításhoz</p>
              </div>

              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-2">Felhasználónév</label>
                  <input v-model="form.user_name" type="text" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none transition-all" />
                </div>

                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-2">Bemutatkozás</label>
                  <textarea v-model="form.description" rows="3" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none transition-all" placeholder="Pl. Adminisztrátor vagyok..."></textarea>
                </div>
              </div>

              <Transition name="fade">
                <div v-if="message.text" :class="`p-4 rounded-xl text-sm font-medium border ${message.type === 'success' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-rose-50 text-rose-700 border-rose-100'}`">
                  {{ message.text }}
                </div>
              </Transition>

              <button 
                type="submit" 
                :disabled="loading" 
                class="w-full py-4 bg-indigo-600 text-white font-bold rounded-2xl hover:bg-indigo-700 transition-all shadow-lg active:scale-[0.98] disabled:opacity-50"
              >
                {{ loading ? 'Mentés folyamatban...' : 'Módosítások mentése' }}
              </button>
            </form>
          </template>
        </div>

        <template #fallback>
          <div class="bg-white rounded-3xl p-20 flex justify-center border border-slate-100">
            <div class="w-10 h-10 border-4 border-slate-200 border-t-indigo-600 rounded-full animate-spin"></div>
          </div>
        </template>
      </ClientOnly>
    </div>
  </div>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>