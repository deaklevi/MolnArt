<script setup>
definePageMeta({ middleware: 'auth' })

const config = useRuntimeConfig()
const apiBase = config.public.apiBase

// --- Állapotkezelés ---
const loading = ref(true)
const appointments = ref([])
const startIndex = ref(2) 
const daysToShow = ref(5) // Ez fog változni a képernyőmérettől

const getCsrfToken = () => {
  if (process.server) return null
  const match = document.cookie.match(new RegExp('(^| )XSRF-TOKEN=([^;]+)'))
  return match ? decodeURIComponent(match[2]) : null
}

// Képernyőméret figyelése a napok számához
const updateLayout = () => {
  if (process.client) {
    daysToShow.value = window.innerWidth < 768 ? 2 : 5
  }
}

// --- Adatok lekérése ---
const { data: response, refresh } = await useFetch(`${apiBase}/api/user_public_data`, {
  credentials: 'include',
  server: false,
  lazy: true,
  headers: {
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest'
  }
})

watch(response, (newVal) => {
  if (newVal && newVal.data) {
    const currentUser = newVal.data[0] 
    appointments.value = currentUser?.appointments || []
    loading.value = false
  }
}, { immediate: true })

// --- Naptár Logika ---
const calendarDays = ref([])
const generateCalendar = () => {
  const days = []
  const today = new Date()
  const start = new Date()
  start.setDate(today.getDate() - 2)
  
  for (let i = 0; i < 60; i++) {
    const d = new Date(start)
    d.setDate(d.getDate() + i)
    const y = d.getFullYear()
    const m = String(d.getMonth() + 1).padStart(2, '0')
    const day = String(d.getDate()).padStart(2, '0')
    const dateStr = `${y}-${m}-${day}`

    days.push({
      dateStr: dateStr,
      dayName: d.toLocaleDateString('hu-HU', { weekday: 'short' }),
      dayNum: d.getDate(),
      isToday: d.toDateString() === today.toDateString()
    })
  }
  calendarDays.value = days
}

const timeSlots = computed(() => {
  const slots = []
  for (let hour = 8; hour < 20; hour++) {
    for (let min of ['00', '15', '30', '45']) {
      slots.push(`${hour.toString().padStart(2, '0')}:${min}`)
    }
  }
  return slots
})

// Dinamikus szeletelés a daysToShow alapján
const visibleDays = computed(() => calendarDays.value.slice(startIndex.value, startIndex.value + daysToShow.value))

const nextDays = () => { 
  if (startIndex.value + daysToShow.value < calendarDays.value.length) {
    startIndex.value += daysToShow.value 
  }
}
const prevDays = () => { 
  if (startIndex.value - daysToShow.value >= 0) {
    startIndex.value -= daysToShow.value 
  }
}

const getBookingsForDay = (dateStr) => {
  return appointments.value.filter(app => {
    const appDate = app.appointment_from.split(/[\sT]/)[0]
    return appDate === dateStr
  })
}

const calculateBookingStyle = (appointment) => {
  const datePart = appointment.appointment_from.replace(' ', 'T')
  const endPart = appointment.appointment_to.replace(' ', 'T')
  const start = new Date(datePart)
  const end = new Date(endPart)
  const startMin = (start.getHours() * 60) + start.getMinutes()
  const offset = startMin - (8 * 60)
  const duration = (end - start) / 1000 / 60
  const pxPerMin = 50 / 15 

  return {
    top: `${offset * pxPerMin}px`,
    height: `${duration * pxPerMin}px`
  }
}

const deleteBooking = async (id) => {
  if (!confirm('Biztosan törölni szeretnéd?')) return
  try {
    await $fetch(`${apiBase}/api/appointments/${id}`, {
      method: 'DELETE',
      credentials: 'include',
      headers: { 'X-XSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' }
    })
    appointments.value = appointments.value.filter(a => a.id !== id)
    await refresh()
  } catch (err) { alert('Hiba történt.') }
}

onMounted(() => {
  generateCalendar()
  updateLayout()
  window.addEventListener('resize', updateLayout)
})

onUnmounted(() => {
  window.removeEventListener('resize', updateLayout)
})
</script>

