<script setup>
definePageMeta({ middleware: 'auth' })

const config = useRuntimeConfig()
const apiBase = config.public.apiBase

// 1. ADATOK LEKÉRÉSE - Csak kliens oldalon (server: false)
// Így a böngésző küldi el a sütiket, és nem lesz üres a válasz F5 után
const { data: user } = await useFetch(`${apiBase}/api/user`, { 
  credentials: 'include',
  server: false, 
  lazy: true,
  transform: (res) => res?.data || res
})

const { data: publicData, refresh, pending } = await useFetch(`${apiBase}/api/user_public_data`, {
  credentials: 'include',
  server: false,
  lazy: true,
  transform: (res) => res?.data || res
})

// 2. REAKTÍV PROFIL ÁLLAPOT
const myProfile = computed(() => {
  if (!publicData.value || !user.value) return null
  
  // Megkeressük az ID-t (kezelve a Laravel esetleges 'data' burkolását)
  const currentId = user.value.id || user.value.user?.id
  
  // Típusfüggetlen keresés (==)
  return publicData.value.find(u => u.id == currentId)
})

const isDeleting = ref(null)
const statusMessage = ref({ text: '', type: '' })

const getCsrfToken = () => {
  if (process.server) return null
  const match = document.cookie.match(new RegExp('(^| )XSRF-TOKEN=([^;]+)'));
  return match ? decodeURIComponent(match[2]) : null;
}

// 3. TÖRLÉS LOGIKA
const deleteReview = async (reviewId) => {
  if (!confirm('Biztosan törölni szeretnéd ezt a véleményt?')) return

  isDeleting.value = reviewId
  try {
    await $fetch(`${apiBase}/api/reviews/${reviewId}`, {
      method: 'DELETE',
      credentials: 'include',
      headers: {
        'Accept': 'application/json',
        'X-XSRF-TOKEN': getCsrfToken()
      }
    })
    
    statusMessage.value = { text: 'Vélemény sikeresen eltávolítva!', type: 'success' }
    await refresh() 
    setTimeout(() => statusMessage.value.text = '', 3000)
  } catch (e) {
    statusMessage.value = { text: 'Hiba történt a törlés során.', type: 'error' }
  } finally {
    isDeleting.value = null
  }
}
</script>

<template>
  <div class="max-w-5xl mx-auto p-6 font-sans">
    
    <ClientOnly>
      
      <div v-if="pending || !user" class="flex flex-col items-center justify-center py-20 gap-4">
        <div class="w-12 h-12 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
        <p class="text-slate-400 font-bold animate-pulse">Vélemények szinkronizálása...</p>
      </div>

      <div v-else-if="myProfile" class="space-y-8">
        
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-3xl font-black text-slate-900">Vélemények Kezelése</h1>
          <NuxtLink to="/dashboard" class="px-5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold transition-all">
            ← Vissza
          </NuxtLink>
        </div>
        <div v-if="myProfile.reviews?.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div 
            v-for="review in myProfile.reviews" 
            :key="review.id"
            class="group bg-white border border-slate-100 p-6 rounded-[2rem] hover:shadow-xl transition-all"
          >
            <div class="flex justify-between items-start mb-4">
              <div>
                <div class="font-black text-slate-800 text-lg">{{ review.name }}</div>
                <div class="flex text-amber-400 text-sm mt-1">
                  <span v-for="i in 5" :key="i">{{ i <= review.rating ? '★' : '☆' }}</span>
                </div>
              </div>
              
              <button 
                @click="deleteReview(review.id)"
                :disabled="isDeleting === review.id"
                class="p-2.5 text-slate-300 hover:text-rose-500 hover:bg-rose-50 rounded-xl transition-all"
              >
                <svg v-if="isDeleting !== review.id" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                <div v-else class="w-6 h-6 border-2 border-rose-500 border-t-transparent rounded-full animate-spin"></div>
              </button>
            </div>
            <p class="text-slate-600 italic">"{{ review.comment }}"</p>
          </div>
        </div>

        <div v-else class="text-center py-20 bg-slate-50 rounded-[2.5rem] border-2 border-dashed border-slate-200">
          <p class="text-slate-400 font-bold italic">Még nem érkezett vélemény.</p>
        </div>
      </div>

      <div v-else class="bg-amber-50 border border-amber-100 p-10 rounded-[2.5rem] text-center">
        <h3 class="text-xl font-bold text-amber-900">Profil nem található</h3>
        <p class="text-amber-700 mt-2">Próbálkozz az oldal frissítésével vagy jelentkezz be újra.</p>
        <button @click="refresh" class="mt-4 px-6 py-2 bg-amber-200 text-amber-800 rounded-xl font-bold">Újratöltés</button>
      </div>

      <Transition name="fade">
        <div v-if="statusMessage.text" 
          :class="['fixed bottom-10 right-10 px-8 py-4 rounded-2xl shadow-2xl font-bold text-white z-50', 
          statusMessage.type === 'success' ? 'bg-emerald-600' : 'bg-rose-600']">
          {{ statusMessage.text }}
        </div>
      </Transition>

      <template #fallback>
        <div class="flex justify-center py-20">
          <div class="w-10 h-10 border-4 border-slate-200 border-t-indigo-600 rounded-full animate-spin"></div>
        </div>
      </template>
    </ClientOnly>

  </div>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>