<script setup>
definePageMeta({ middleware: 'auth' })

const config = useRuntimeConfig()
const apiBase = config.public.apiBase

// --- Állapotkezelés ---
const loading = ref(true)
const appointments = ref([])
const startIndex = ref(2) 
const daysToShow = ref(5)
const scrollContainer = ref(null) // Ref a görgetéshez

// --- MODÁL ÁLLAPOT ---
const isEditModalOpen = ref(false)
const isSaving = ref(false)
const editingAppointment = ref({
  id: null,
  name: '',
  email: '',
  phone_number: '',
  service: '',
  start: '',
  end: ''
})

// --- Szolgáltatások állapota ---
const services = ref([])

// --- Szolgáltatások lekérése ---
const fetchServices = async () => {
  try {
    const res = await $fetch(`${apiBase}/api/services`, { credentials: 'include' })
    // A Laravel response felépítésétől függően: res.data vagy simán res
    services.value = res.data || res
  } catch (err) {
    console.error("Hiba a szolgáltatások betöltésekor:", err)
  }
}

// --- Automatikus időszámítás ---
// Figyeljük, ha változik a kiválasztott szolgáltatás vagy a kezdési idő
watch(() => [editingAppointment.value.service, editingAppointment.value.start], ([newService, newStart]) => {
  if (newService && newStart) {
    const selectedService = services.value.find(s => s.name === newService)
    if (selectedService) {
      const startDate = new Date(newStart)
      // Hozzáadjuk a szolgáltatás időtartamát (percekben)
      const endDate = new Date(startDate.getTime() + selectedService.time * 60000)
      
      // Visszaalakítjuk ISO formátumba az input számára (YYYY-MM-DDTHH:mm)
      const year = endDate.getFullYear()
      const month = String(endDate.getMonth() + 1).padStart(2, '0')
      const day = String(endDate.getDate()).padStart(2, '0')
      const hours = String(endDate.getHours()).padStart(2, '0')
      const minutes = String(endDate.getMinutes()).padStart(2, '0')
      
      editingAppointment.value.end = `${year}-${month}-${day}T${hours}:${minutes}`
    }
  }
})

const getCsrfToken = () => {
  if (process.server) return null
  const match = document.cookie.match(new RegExp('(^| )XSRF-TOKEN=([^;]+)'))
  return match ? decodeURIComponent(match[2]) : null
}

const updateLayout = () => {
  if (process.client) { daysToShow.value = window.innerWidth < 768 ? 2 : 5 }
}

// --- Automatikus görgetés az aktuális időhöz ---
const scrollToCurrentTime = () => {
  if (!scrollContainer.value) return
  const now = new Date()
  const currentHour = now.getHours()
  if (currentHour >= 8 && currentHour < 20) {
    const minutesSinceEight = ((currentHour - 8) * 60) + now.getMinutes()
    const pxPerMin = 50 / 15
    scrollContainer.value.scrollTo({
      top: (minutesSinceEight * pxPerMin) - 100,
      behavior: 'smooth'
    })
  }
}

// --- Adatok lekérése ---
const { data: response, refresh } = await useFetch(`${apiBase}/api/user_public_data`, {
  credentials: 'include',
  server: false,
  lazy: true,
})

watch(response, (newVal) => {
  if (newVal) {
    const data = newVal.data || newVal
    const currentUser = data[0] 
    appointments.value = currentUser?.appointments || []
    loading.value = false
    // Miután betöltődtek az adatok, görgetünk
    setTimeout(scrollToCurrentTime, 500)
  }
}, { immediate: true })

