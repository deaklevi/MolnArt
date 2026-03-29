<script setup>
definePageMeta({ middleware: 'auth' })

const config = useRuntimeConfig()
const apiBase = config.public.apiBase

// --- Állapotkezelés ---
const calendarDays = ref([])
const loading = ref(true)

// 1. Adatok lekérése a Laravel-től (Cookie-kkal)
const { data: appointments, pending, refresh } = await useFetch(`${apiBase}/api/appointments`, {
  credentials: 'include',
  server: false,
  lazy: true,
  headers: {
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest'
  }
})

// CSRF Token segédfüggvény (ahogy kérted)
const getCsrfToken = () => {
  if (process.server) return null
  const match = document.cookie.match(new RegExp('(^| )XSRF-TOKEN=([^;]+)'));
  return match ? decodeURIComponent(match[2]) : null;
}

// 2. Naptár generálás (-2 nap, +2 hónap)
const generateCalendar = () => {
  const days = []
  const start = new Date()
  start.setDate(start.getDate() - 2)
  
  const end = new Date()
  end.setMonth(end.getMonth() + 2)

  let current = new Date(start)
  while (current <= end) {
    days.push({
      dateStr: current.toISOString().split('T')[0],
      dayNum: current.getDate(),
      dayName: current.toLocaleDateString('hu-HU', { weekday: 'short' }),
      isToday: current.toDateString() === (new Date()).toDateString()
    })
    current.setDate(current.getDate() + 1)
  }
  calendarDays.value = days
}

// Szűrés: Az adott naphoz tartozó foglalások kigyűjtése
const getDayEvents = (dateStr) => {
  if (!appointments.value?.data) return []
  return appointments.value.data.filter(app => app.appointment_from.startsWith(dateStr))
}

onMounted(() => {
  generateCalendar()
})
</script>

<template>
  <div class="min-h-screen bg-slate-50 p-4 md:p-8">
    <div class="max-w-3xl mx-auto">
      
      <div class="mb-8 flex justify-between items-end">
        <div>
          <h1 class="text-3xl font-bold text-slate-900">Naptáram</h1>
          <p class="text-slate-500 text-sm">Időpontok kezelése és áttekintése</p>
        </div>
        <button @click="refresh" class="text-xs bg-white border px-3 py-2 rounded-lg hover:bg-slate-50 transition-all">
          Frissítés
        </button>
      </div>

      <ClientOnly>
        <div v-if="pending" class="space-y-4">
          <div v-for="i in 5" :key="i" class="h-20 bg-white rounded-2xl animate-pulse border border-slate-100"></div>
        </div>

        <div v-else class="space-y-3">
          <div v-for="day in calendarDays" :key="day.dateStr" 
               :class="['group flex gap-4 p-4 rounded-2xl border transition-all duration-300', 
                        day.isToday ? 'bg-indigo-50 border-indigo-200 shadow-sm' : 'bg-white border-slate-200 hover:border-indigo-300']">
            
            <div class="flex flex-col items-center justify-center min-w-[50px]">
              <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ day.dayName }}</span>
              <span :class="['text-xl font-black', day.isToday ? 'text-indigo-600' : 'text-slate-700']">{{ day.dayNum }}</span>
            </div>

            <div class="flex-1 flex flex-col gap-2 border-l border-slate-100 pl-4">
              <div v-if="getDayEvents(day.dateStr).length > 0" class="space-y-2">
                <div v-for="app in getDayEvents(day.dateStr)" :key="app.id" 
                     class="bg-white border border-slate-100 p-3 rounded-xl shadow-sm flex justify-between items-center group-hover:shadow-md transition-shadow">
                  <div>
                    <div class="font-bold text-slate-800 text-sm">{{ app.service }}</div>
                    <div class="text-xs text-indigo-500 font-medium">
                      {{ app.appointment_from.split(' ')[1].substring(0,5) }} - {{ app.customer?.name }}
                    </div>
                  </div>
                  <button class="opacity-0 group-hover:opacity-100 p-2 hover:bg-rose-50 rounded-lg text-rose-500 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              </div>
              <div v-else class="h-full flex items-center">
                <span class="text-slate-300 text-xs italic">Nincs bejegyzés</span>
              </div>
            </div>
          </div>
        </div>
      </ClientOnly>

    </div>
  </div>
</template>