<template>
  <Transition name="fade">
    <div v-if="isOpen" class="fixed inset-0 z-[110] flex items-end md:items-center justify-center p-0 md:p-4 bg-stone-900/60 backdrop-blur-md"@click.self="$emit('close')">
      <!-- ── DESKTOP ── -->
      <div class="hidden md:flex bg-white w-full max-w-5xl rounded-[40px] shadow-2xl overflow-hidden h-[700px] relative">
        <button @click="$emit('close')" class="absolute top-6 right-6 z-20 text-stone-300 hover:text-stone-900 transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>

        <div class="bg-stone-50 p-8 w-5/12 border-r border-stone-100 flex flex-col">
          <h3 class="text-[10px] font-black text-purple-900 uppercase tracking-widest mb-8 border-b border-stone-100 pb-2 inline-block">
            Időpont kiválasztása
          </h3>
          <div class="flex justify-between items-center mb-6 px-2">
            <button @click="prevMonth" class="p-2 hover:bg-stone-100 rounded-full transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </button>
            <div class="flex items-center gap-4">
              <button @click="goToToday" class="text-[10px] font-bold uppercase tracking-widest px-3 py-1 bg-white border border-stone-200 rounded-full hover:bg-stone-100 transition-colors">
                Ma
              </button>
              <span class="font-serif text-lg capitalize">{{ currentMonthDisplay }}</span>
            </div>
            <button @click="nextMonth" class="p-2 hover:bg-stone-100 rounded-full transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </button>
          </div>

          <div class="grid grid-cols-7 gap-2 mb-8">
            <div v-for="(day, index) in daysInMonth" :key="index">
              <div 
                v-if="day"
                @click="selectedDateObj = day.fullDate"
                :class="[
                  'aspect-square flex items-center justify-center text-sm rounded-full cursor-pointer transition-all',
                  isSameDay(selectedDateObj, day.fullDate)
                    ? 'bg-purple-900 text-white font-bold shadow-lg'
                    : 'hover:bg-purple-100 text-stone-600'
                ]"
              >
                {{ day.dayNumber }}
              </div>
            </div>
          </div>

          <div class="mt-auto bg-white p-6 rounded-[2rem] border border-stone-200 shadow-sm">
            <p class="text-[9px] font-black uppercase text-purple-900 mb-1 tracking-widest">Kiválasztott munka</p>
            <p class="text-md font-bold text-gray-950">{{ pendingService?.name }}</p>
            <div class="flex items-center gap-2 mt-2">
              <span class="text-xs text-stone-400">{{ pendingService?.time }} perc</span>
            </div>
          </div>
        </div>

        <!-- Desktop right panel: slots -->
        <div class="p-12 w-7/12 flex flex-col bg-white overflow-hidden">
          <h2 class="text-3xl font-serif mb-2 text-gray-950">Szabad időpontok</h2>
          <p class="text-xs text-stone-400 uppercase font-bold tracking-widest mb-10">
            {{ formatDateLabel(selectedDateObj) }} • {{ workerName }}
          </p>
          <div class="flex-1 overflow-y-auto pr-4 custom-scrollbar">
            <div v-if="loading" class="grid grid-cols-3 gap-3">
              <div v-for="n in 9" :key="n" class="h-14 rounded-2xl bg-stone-100 animate-pulse" />
            </div>
            <div v-else-if="filteredSlots.length > 0" class="grid grid-cols-3 gap-3">
              <button v-for="slot in filteredSlots" :key="slot.start" @click="confirmBooking(slot)" class="py-4 rounded-2xl border text-sm font-bold transition-all bg-white border-stone-100 hover:border-purple-900 hover:text-purple-900 shadow-sm hover:shadow-md">
                {{ slot.start }}
              </button>
            </div>
            <div v-else class="py-20 text-center opacity-40 italic font-serif">
              Erre a napra minden hely elkelt.
            </div>
          </div>
        </div>
      </div>

      <!-- ── MOBILE ── -->
      <div class="flex md:hidden bg-white w-full shadow-2xl flex-col h-screen overflow-hidden animate-slide-up">
        <div class="p-6 pb-2">
          <div class="flex justify-between items-center mb-4">
            <div>
              <h3 class="text-xl font-serif text-gray-900">{{ pendingService?.name }}</h3>
              <p class="text-xs text-purple-700 font-bold uppercase tracking-widest mt-1">
                {{ workerName }}
              </p>
            </div>
            <button @click="$emit('close')" class="p-2 bg-stone-100 rounded-full">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <div class="flex justify-between items-center bg-stone-50 rounded-xl p-2">
            <button @click="prevMonth" class="p-2 hover:bg-white rounded-lg transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </button>
            <button @click="goToToday" class="text-[10px] font-black uppercase text-purple-900">
              Ma
            </button>
            <span class="font-serif text-md capitalize font-bold text-gray-900">{{ currentMonthDisplay }}</span>
            <button @click="nextMonth" class="p-2 hover:bg-white rounded-lg transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </button>
          </div>
        </div>

        <div class="flex gap-2 overflow-x-auto p-4 no-scrollbar border-b border-stone-50">
          <div v-for="(day, index) in daysInMonth.filter(d => d !== null)" :key="index" @click="selectedDateObj = day.fullDate" class="flex-shrink-0 w-16 h-20 flex flex-col items-center justify-center rounded-2xl cursor-pointer transition-all border-2" :class="[isSameDay(selectedDateObj, day.fullDate) ? 'bg-purple-900 border-purple-900 text-white shadow-lg' : 'bg-white border-stone-100 text-stone-500']">
            <span class="text-[10px] uppercase font-bold">
              {{ day.fullDate.toLocaleDateString('hu-HU', { weekday: 'short' }) }}
            </span>
            <span class="text-lg font-serif">{{ day.dayNumber }}</span>
          </div>
        </div>

        <div class="flex-1 overflow-y-auto p-6 bg-white">
          <div v-if="loading" class="grid grid-col-1 gap-3">
            <div v-for="n in 6" :key="n" class="h-12 rounded-xl bg-stone-100 animate-pulse" />
          </div>
          <div v-else-if="filteredSlots.length > 0" class="grid grid-cols-1 gap-3">
            <button v-for="slot in filteredSlots" :key="slot.start" @click="confirmBooking(slot)" class="py-3 rounded-xl text-sm font-bold border border-stone-100 bg-stone-50 text-gray-700 active:scale-95 hover:border-purple-900 transition-all">
              {{ slot.start }}
            </button>
          </div>
          <div v-else class="text-center py-10 opacity-40 italic font-serif">
            Nincs szabad időpont erre a napra.
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const goToToday = () => {
  const today = new Date()
  currentViewDate.value = new Date(today.getFullYear(), today.getMonth(), 1)
  selectedDateObj.value = today
}
const props = defineProps({
  isOpen: { type: Boolean, required: true },
  pendingService: { type: Object,  default: null },
  workerName: { type: String,  default: '' },
  workerId: { type: Number,  required: true },
  cartItems: { type: Array,   default: () => [] },
})

