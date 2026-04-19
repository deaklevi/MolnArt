<script setup>
definePageMeta({ middleware: 'auth' })

const config = useRuntimeConfig()
const apiBase = config.public.apiBase

const { data: currentUser } = await useFetch(`${apiBase}/api/user`, { 
  key: 'auth_user_appointments',
  credentials: 'include',
  server: false,
  lazy: true,
  transform: (res) => res?.data || res
})

const { data: publicData, pending } = await useFetch(`${apiBase}/api/user_public_data`, {
  key: 'public_data_customers',
  credentials: 'include',
  server: false,
  lazy: true,
  transform: (res) => res?.data || res
})

// 2. KERESÉS ÁLLAPOTA
const searchQuery = ref('')

// 3. SZŰRÉS LOGIKA
const myProfile = computed(() => {
  if (!publicData.value || !currentUser.value) return null
  const currentId = currentUser.value.id || currentUser.value.user?.id
  return publicData.value.find(u => u.id == currentId)
})

// Kereshető és szűrt időpontok
const filteredAppointments = computed(() => {
  const appointments = myProfile.value?.appointments || []
  
  if (!searchQuery.value) return appointments

  const query = searchQuery.value.toLowerCase()
  
  return appointments.filter(app => {
    const name = app.customer?.name?.toLowerCase() || ''
    const email = app.customer?.email?.toLowerCase() || ''
    const service = app.service?.toLowerCase() || ''
    
    return name.includes(query) || email.includes(query) || service.includes(query)
  })
})

const formatDate = (dateString) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('hu-HU', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>

<template>
  <div class="max-w-6xl mx-auto p-6 font-sans">
    <ClientOnly>
      <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-10 gap-6">
        <div>
          <h1 class="text-4xl font-black text-slate-900 tracking-tight">Vendégeim</h1>
          <p class="text-slate-500 font-medium">Kezeld az időpontokat és a vendégadatokat.</p>
        </div>

        <div class="relative w-full lg:w-96">
          <span class="absolute inset-y-0 left-4 flex items-center text-slate-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </span>
          <input 
            v-model="searchQuery"
            type="text" 
            placeholder="Keresés név, email vagy szolgáltatás alapján..."
            class="w-full pl-12 pr-4 py-4 bg-white border border-slate-200 rounded-2xl shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium text-slate-700"
          />
        </div>

        <NuxtLink to="/dashboard" class="px-6 py-3 bg-slate-100 text-slate-600 rounded-2xl font-bold hover:bg-slate-200 transition-all text-sm">
          ← Dashboard
        </NuxtLink>
      </div>

      <div v-if="pending || !currentUser" class="flex flex-col items-center justify-center py-20 gap-4">
        <div class="w-12 h-12 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
        <p class="text-slate-400 font-bold animate-pulse">Vendéglista szinkronizálása...</p>
      </div>

      <div v-else class="space-y-6">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
          <div class="bg-indigo-600 p-6 rounded-[2rem] text-white">
            <div class="text-indigo-200 text-xs font-black uppercase tracking-widest mb-1">Összes foglalás</div>
            <div class="text-3xl font-black">{{ myProfile?.appointments?.length || 0 }}</div>
          </div>
          <div class="bg-white border border-slate-100 p-6 rounded-[2rem]">
            <div class="text-slate-400 text-xs font-black uppercase tracking-widest mb-1">Találatok</div>
            <div class="text-3xl font-black text-slate-800">{{ filteredAppointments.length }}</div>
          </div>
        </div>

        <div v-if="filteredAppointments.length > 0" class="grid grid-cols-1 gap-4">
          <TransitionGroup name="list">
            <div 
              v-for="app in filteredAppointments" 
              :key="app.id"
              class="bg-white border border-slate-100 p-6 rounded-[2rem] shadow-sm hover:shadow-xl hover:scale-[1.01] transition-all duration-300 flex flex-col md:flex-row justify-between items-center gap-6"
            >
              <div class="flex items-center gap-5 w-full md:w-1/3">
                <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 text-white rounded-2xl flex items-center justify-center text-xl font-black shadow-lg shadow-indigo-200">
                  {{ app.customer?.name?.charAt(0) || '?' }}
                </div>
                <div class="overflow-hidden">
                  <h3 class="font-black text-slate-800 text-lg truncate">{{ app.customer?.name }}</h3>
                  <p class="text-slate-400 text-sm font-medium truncate">{{ app.customer?.email }}</p>
                </div>
              </div>

              <div class="flex flex-col sm:flex-row items-center gap-4 w-full md:w-2/3 justify-end">
                <div class="text-center sm:text-right">
                  <div class="text-[10px] uppercase font-black text-slate-400 tracking-tighter">Kezdés</div>
                  <div class="text-slate-700 font-bold">{{ formatDate(app.appointment_from) }}</div>
                </div>

                <div class="hidden sm:block text-slate-200">|</div>

                <div class="bg-indigo-50 px-5 py-3 rounded-2xl border border-indigo-100 min-w-[180px] text-center">
                  <div class="text-[10px] uppercase font-black text-indigo-400 mb-1 leading-none">Igényelt szolgáltatás</div>
                  <div class="text-indigo-700 font-black text-sm">{{ app.service }}</div>
                </div>

                <div class="flex gap-2">
                  <button class="p-3 bg-slate-50 text-slate-400 hover:text-indigo-600 rounded-xl transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </TransitionGroup>
        </div>

        <div v-else class="bg-slate-50 border-2 border-dashed border-slate-200 rounded-[3rem] py-24 text-center">
          <div class="text-5xl mb-4">🕵️‍♂️</div>
          <h3 class="text-xl font-bold text-slate-500">Nincs a keresésnek megfelelő vendég.</h3>
          <p class="text-slate-400 mt-2">Próbálj meg más kulcsszót megadni.</p>
          <button v-if="searchQuery" @click="searchQuery = ''" class="mt-4 text-indigo-600 font-bold underline">Kereső törlése</button>
        </div>

      </div>

      <template #fallback>
        <div class="flex justify-center py-20">
          <div class="w-10 h-10 border-4 border-slate-200 border-t-indigo-600 rounded-full animate-spin"></div>
        </div>
      </template>
    </ClientOnly>
  </div>
</template>

<style scoped>
.list-enter-active, .list-leave-active { transition: all 0.4s ease; }
.list-enter-from, .list-leave-to { opacity: 0; transform: translateY(20px); }

/* Custom Scrollbar a modern look érdekében */
input::-webkit-calendar-picker-indicator { display: none !important; }
</style>