// --- MODÁL NYITÁS ---
const openCreateModal = (dateStr, timeStr) => {
  const startDateTime = `${dateStr}T${timeStr}`
  const endDate = new Date(`${dateStr}T${timeStr}`)
  endDate.setMinutes(endDate.getMinutes() + 60)
  const endStr = endDate.getHours().toString().padStart(2, '0') + ':' + endDate.getMinutes().toString().padStart(2, '0')
  const endDateTime = `${dateStr}T${endStr}`

  editingAppointment.value = {
    id: null,
    name: '',
    email: '',
    phone_number: '',
    service: '',
    start: startDateTime,
    end: endDateTime
  }
  isEditModalOpen.value = true
}

const openEditModal = (app) => {
  editingAppointment.value = {
    id: app.id,
    name: app.customer?.name || '',
    email: app.customer?.email || '',
    phone_number: app.customer?.phone_number || '',
    service: app.service,
    start: app.appointment_from.replace(' ', 'T').substring(0, 16),
    end: app.appointment_to.replace(' ', 'T').substring(0, 16)
  }
  isEditModalOpen.value = true
}

// --- MENTÉS LOGIKA (Fixált 422 hiba kezelés) ---
const handleSave = async () => {
  if (!editingAppointment.value.phone_number) {
    alert("A telefonszám kötelező!");
    return;
  }

  isSaving.value = true
  const isNew = !editingAppointment.value.id
  const url = isNew ? `${apiBase}/api/appointments` : `${apiBase}/api/appointments/${editingAppointment.value.id}`
  const method = isNew ? 'POST' : 'PUT'

  // Összeállítjuk a payload-ot úgy, hogy a Laravel store ÉS update is értse
  const payload = {
    name: editingAppointment.value.name,
    email: editingAppointment.value.email,
    phone_number: editingAppointment.value.phone_number,
    service: editingAppointment.value.service,
    worker_id: 1, // <--- Ha van auth-od, ide a user id-ja kell!
    start: editingAppointment.value.start.replace('T', ' '),
    end: editingAppointment.value.end.replace('T', ' '),
    appointment_from: editingAppointment.value.start.replace('T', ' '),
    appointment_to: editingAppointment.value.end.replace('T', ' '),
  }

  try {
    await $fetch(url, {
      method: method,
      body: payload,
      credentials: 'include',
      headers: { 
        'X-XSRF-TOKEN': getCsrfToken(), 
        'Accept': 'application/json' 
      }
    })
    
    await refresh()
    isEditModalOpen.value = false
  } catch (err) {
    if (err.status === 422) {
      const errors = err.data?.errors
      const msg = errors ? Object.values(errors).flat().join('\n') : err.data?.message
      alert("Validációs hiba:\n" + (msg || "Ismeretlen hiba"));
    } else {
      alert('Szerver hiba történt a mentés során.');
    }
  } finally {
    isSaving.value = false
  }
}

const deleteBooking = async (id) => {
  if (!confirm('Biztosan törölni szeretnéd ezt a foglalást?')) return
  try {
    await $fetch(`${apiBase}/api/appointments/${id}`, {
      method: 'DELETE',
      credentials: 'include',
      headers: { 'X-XSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' }
    })
    await refresh()
  } catch (err) { 
    alert('Hiba történt a törlés során.') 
  }
}

