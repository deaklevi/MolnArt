<script setup>
definePageMeta({ middleware: 'auth' })

const config = useRuntimeConfig()
const apiBase = config.public.apiBase

// --- Állapotkezelés ---
const calendarDays = ref([])
const reservations = ref([])
const loading = ref(true)

// --- 1. Naptár napjainak legenerálása ---
const generateCalendar = () => {
  const days = []
  const today = new Date()
  
  // Kezdő dátum: ma - 2 nap
  const startDate = new Date()
  startDate.setDate(today.getDate() - 2)
  
  // Vége dátum: ma + 2 hónap
  const endDate = new Date()
  endDate.setMonth(today.getMonth() + 2)

  let currentDate = new Date(startDate)

  while (currentDate <= endDate) {
    days.push({
      dateString: currentDate.toISOString().split('T')[0], // "2024-05-20" formátum
      dayName: currentDate.toLocaleDateString('hu-HU', { weekday: 'short' }),
      dayNumber: currentDate.getDate(),
      monthName: currentDate.toLocaleDateString('hu-HU', { month: 'short' }),
      isToday: currentDate.toDateString() === today.toDateString(),
      fullDate: new Date(currentDate)
    })
    currentDate.setDate(currentDate.getDate() + 1)
  }
  calendarDays.value = days
}

// --- 2. Foglalások lekérése az API-ból ---
const { data: bookingData, refresh } = await useFetch(`${apiBase}/api/my-bookings`, {
  credentials: 'include',
  server: false,
  lazy: true
})

// Adatok szinkronizálása
watch(bookingData, (newData) => {
  if (newData) {
    reservations.value = newData
    loading.value = false
  }
}, { immediate: true })

// Segédfüggvény: Van-e foglalás az adott napon?
const getBookingsForDay = (dateString) => {
  return reservations.value.filter(b => b.date === dateString)
}

onMounted(() => {
  generateCalendar()
})
</script>

<template>
  <div class="min-h-screen bg-slate-50 p-4 md:p-8">
    <div class="max-w-4xl mx-auto">
      
      <div class="flex items-center justify-between mb-8">
        <div>
          <h1 class="text-3xl font-bold text-slate-900">Foglalásaim</h1>
          <p class="text-slate-500">Időrendi áttekintés a következő 2 hónapra</p>
        </div>
        <NuxtLink to="/dashboard" class="p-2 bg-white rounded-xl shadow-sm border border-slate-200 hover:bg-slate-50 transition-colors">
          Vissza
        </NuxtLink>
      </div>

      <div v-if="loading" class="flex justify-center p-20">
        <div class="w-10 h-10 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
      </div>

      <div v-else class="space-y-4">
        <div v-for="day in calendarDays" :key="day.dateString" 
             :class="['flex gap-4 p-4 rounded-2xl border transition-all', 
                      day.isToday ? 'bg-indigo-50 border-indigo-200 ring-1 ring-indigo-200' : 'bg-white border-slate-200']">
          
          <div class="flex flex-col items-center justify-center min-w-[60px] h-16 rounded-xl"
               :class="day.isToday ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600'">
            <span class="text-xs font-bold uppercase">{{ day.dayName }}</span>
            <span class="text-xl font-black">{{ day.dayNumber }}</span>
          </div>

          <div class="flex-1 flex flex-col justify-center">
            <div v-if="getBookingsForDay(day.dateString).length > 0" class="space-y-2">
              <div v-for="booking in getBookingsForDay(day.dateString)" :key="booking.id"
                   class="flex items-center justify-between bg-white border border-slate-100 p-3 rounded-xl shadow-sm">
                <div>
                  <span class="font-semibold text-slate-800">{{ booking.service_name }}</span>
                  <p class="text-xs text-slate-500">{{ booking.time }} - {{ booking.location }}</p>
                </div>
                <span :class="['px-3 py-1 rounded-full text-[10px] font-bold uppercase', 
                              booking.status === 'confirmed' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700']">
                  {{ booking.status === 'confirmed' ? 'Visszaigazolva' : 'Függőben' }}
                </span>
              </div>
            </div>
            <div v-else class="text-slate-300 text-sm italic">
              Nincs foglalás erre a napra
            </div>
          </div>

          <div v-if="day.isToday" class="hidden md:flex items-center">
            <span class="bg-indigo-100 text-indigo-600 px-3 py-1 rounded-lg text-xs font-bold italic">MA</span>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<style scoped>
/* Egyedi görgetősáv vagy animációk helye */
</style>