const emit = defineEmits(['close', 'confirm'])

const config = useRuntimeConfig()
const selectedDateObj = ref(new Date())
const slots = ref([])
const loading = ref(false)

const timeToMins = (t) => {
  const [h, m] = t.split(':').map(Number)
  return h * 60 + m
}

async function fetchSlots() {
  if (!props.workerId || !props.pendingService) return
  const date = selectedDateObj.value.toISOString().split('T')[0]
  loading.value = true
  slots.value = []
  try {
    const res = await $fetch(`${config.public.apiBase}/api/appointments/slots`, {
      headers: { Accept: 'application/json' },
      params: {
        user_id:  props.workerId,
        date,
        duration: props.pendingService.time,
      },
    })
    slots.value = res.slots
  } catch {
    slots.value = []
  } finally {
    loading.value = false
  }
}

const filteredSlots = computed(() => {
  if (!props.pendingService) return slots.value
  const duration   = Number(props.pendingService.time)
  const dateString = selectedDateObj.value.toISOString().split('T')[0]
  return slots.value.filter(slot => {
    const slotStart = timeToMins(slot.start)
    const slotEnd   = slotStart + duration
    const blockedByCart = props.cartItems.some(cartItem => {
      if (!cartItem.startTime.startsWith(dateString)) return false
      const cartTimePart = cartItem.startTime.split(' ')[1]
      const cartStart = timeToMins(cartTimePart)
      const cartEnd = cartStart + Number(cartItem.time)
      return slotStart < cartEnd && slotEnd > cartStart
    })
    return !blockedByCart
  })
})