const calendarDays = ref([])
const generateCalendar = () => {
  const days = []
  const today = new Date()
  const start = new Date()
  start.setDate(today.getDate() - 2)
  for (let i = 0; i < 60; i++) {
    const d = new Date(start); d.setDate(d.getDate() + i)
    days.push({
      dateStr: `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`,
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
    for (let min of ['00', '15', '30', '45']) { slots.push(`${hour.toString().padStart(2, '0')}:${min}`) }
  }
  return slots
})

const visibleDays = computed(() => calendarDays.value.slice(startIndex.value, startIndex.value + daysToShow.value))
const nextDays = () => { if (startIndex.value + daysToShow.value < calendarDays.value.length) startIndex.value += daysToShow.value }
const prevDays = () => { if (startIndex.value - daysToShow.value >= 0) startIndex.value -= daysToShow.value }
const getBookingsForDay = (dateStr) => appointments.value.filter(app => app.appointment_from.split(/[\sT]/)[0] === dateStr)

const calculateBookingStyle = (appointment) => {
  const start = new Date(appointment.appointment_from.replace(' ', 'T'))
  const end = new Date(appointment.appointment_to.replace(' ', 'T'))
  const startMin = (start.getHours() * 60) + start.getMinutes()
  const offset = startMin - (8 * 60)
  const duration = (end - start) / 1000 / 60
  const pxPerMin = 50 / 15 
  return { top: `${offset * pxPerMin}px`, height: `${duration * pxPerMin}px` }
}

onMounted(() => { 
  generateCalendar()
  updateLayout()
  fetchServices()
  window.addEventListener('resize', updateLayout)
})
</script>

<template>
  <div class="min-h-screen bg-slate-50 p-2 md:p-4 font-sans text-slate-900">
    <div class="max-w-7xl mx-auto">
      
      <div class="mb-4 flex justify-between items-center px-2">
        <div class="flex items-center gap-3">
          <NuxtLink to="/dashboard" class="p-2 bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 transition-colors text-slate-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
          </NuxtLink>
          <h1 class="text-lg md:text-xl font-bold text-slate-800">Saját naptár</h1>
        </div>
        <div class="bg-white border rounded-lg px-3 py-1 text-xs font-medium text-slate-500 shadow-sm">
          {{ visibleDays[0]?.dateStr }} - {{ visibleDays[daysToShow - 1]?.dateStr }}
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-md border border-slate-200 flex flex-col h-[82vh] overflow-hidden relative">
        <div class="grid grid-cols-[60px_1fr] md:grid-cols-[100px_1fr] border-b border-slate-200 bg-slate-50 z-30">
          <div class="flex items-center justify-center border-r border-slate-200 gap-1 bg-slate-100/50">
            <button @click="prevDays" class="p-1 hover:bg-white rounded-full text-slate-500"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" /></svg></button>
            <button @click="nextDays" class="p-1 hover:bg-white rounded-full text-slate-500"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" /></svg></button>
          </div>
          <div class="grid divide-x divide-slate-200" :style="{ gridTemplateColumns: `repeat(${daysToShow}, minmax(0, 1fr))` }">
            <div v-for="day in visibleDays" :key="day.dateStr" class="py-2 text-center" :class="day.isToday ? 'bg-indigo-50/50' : ''">
              <span class="text-[10px] uppercase font-bold text-slate-400 block">{{ day.dayName }}</span>
              <span class="text-lg font-black" :class="day.isToday ? 'text-indigo-600' : 'text-slate-700'">{{ day.dayNum }}</span>
            </div>
          </div>
        </div>

        <div ref="scrollContainer" class="flex-1 overflow-y-auto relative bg-white">
          <div class="grid grid-cols-[60px_1fr] md:grid-cols-[100px_1fr] min-w-full">
            <div class="bg-slate-50/50 border-r border-slate-200 z-10">
              <div v-for="time in timeSlots" :key="time" class="h-[50px] border-b border-slate-100 text-[10px] text-slate-400 text-center pt-2">{{ time }}</div>
            </div>

            <div class="relative grid divide-x divide-slate-100" :style="{ gridTemplateColumns: `repeat(${daysToShow}, minmax(0, 1fr))` }">
              <div v-for="day in visibleDays" :key="'col-'+day.dateStr" class="relative h-[2400px]">
                <div class="absolute inset-0">
                  <div v-for="time in timeSlots" :key="'slot-'+time" 
                       @click="openCreateModal(day.dateStr, time)"
                       class="h-[50px] border-b border-slate-50 hover:bg-indigo-50/30 cursor-pointer transition-colors">
                  </div>
                </div>

                <div v-for="app in getBookingsForDay(day.dateStr)" :key="app.id"
                     @click.stop="openEditModal(app)"
                     class="absolute left-1 right-1 bg-indigo-50 border-l-4 border-indigo-500 rounded p-1.5 shadow-sm group cursor-pointer hover:bg-indigo-100 transition-all border border-indigo-100 overflow-hidden z-20"
                     :style="calculateBookingStyle(app)">
                  <button @click.stop="deleteBooking(app.id)" class="absolute top-1 right-1 bg-white/90 p-1 rounded opacity-0 group-hover:opacity-100 text-rose-500 shadow-sm hover:bg-rose-50 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                  </button>
                  <div class="text-[11px] font-bold text-indigo-950 truncate">{{ app.service }}</div>
                  <div class="text-[9px] text-indigo-600 font-mono">{{ app.appointment_from.split(/[ T]/)[1].substring(0,5) }} - {{ app.appointment_to.split(/[ T]/)[1].substring(0,5) }}</div>
                  <div class="text-[10px] text-indigo-800/80 truncate font-medium">{{ app.customer?.name }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <Transition name="fade">
        <div v-if="isEditModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
          <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden border border-slate-200">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50">
              <h2 class="text-xl font-black text-slate-800">{{ editingAppointment.id ? 'Szerkesztés' : 'Új foglalás' }}</h2>
              <button @click="isEditModalOpen = false" class="text-slate-400 hover:text-slate-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
              </button>
            </div>
            
            <form @submit.prevent="handleSave" class="p-6 space-y-4">
              <div>
                <label class="block text-[10px] font-black uppercase text-slate-400 mb-1">Vendég Neve</label>
                <input v-model="editingAppointment.name" required placeholder="pl. Kovács János" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl font-bold focus:outline-indigo-500" />
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-[10px] font-black uppercase text-slate-400 mb-1">Email</label>
                  <input v-model="editingAppointment.email" type="email" placeholder="email@pelda.hu" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-indigo-500" />
                </div>
                <div>
                  <label class="block text-[10px] font-black uppercase text-slate-400 mb-1">Telefon</label>
                  <input v-model="editingAppointment.phone_number" required placeholder="+36..." class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-indigo-500" />
                </div>
              </div>

              <div>
                <label class="block text-[10px] font-black uppercase text-indigo-600 mb-1">Szolgáltatás</label>
                <select 
                  v-model="editingAppointment.service" 
                  required 
                  class="w-full p-3 bg-white border-2 border-indigo-100 rounded-xl font-bold focus:ring-2 focus:ring-indigo-500 outline-none appearance-none cursor-pointer"
                >
                  <option value="" disabled selected>Válassz szolgáltatást...</option>
                  <option v-for="service in services" :key="service.id" :value="service.name">
                    {{ service.name }} ({{ service.time }} perc)
                  </option>
                </select>
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-[10px] font-black uppercase text-indigo-600 mb-1">Kezdés</label>
                  <input type="datetime-local" v-model="editingAppointment.start" required class="w-full p-2 text-sm border border-slate-200 rounded-lg focus:outline-indigo-500" />
                </div>
                <div>
                  <label class="block text-[10px] font-black uppercase text-indigo-600 mb-1">Vége</label>
                  <input type="datetime-local" v-model="editingAppointment.end" required class="w-full p-2 text-sm border border-slate-200 rounded-lg bg-slate-50" />
                </div>
              </div>

              <div class="pt-2">
                <button type="submit" :disabled="isSaving" class="w-full py-4 bg-indigo-600 text-white font-black rounded-2xl shadow-lg hover:bg-indigo-700 transition-all disabled:opacity-50 flex justify-center items-center gap-2">
                  <span v-if="isSaving" class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></span>
                  {{ isSaving ? 'MENTÉS...' : (editingAppointment.id ? 'MÓDOSÍTÁSOK MENTÉSE' : 'FOGLALÁS LÉTREHOZÁSA') }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </Transition>
    </div>
  </div>
</template>