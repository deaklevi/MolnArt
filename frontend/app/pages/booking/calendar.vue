<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const config = useRuntimeConfig();
const router = useRouter();
const route = useRoute();

// --- Állapotok ---
const bookingInfo = ref(null);
const appointments = ref([]);
const loading = ref(true);
const startIndex = ref(0);
const daysToShow = ref(5);
const showModal = ref(false);
const isSubmitting = ref(false);
const bookingSuccess = ref(false);
const errorMessage = ref("");
const selectedSlots = ref([]); 

const customerForm = ref({ name: '', email: '', phone_number: '' });

// --- Segédfüggvények ---
const formatToMySQL = (date) => {
  const pad = (n) => n.toString().padStart(2, '0');
  return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())} ${pad(date.getHours())}:${pad(date.getMinutes())}:00`;
};

const toDate = (dateStr) => {
  if (!dateStr) return new Date();
  return new Date(dateStr.replace(' ', 'T'));
};

const isPast = (dayStr, timeStr) => {
  const now = new Date();
  const slotDate = new Date(`${dayStr}T${timeStr}:00`);
  return slotDate < now;
};

// --- KOSÁR MENTÉSE ---
watch(selectedSlots, (newVal) => {
  sessionStorage.setItem('cart_slots', JSON.stringify(newVal));
}, { deep: true });

// --- Google Auth ---
const loginWithGoogle = () => {
  if (selectedSlots.value.length === 0) {
    errorMessage.value = "Kérlek, előbb válassz legalább egy időpontot!";
    return;
  }
  window.location.href = `${config.public.apiBase}/api/auth/google/redirect`;
};

const updateLayout = () => {
  if (process.client) {
    daysToShow.value = window.innerWidth < 1024 ? 2 : 5;
  }
};

// --- Életciklus ---
onMounted(async () => {
  updateLayout();
  window.addEventListener('resize', updateLayout);
  
  const savedCart = sessionStorage.getItem('cart_slots');
  if (savedCart) selectedSlots.value = JSON.parse(savedCart);

  const saved = sessionStorage.getItem('temp_booking');
  if (!saved) return router.push('/booking'); 
  bookingInfo.value = JSON.parse(saved);

  try {
    const res = await $fetch(`${config.public.apiBase}/api/user_public_data`);
    const worker = res.data.find(u => u.id === bookingInfo.value.worker.id);
    appointments.value = worker?.appointments || [];
  } catch (err) { console.error(err); } finally { loading.value = false; }

  const googleData = sessionStorage.getItem('google_user_data');
  if (googleData) {
    const user = JSON.parse(googleData);
    customerForm.value.name = user.name;
    customerForm.value.email = user.email;
    if (selectedSlots.value.length > 0) showModal.value = true;
    sessionStorage.removeItem('google_user_data');
  }
});

onUnmounted(() => window.removeEventListener('resize', updateLayout));

// --- Naptár Logika ---
const timeSlots = computed(() => {
  const slots = [];
  for (let h = 8; h < 20; h++) {
    for (let m of ['00', '15', '30', '45']) slots.push(`${h.toString().padStart(2, '0')}:${m}`);
  }
  return slots;
});

const calendarDays = computed(() => {
  const days = [];
  const today = new Date();
  for (let i = 0; i < 30; i++) {
    const d = new Date(today);
    d.setDate(today.getDate() + i);
    days.push({
      dateStr: d.toISOString().split('T')[0],
      dayName: d.toLocaleDateString('hu-HU', { weekday: 'short' }),
      dayNum: d.getDate()
    });
  }
  return days;
});

const visibleDays = computed(() => calendarDays.value.slice(startIndex.value, startIndex.value + daysToShow.value));

const isBlockSelected = (day, time) => {
  const blockStart = new Date(`${day.dateStr}T${time}:00`).getTime();
  return selectedSlots.value.some(slot => {
    const start = new Date(slot.start.replace(' ', 'T')).getTime();
    const end = new Date(slot.end.replace(' ', 'T')).getTime();
    return blockStart >= start && blockStart < end;
  });
};

// --- ELSŐ SZABAD IDŐPONT KERESÉSE ---
const jumpToFirstFree = () => {
  const duration = Number(bookingInfo.value.totalDuration);
  const now = new Date();

  for (let i = 0; i < calendarDays.value.length; i++) {
    const day = calendarDays.value[i];
    for (const time of timeSlots.value) {
      const startDate = new Date(`${day.dateStr}T${time}:00`);
      if (startDate < now) continue;

      const endDate = new Date(startDate.getTime() + duration * 60000);
      const hasOverlap = appointments.value.some(app => {
        if (!app.appointment_from.startsWith(day.dateStr)) return false;
        const aStart = toDate(app.appointment_from);
        const aEnd = toDate(app.appointment_to);
        return (startDate < aEnd && endDate > aStart);
      });

      if (!hasOverlap) {
        startIndex.value = Math.max(0, Math.min(30 - daysToShow.value, i));
        errorMessage.value = `Találtunk egy szabad helyet: ${day.dateStr} ${time}`;
        setTimeout(() => errorMessage.value = "", 4000);
        return;
      }
    }
  }
  errorMessage.value = "Sajnos nincs szabad hely a következő 30 napban.";
};

const selectTime = (day, time) => {
  errorMessage.value = "";
  
  if (isPast(day.dateStr, time)) {
    errorMessage.value = "Múltbéli időpontra nem tudsz foglalni!";
    return;
  }

  const duration = Number(bookingInfo.value.totalDuration);
  const startStr = `${day.dateStr}T${time}:00`;
  const startDate = new Date(startStr);
  const endDate = new Date(startDate.getTime() + duration * 60000);
  
  const currentServiceStr = bookingInfo.value.services.map(s => `${s.name} (${s.quantity}x)`).join(', ');
  const display = `${day.dateStr} ${time}`;

  const existingIndex = selectedSlots.value.findIndex(s => s.start === formatToMySQL(startDate));
  if (existingIndex !== -1) {
    selectedSlots.value.splice(existingIndex, 1);
    return;
  }

  const hasOverlap = [...appointments.value, ...selectedSlots.value.map(s => ({ appointment_from: s.start, appointment_to: s.end }))].some(app => {
    if (!app.appointment_from.startsWith(day.dateStr)) return false;
    const aStart = toDate(app.appointment_from);
    const aEnd = toDate(app.appointment_to);
    return (startDate < aEnd && endDate > aStart);
  });

  if (hasOverlap) {
    errorMessage.value = "Ez az időpont ütközik egy már foglalt időponttal!";
    return;
  }

  selectedSlots.value.push({ 
    start: formatToMySQL(startDate), 
    end: formatToMySQL(endDate), 
    display,
    worker_id: bookingInfo.value.worker.id,
    service: currentServiceStr
  });
};

const confirmBooking = async () => {
  if (!customerForm.value.name || !customerForm.value.email || !customerForm.value.phone_number) {
    errorMessage.value = "Minden mező kötelező!";
    return;
  }
  isSubmitting.value = true;
  try {
    const promises = selectedSlots.value.map(slot => $fetch(`${config.public.apiBase}/api/appointments`, {
      method: 'POST',
      body: { ...customerForm.value, worker_id: slot.worker_id, service: slot.service, start: slot.start, end: slot.end }
    }));
    await Promise.all(promises);
    bookingSuccess.value = true;
    sessionStorage.removeItem('temp_booking');
    sessionStorage.removeItem('cart_slots');
    selectedSlots.value = [];
  } catch (err) { errorMessage.value = "Szerver hiba történt."; } finally { isSubmitting.value = false; }
};

const calculateBusyStyle = (app) => {
  const start = toDate(app.appointment_from);
  let end = toDate(app.appointment_to);
  const offsetMinutes = ((start.getHours() * 60) + start.getMinutes()) - (8 * 60);
  const durationMinutes = (end - start) / 60000;
  return { top: `${offsetMinutes * (50/15)}px`, height: `${durationMinutes * (50/15)}px` };
};
</script>

<template>
  <div class="min-h-screen bg-stone-50 p-2 md:p-6 font-sans pb-32">
    <div v-if="bookingInfo" class="max-w-7xl mx-auto">
      
      <div class="mb-6 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-3">
          <NuxtLink to="/booking" class="inline-flex items-center gap-2 px-4 py-2 bg-white rounded-xl shadow-sm hover:bg-stone-100 text-stone-700 border border-stone-200 transition-all font-black text-[10px] uppercase tracking-widest">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Vissza
          </NuxtLink>
          
          <button @click="jumpToFirstFree" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-900 text-white rounded-xl shadow-md hover:bg-purple-800 transition-all font-black text-[10px] uppercase tracking-widest">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
            Első szabad hely
          </button>
        </div>

        <div class="lg:text-right">
          <h1 class="text-xl font-serif text-gray-900 uppercase tracking-widest">{{ bookingInfo.worker.name }}</h1>
          <p v-if="errorMessage" class="text-red-500 text-[10px] font-black uppercase animate-pulse">{{ errorMessage }}</p>
          <p v-else class="text-[10px] font-black uppercase text-purple-900">Válassz időpontot</p>
        </div>
      </div>

      <div class="bg-white rounded-[2.5rem] shadow-xl border border-stone-200 overflow-hidden flex flex-col h-[70vh]">
        
        <div class="grid grid-cols-[70px_1fr] md:grid-cols-[100px_1fr] border-b border-stone-200 bg-stone-50/50">
          <div class="flex items-center justify-center border-r border-stone-200 gap-1">
            <button @click="startIndex = Math.max(0, startIndex - 1)" class="p-2 hover:bg-white rounded-full"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" /></svg></button>
            <button @click="startIndex = Math.min(30 - daysToShow, startIndex + 1)" class="p-2 hover:bg-white rounded-full"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" /></svg></button>
          </div>
          <div class="grid divide-x divide-stone-100" :style="{ gridTemplateColumns: `repeat(${daysToShow}, 1fr)` }">
            <div v-for="day in visibleDays" :key="day.dateStr" class="py-4 text-center">
              <div class="text-[10px] uppercase font-black text-stone-400">{{ day.dayName }}</div>
              <div class="text-lg font-bold text-stone-800">{{ day.dayNum }}</div>
            </div>
          </div>
        </div>

        <div class="flex-1 overflow-y-auto relative bg-white">
          <div class="grid grid-cols-[70px_1fr] md:grid-cols-[100px_1fr]">
            <div class="border-r border-stone-100">
              <div v-for="time in timeSlots" :key="time" class="h-[50px] border-b border-stone-50 flex items-start justify-center pt-2">
                <span class="text-[11px] font-mono text-stone-400">{{ time }}</span>
              </div>
            </div>
            
            <div class="relative grid divide-x divide-stone-100" :style="{ gridTemplateColumns: `repeat(${daysToShow}, 1fr)` }">
              <div v-for="day in visibleDays" :key="'col-'+day.dateStr" class="relative h-[2400px]">
                <div v-for="time in timeSlots" :key="time" 
                     @click="selectTime(day, time)" 
                     class="h-[50px] border-b border-stone-50 cursor-pointer transition-all relative"
                     :class="[
                        isBlockSelected(day, time) ? 'bg-purple-600/20 border-l-4 border-l-purple-900' : 'hover:bg-purple-50',
                        isPast(day.dateStr, time) ? 'bg-stone-100/50 !cursor-not-allowed' : ''
                     ]">
                     <div v-if="isPast(day.dateStr, time)" class="absolute inset-0 flex items-center justify-center opacity-20">
                        <div class="w-full h-[1px] bg-stone-300 rotate-12"></div>
                     </div>
                </div>

                <div v-for="app in appointments.filter(a => a.appointment_from.startsWith(day.dateStr))" 
                     :key="app.id" class="absolute left-1 right-1 bg-stone-200 border-l-4 border-stone-400 opacity-90 rounded-r flex items-center px-2 pointer-events-none overflow-hidden z-10" :style="calculateBusyStyle(app)">
                  <span class="text-[9px] font-black text-stone-500 uppercase tracking-tighter">Foglalt</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="selectedSlots.length > 0" class="fixed bottom-8 left-1/2 -translate-x-1/2 z-50 w-[95%] max-w-xl">
       <div class="bg-gray-950 text-white p-4 rounded-3xl shadow-2xl flex items-center justify-between border border-white/10">
          <div class="pl-4">
             <p class="text-[10px] font-black uppercase text-purple-400">Kiválasztva</p>
             <p class="text-sm font-bold">{{ selectedSlots.length }} időpont</p>
          </div>
          <button @click="showModal = true" class="bg-purple-600 hover:bg-purple-500 px-8 py-3 rounded-2xl font-black uppercase text-xs transition-all shadow-lg active:scale-95">
             Tovább
          </button>
       </div>
    </div>

    <div v-if="showModal" class="fixed inset-0 bg-stone-900/80 backdrop-blur-md flex items-center justify-center z-[100] p-4">
      <div class="bg-white rounded-[2.5rem] p-8 max-w-md w-full shadow-2xl relative">
        <div v-if="bookingSuccess" class="py-8 text-center">
            <h3 class="text-3xl font-serif text-stone-900 mb-2">Sikeres!</h3>
            <p class="text-stone-500 mb-8">Köszönjük a foglalást!</p>
            <div class="flex flex-col gap-3">
               <button @click="router.push('/booking'); showModal = false; bookingSuccess = false;" class="w-full py-4 bg-purple-900 text-white rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-purple-800 transition-all">Új foglalás</button>
               <button @click="router.push('/')" class="w-full py-4 bg-stone-100 text-stone-700 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-stone-200 transition-all border border-stone-200">Főoldal</button>
            </div>
        </div>

        <div v-else>
            <h3 class="text-2xl font-serif text-stone-900 mb-4">Adatok megadása</h3>
            <button @click="loginWithGoogle" class="w-full flex items-center justify-center gap-3 p-4 bg-stone-50 border border-stone-200 rounded-2xl hover:bg-stone-100 transition-all font-bold text-stone-700 shadow-sm mb-6 text-sm">
              <img src="https://www.svgrepo.com/show/355037/google.svg" class="w-5 h-5" alt="Google"> Google fiók használata
            </button>

            <div class="max-h-32 overflow-y-auto mb-6 bg-stone-50 p-4 rounded-xl border border-stone-100">
               <div v-for="(slot, index) in selectedSlots" :key="index" class="mb-2 last:mb-0 text-[11px] font-black text-purple-900 uppercase">
                  • {{ slot.display }}
               </div>
            </div>
          
            <div class="space-y-3">
              <input v-model="customerForm.name" placeholder="Név" class="w-full p-4 bg-stone-50 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-purple-900/20">
              <input v-model="customerForm.email" type="email" placeholder="E-mail" class="w-full p-4 bg-stone-50 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-purple-900/20">
              <input v-model="customerForm.phone_number" placeholder="Telefon" class="w-full p-4 bg-stone-50 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-purple-900/20">
            </div>

            <div class="grid grid-cols-2 gap-4 mt-8">
              <button @click="showModal = false" class="py-4 font-black uppercase text-[10px] text-stone-400">Mégse</button>
              <button @click="confirmBooking" :disabled="isSubmitting" class="py-4 bg-gray-950 text-white rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-purple-900 disabled:opacity-50">
                {{ isSubmitting ? 'Küldés...' : 'Foglalás' }}
              </button>
            </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.overflow-y-auto::-webkit-scrollbar { width: 4px; }
.overflow-y-auto::-webkit-scrollbar-thumb { background: #d6d3d1; border-radius: 10px; }
</style>