<template>
  <div class="min-h-screen bg-slate-50 p-2 md:p-4 font-sans text-slate-900">
    <div class="max-w-7xl mx-auto">
      
      <div class="mb-4 flex justify-between items-center px-2">
        <div class="flex items-center gap-3">
          <NuxtLink to="/dashboard" 
            class="p-2 bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 transition-colors text-slate-600 group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:-translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
          </NuxtLink>
          <h1 class="text-lg md:text-xl font-bold text-slate-800">Saját naptár</h1>
        </div>

        <div class="bg-white border rounded-lg px-3 py-1 text-[10px] md:text-sm font-medium text-slate-500 shadow-sm">
          {{ visibleDays[0]?.dateStr }} - {{ visibleDays[daysToShow - 1]?.dateStr }}
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-md border border-slate-200 flex flex-col h-[82vh] overflow-hidden">
        
        <div class="grid grid-cols-[60px_1fr] md:grid-cols-[100px_1fr] border-b border-slate-200 bg-slate-50 z-30">
          <div class="flex items-center justify-center border-r border-slate-200 gap-1 bg-slate-100/50">
            <button @click="prevDays" class="p-1 md:p-2 hover:bg-white rounded-full transition-all text-slate-500">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" />
              </svg>
            </button>
            <button @click="nextDays" class="p-1 md:p-2 hover:bg-white rounded-full transition-all text-slate-500">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" />
              </svg>
            </button>
          </div>

          <div class="grid divide-x divide-slate-200" :style="{ gridTemplateColumns: `repeat(${daysToShow}, minmax(0, 1fr))` }">
            <div v-for="day in visibleDays" :key="day.dateStr" 
                 class="py-2 md:py-3 text-center flex flex-col items-center justify-center relative"
                 :class="day.isToday ? 'bg-indigo-50/50' : ''">
              <span class="text-[9px] md:text-[10px] uppercase font-bold text-slate-400">{{ day.dayName }}</span>
              <span class="text-lg md:text-xl font-black" :class="day.isToday ? 'text-indigo-600' : 'text-slate-700'">{{ day.dayNum }}</span>
              <div v-if="day.isToday" class="absolute bottom-0 left-0 right-0 h-1 bg-indigo-500"></div>
            </div>
          </div>
        </div>

        <div class="flex-1 overflow-y-auto relative bg-white">
          <div class="grid grid-cols-[60px_1fr] md:grid-cols-[100px_1fr] min-w-full">
            
            <div class="bg-slate-50/50 border-r border-slate-200 z-10">
              <div v-for="time in timeSlots" :key="time" 
                   class="h-[50px] border-b border-slate-100 flex items-start justify-center text-[10px] md:text-[11px] text-slate-400 pt-2">
                {{ time }}
              </div>
            </div>

            <div class="relative grid divide-x divide-slate-100" :style="{ gridTemplateColumns: `repeat(${daysToShow}, minmax(0, 1fr))` }">
              <div class="absolute inset-0 pointer-events-none">
                <div v-for="time in timeSlots" :key="'line-'+time" class="h-[50px] border-b border-slate-50 w-full"></div>
              </div>

              <div v-for="day in visibleDays" :key="'col-'+day.dateStr" class="relative h-[2400px]">
                <div v-for="app in getBookingsForDay(day.dateStr)" :key="app.id"
                     class="absolute left-0.5 right-0.5 md:left-1 md:right-1 bg-indigo-50 border-l-2 md:border-l-4 border-indigo-500 rounded p-1 md:p-1.5 shadow-sm group hover:z-20 transition-all border border-indigo-100 overflow-hidden"
                     :style="calculateBookingStyle(app)">
                  
                  <button @click.stop.prevent="deleteBooking(app.id)"
                          class="absolute top-0.5 right-0.5 bg-white/90 p-1 rounded opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity z-30 shadow-sm border border-rose-100 text-rose-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-2 w-2 md:h-3 md:w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>

                  <div class="text-[9px] md:text-[11px] font-bold text-indigo-950 truncate leading-tight mb-1">
                    {{ app.service }}
                  </div>

                  <div class="text-[8px] md:text-[9px] font-mono font-bold text-indigo-600 flex items-center gap-1">
                    {{ app.appointment_from.split(/[\sT]/)[1]?.substring(0,5) }} - {{ app.appointment_to.split(/[\sT]/)[1]?.substring(0,5) }}
                  </div>

                  <div class="text-[8px] md:text-[10px] text-indigo-800/80 truncate font-medium mt-1">
                    {{ app.customer?.name || 'Vendég' }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.overflow-y-auto {
  scrollbar-width: thin;
  scrollbar-color: #e2e8f0 transparent;
}
/* Eltüntetjük az alapértelmezett görgetősávot mobilon a tisztább képért */
@media (max-width: 768px) {
  .overflow-y-auto::-webkit-scrollbar {
    width: 2px;
  }
}
</style>