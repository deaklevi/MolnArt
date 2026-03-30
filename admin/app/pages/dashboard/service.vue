<script setup>
definePageMeta({ middleware: 'auth' })

const config = useRuntimeConfig()
const apiBase = config.public.apiBase

// --- ADATOK LEKÉRÉSE (Csak kliens oldalon!) ---

// 1. Összes szolgáltatás
const { data: allServices, refresh: refreshAll, pending: servicesPending } = await useFetch(`${apiBase}/api/services`, {
  server: false,
  lazy: true,
  transform: (response) => response.data || response 
})

// 2. Bejelentkezett felhasználó
const { data: user, pending: userPending } = await useFetch(`${apiBase}/api/user`, { 
  key: 'user_services_view',
  credentials: 'include',
  server: false,
  lazy: true
})

// --- ÁLLAPOTOK ---
const selectedIds = ref([]) 
const isSaving = ref(false)
const isCreating = ref(false)
const statusMessage = ref({ text: '', type: '' })

// 3. SZINKRONIZÁCIÓ: Megvárjuk, amíg a user megérkezik (F5 után is)
watch(user, (newUser) => {
  if (newUser?.services) {
    // Kicsomagoljuk a már meglévő szolgáltatásai ID-it
    selectedIds.value = newUser.services.map(s => s.id)
  }
}, { immediate: true })

const getCsrfToken = () => {
  if (process.server) return null
  const match = document.cookie.match(new RegExp('(^| )XSRF-TOKEN=([^;]+)'));
  return match ? decodeURIComponent(match[2]) : null;
}

// PIPÁLÁS LOGIKA
const toggleLocalService = (id) => {
  const index = selectedIds.value.indexOf(id)
  if (index === -1) {
    selectedIds.value.push(id)
  } else {
    selectedIds.value.splice(index, 1)
  }
}

// MENTÉS
const saveChanges = async () => {
  isSaving.value = true
  statusMessage.value = { text: '', type: '' }
  
  try {
    await $fetch(`${apiBase}/api/user/services/sync`, {
      method: 'POST',
      body: { service_ids: selectedIds.value },
      credentials: 'include',
      headers: { 
        'Accept': 'application/json',
        'X-XSRF-TOKEN': getCsrfToken() 
      }
    })
    statusMessage.value = { text: 'Beállítások sikeresen elmentve!', type: 'success' }
    setTimeout(() => statusMessage.value.text = '', 4000)
  } catch (e) {
    statusMessage.value = { text: 'Hiba történt a mentés során.', type: 'error' }
  } finally {
    isSaving.value = false
  }
}

// ÚJ SZOLGÁLTATÁS
const newService = ref({ name: '', time: 30 })
const createService = async () => {
  isCreating.value = true
  try {
    await $fetch(`${apiBase}/api/services`, {
      method: 'POST',
      body: newService.value,
      credentials: 'include',
      headers: { 
        'Accept': 'application/json',
        'X-XSRF-TOKEN': getCsrfToken() 
      }
    })
    newService.value = { name: '', time: 30 }
    statusMessage.value = { text: 'Új szolgáltatás létrehozva!', type: 'success' }
    await refreshAll() // Frissítjük a globális listát
  } catch (e) {
    alert("Hiba a létrehozáskor!")
  } finally {
    isCreating.value = false
  }
}
</script>

<template>
  <div class="max-w-4xl mx-auto p-6 font-sans">
    
    <ClientOnly>
      
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
        <div>
          <h1 class="text-3xl font-black text-slate-900 tracking-tight">Szolgáltatásaim</h1>
          <p class="text-slate-500 font-medium">Válaszd ki, milyen szolgáltatásokat vállalsz.</p>
        </div>
        <NuxtLink to="/dashboard" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-sm font-bold hover:bg-slate-50 transition-all shadow-sm">
          ← Irányítópult
        </NuxtLink>
      </div>

      <div v-if="userPending || servicesPending" class="flex flex-col items-center justify-center py-20 gap-4">
        <div class="w-10 h-10 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
        <p class="text-slate-400 font-bold animate-pulse">Szolgáltatások betöltése...</p>
      </div>

      <template v-else>
        <div class="bg-indigo-50/50 p-6 rounded-3xl border border-indigo-100 mb-8">
          <h3 class="text-xs font-black uppercase tracking-widest text-indigo-600 mb-4">Új típus felvétele a rendszerbe</h3>
          <form @submit.prevent="createService" class="flex flex-wrap gap-3">
            <input v-model="newService.name" placeholder="Szolgáltatás neve..." class="flex-1 min-w-[200px] p-3 rounded-xl border-none ring-1 ring-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none" required />
            <input v-model="newService.time" type="number" placeholder="Perc" class="w-24 p-3 rounded-xl border-none ring-1 ring-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none" required />
            <button type="submit" :disabled="isCreating" class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-indigo-700 disabled:opacity-50 transition-all active:scale-95">
              {{ isCreating ? '...' : 'Hozzáadás' }}
            </button>
          </form>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] shadow-xl shadow-slate-200/40 border border-slate-100">
          
          <div class="flex flex-col sm:flex-row items-center justify-between mb-8 gap-4">
            <h3 class="text-xl font-bold text-slate-800">Elérhető listád</h3>
            
            <button 
              @click="saveChanges" 
              :disabled="isSaving"
              class="w-full sm:w-auto bg-emerald-500 text-white px-8 py-4 rounded-2xl font-black shadow-lg shadow-emerald-200 hover:bg-emerald-600 active:scale-95 transition-all disabled:opacity-50 flex items-center justify-center gap-3"
            >
              <span v-if="isSaving" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
              {{ isSaving ? 'Mentés...' : 'VÁLTOZÁSOK MENTÉSE' }}
            </button>
          </div>

          <Transition name="fade">
            <div v-if="statusMessage.text" :class="['mb-6 p-4 rounded-xl text-center font-bold text-sm border', statusMessage.type === 'success' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-rose-50 text-rose-600 border-rose-100']">
              {{ statusMessage.text }}
            </div>
          </Transition>

          <div v-if="allServices?.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div 
              v-for="service in allServices" 
              :key="service.id"
              @click="toggleLocalService(service.id)"
              :class="[
                'group p-5 border-2 rounded-2xl cursor-pointer transition-all flex items-center gap-4 relative',
                selectedIds.includes(service.id) ? 'border-indigo-600 bg-indigo-50/40' : 'border-slate-50 bg-slate-50/50 hover:border-slate-200'
              ]"
            >
              <div :class="['w-6 h-6 rounded-lg border-2 flex items-center justify-center transition-all', selectedIds.includes(service.id) ? 'bg-indigo-600 border-indigo-600' : 'bg-white border-slate-200']">
                <svg v-if="selectedIds.includes(service.id)" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
              </div>

              <div>
                <div class="font-bold text-slate-900 group-hover:text-indigo-600 transition-colors">{{ service.name }}</div>
                <div class="text-xs font-bold text-slate-400 uppercase tracking-tighter">{{ service.time }} perc</div>
              </div>
            </div>
          </div>

          <div v-else class="text-center py-16">
            <p class="text-slate-400 font-medium italic">Nincsenek elérhető szolgáltatások.</p>
          </div>
        </div>
      </template>

      <template #fallback>
        <div class="p-20 flex justify-center">
          <div class="w-10 h-10 border-4 border-slate-100 border-t-indigo-600 rounded-full animate-spin"></div>
        </div>
      </template>

    </ClientOnly>
  </div>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.4s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>