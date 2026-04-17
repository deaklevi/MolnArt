<template>
  <Transition name="fade">
    <div v-if="isOpen" class="fixed inset-0 z-[110] flex items-end md:items-center justify-center p-0 md:p-4 bg-stone-900/60 backdrop-blur-md" @click.self="$emit('close')">
      
      <div class="hidden md:flex bg-white w-full max-w-5xl rounded-[40px] shadow-2xl overflow-hidden h-[700px] relative">
        <button @click="$emit('close')" class="absolute top-6 right-6 z-20 text-stone-300 hover:text-stone-900 transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>

        <div class="bg-stone-50 p-8 w-5/12 border-r border-stone-100 flex flex-col">
          <h3 class="text-[10px] font-black text-purple-900 uppercase tracking-widest mb-8 border-b border-stone-100 pb-2 inline-block">Időpont kiválasztása</h3>
          <div class="flex justify-between items-center mb-6">
            <span class="font-serif text-xl capitalize">{{ currentMonthDisplay }}</span>
          </div>
          <div class="grid grid-cols-7 gap-1 text-center mb-4">
            <span v-for="d in ['H','K','Sz','Cs','P','Sz','V']" :key="d" class="text-[9px] font-bold text-stone-300 uppercase">{{d}}</span>
          </div>
          <div class="grid grid-cols-7 gap-2 mb-8">
            <div v-for="day in weekDays" :key="day.dateString" 
                 @click="selectedDateObj = day.fullDate"
                 :class="[
                   'aspect-square flex items-center justify-center text-sm rounded-full cursor-pointer transition-all',
                   isSameDay(selectedDateObj, day.fullDate) ? 'bg-purple-900 text-white font-bold shadow-lg' : 'hover:bg-purple-100 text-stone-600'
                 ]">
              {{ day.dayNumber }}
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

        <div class="p-12 w-7/12 flex flex-col bg-white overflow-hidden">
          <h2 class="text-3xl font-serif mb-2 text-gray-950">Szabad időpontok</h2>
          <p class="text-xs text-stone-400 uppercase font-bold tracking-widest mb-10">
            {{ formatDateLabel(selectedDateObj) }} • {{ workerName }}
          </p>
          <div class="flex-1 overflow-y-auto pr-4 custom-scrollbar">
            <div v-if="availableSlots.length > 0" class="grid grid-cols-3 gap-3">
              <button v-for="slot in availableSlots" :key="slot.mins" @click="confirmBooking(slot.label)"
                class="group py-4 rounded-2xl border text-sm font-bold transition-all flex flex-col items-center justify-center gap-1 bg-white border-stone-100 hover:border-purple-900 hover:text-purple-900 shadow-sm hover:shadow-md">
                {{ slot.label }}
              </button>
            </div>
            <div v-else class="py-20 text-center opacity-40 italic font-serif">Erre a napra minden hely elkelt.</div>
          </div>
        </div>
      </div>

      <div class="flex md:hidden bg-white w-full rounded-t-[2.5rem] shadow-2xl flex-col max-h-[90vh] overflow-hidden animate-slide-up">
        <div class="p-6 border-b border-stone-100 flex justify-between items-center bg-stone-50/50">
          <div>
            <h3 class="text-xl font-serif text-gray-900">{{ pendingService?.name }}</h3>
            <p class="text-xs text-purple-700 font-bold uppercase tracking-widest mt-1">{{ workerName }} • {{ pendingService?.time }} perc</p>
          </div>
          <button @click="$emit('close')" class="p-2 bg-stone-200/50 rounded-full"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
        </div>

        <div class="flex gap-3 overflow-x-auto p-6 no-scrollbar border-b border-stone-50">
          <div v-for="date in mobileDates" :key="date.full" @click="selectedDateObj = new Date(date.full)"
               class="flex-shrink-0 w-16 h-20 flex flex-col items-center justify-center rounded-2xl cursor-pointer transition-all border-2"
               :class="isSameDay(selectedDateObj, new Date(date.full)) ? 'bg-purple-900 border-purple-900 text-white shadow-lg' : 'bg-white border-stone-100 text-stone-500'">
            <span class="text-[10px] uppercase font-bold">{{ date.dayName }}</span>
            <span class="text-lg font-serif">{{ date.day }}</span>
          </div>
        </div>

        <div class="flex-1 overflow-y-auto p-6 bg-white">
          <div v-if="availableSlots.length > 0" class="grid grid-cols-3 gap-3">
            <button v-for="slot in availableSlots" :key="slot.mins" @click="confirmBooking(slot.label)"
                    class="py-3 rounded-xl text-sm font-bold border border-stone-100 bg-stone-50 text-gray-700 active:scale-95">
              {{ slot.label }}
            </button>
          </div>
          <div v-else class="text-center py-10 opacity-40 italic">Nincs szabad időpont.</div>
        </div>
      </div>

    </div>
  </Transition>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  isOpen: Boolean,
  pendingService: Object,
  workerName: String,
  existingAppointments: { type: Array, default: () => [] }
});