watch(selectedDateObj,() => { if (props.isOpen) fetchSlots() })
watch(() => props.isOpen,(open) => { if (open) fetchSlots() },{ immediate: true })
watch(() => props.pendingService,() => { if (props.isOpen) fetchSlots() })

const isSameDay = (d1, d2) => d1.toDateString() === d2.toDateString()

const weekDays = computed(() => {
  const days = []
  const start = new Date()
  start.setDate(start.getDate() - start.getDay() + (start.getDay() === 0 ? -6 : 1))
  

  for (let i = 0; i < 90; i++) { 
    const d = new Date(start)
    d.setDate(d.getDate() + i)
    days.push({ 
      dayNumber: d.getDate(), 
      dateString: d.toISOString().split('T')[0], 
      fullDate: d 
    })
  }
  return days
})

const mobileDates = computed(() => {
  const daysShort = ['Vas', 'Hét', 'Ked', 'Sze', 'Csü', 'Pén', 'Szo']

  return Array.from({ length: 60 }, (_, i) => {
    const d = new Date()
    d.setDate(d.getDate() + i)
    return { 
      full: d.toISOString().split('T')[0], 
      day: d.getDate(), 
      dayName: daysShort[d.getDay()] 
    }
  })
})

const formatDateLabel = (d) =>
  d.toLocaleDateString('hu-HU', { month: 'long', day: 'numeric' })

function confirmBooking(slot) {
  const datePart = selectedDateObj.value.toISOString().split('T')[0]
  emit('confirm', {
    name: props.pendingService.name,
    time: props.pendingService.time,
    price: props.pendingService.price,
    startTime: `${datePart} ${slot.start}:00`,
    endTime: `${datePart} ${slot.end}:00`,
  })
}


const currentViewDate = ref(new Date())

const prevMonth = () => {
  currentViewDate.value = new Date(currentViewDate.value.getFullYear(), currentViewDate.value.getMonth() - 1, 1)
  selectedDateObj.value = new Date(currentViewDate.value)
}

const nextMonth = () => {
  currentViewDate.value = new Date(currentViewDate.value.getFullYear(), currentViewDate.value.getMonth() + 1, 1)
  selectedDateObj.value = new Date(currentViewDate.value)
}

const daysInMonth = computed(() => {
  const year = currentViewDate.value.getFullYear()
  const month = currentViewDate.value.getMonth()
  const firstDay = new Date(year, month, 1).getDay()
  const padding = (firstDay === 0 ? 6 : firstDay - 1)
  
  const days = []
  for (let i = 0; i < padding; i++) {
    days.push(null)
  }

  const daysInMonthCount = new Date(year, month + 1, 0).getDate()
  for (let i = 1; i <= daysInMonthCount; i++) {
    const d = new Date(year, month, i)
    days.push({ 
      dayNumber: i, 
      dateString: d.toISOString().split('T')[0], 
      fullDate: d 
    })
  }
  return days
})

const currentMonthDisplay = computed(() =>
  currentViewDate.value.toLocaleDateString('hu-HU', { month: 'long', year: 'numeric' })
)
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
.animate-slide-up { animation: slide-up 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
@keyframes slide-up { from { transform: translateY(100%); } to { transform: translateY(0); } }
.custom-scrollbar::-webkit-scrollbar { width: 5px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>