const emit = defineEmits(['close', 'confirm']);
const selectedDateObj = ref(new Date());

const fixedTimes = ["06:00", "07:00", "08:00", "09:00", "10:00", "11:00", 
  "12:00", "12:30", "13:00", "14:00", "15:00", "16:00", 
  "17:00", "18:00", "19:00", "20:00", "21:00", "22:00", "23:00"];

const timeToMins = (t) => {
  const [h, m] = t.split(':').map(Number);
  return h * 60 + m;
};

const isSameDay = (d1, d2) => d1.toDateString() === d2.toDateString();

// IDŐPONTOK SZŰRÉSE (Mindkét nézethez)
const availableSlots = computed(() => {
  if (!props.pendingService) return [];
  const duration = Number(props.pendingService.time);
  const dateString = selectedDateObj.value.toISOString().split('T')[0];

  return fixedTimes.map(timeStr => {
    const startMins = timeToMins(timeStr);
    const endMins = startMins + duration;
    
    const isBooked = props.existingAppointments.some(app => {
      if (!app.appointment_from.includes(dateString)) return false;
      const appStart = timeToMins(app.appointment_from.split(' ')[1]);
      const appEnd = timeToMins(app.appointment_to.split(' ')[1]);
      return startMins < appEnd && endMins > appStart;
    });

    return isBooked ? null : { label: timeStr, mins: startMins };
  }).filter(slot => slot !== null);
});

// ASZTALI NAPTÁR GENERÁLÁS (35 nap)
const weekDays = computed(() => {
  const days = [];
  const start = new Date();
  start.setDate(start.getDate() - start.getDay() + (start.getDay() === 0 ? -6 : 1));
  for (let i = 0; i < 35; i++) {
    const d = new Date(start);
    d.setDate(d.getDate() + i);
    days.push({ dayNumber: d.getDate(), dateString: d.toISOString().split('T')[0], fullDate: d });
  }
  return days;
});

// MOBIL DÁTUMVÁLASZTÓ GENERÁLÁS (14 nap)
const mobileDates = computed(() => {
  const dates = [];
  const daysShort = ['Vas', 'Hét', 'Ked', 'Sze', 'Csü', 'Pén', 'Szo'];
  for (let i = 0; i < 14; i++) {
    const d = new Date();
    d.setDate(d.getDate() + i);
    dates.push({ full: d.toISOString().split('T')[0], day: d.getDate(), dayName: daysShort[d.getDay()] });
  }
  return dates;
});

const currentMonthDisplay = computed(() => selectedDateObj.value.toLocaleDateString('hu-HU', { month: 'long', year: 'numeric' }));
const formatDateLabel = (d) => d.toLocaleDateString('hu-HU', { month: 'long', day: 'numeric' });

const confirmBooking = (timeLabel) => {
  const datePart = selectedDateObj.value.toISOString().split('T')[0];
  emit('confirm', { ...props.pendingService, startTime: `${datePart} ${timeLabel}` });
};
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