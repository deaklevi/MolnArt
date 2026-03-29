<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

const config = useRuntimeConfig();
const router = useRouter();

// --- Állapotok ---
const bookingInfo = ref(null);
const appointments = ref([]);
const loading = ref(true);
const startIndex = ref(0);
const daysToShow = ref(5);
const showModal = ref(false);
const isSubmitting = ref(false);

const selectedSlot = ref({ start: '', end: '' });
const customerForm = ref({ name: '', email: '', phone_number: '' });

// --- Segédfüggvény: Dátum kezelés ---
const toDate = (dateStr) => {
  if (!dateStr) return new Date();
  return new Date(dateStr.replace(' ', 'T'));
};

const updateLayout = () => {
  if (process.client) daysToShow.value = window.innerWidth < 768 ? 2 : 5;
};

onMounted(async () => {
  updateLayout();
  window.addEventListener('resize', updateLayout);
  
  const saved = sessionStorage.getItem('temp_booking');
  if (!saved) return router.push('/booking');
  
  const parsed = JSON.parse(saved);
  // Kényszerítsük a duration-t számmá, és ha nincs meg, legyen 30 perc alapértelmezett
  parsed.totalDuration = Number(parsed.totalDuration) || 30;
  bookingInfo.value = parsed;

  try {
    const res = await $fetch(`${config.public.apiBase}/api/user_public_data`);
    const worker = res.data.find(u => u.id === bookingInfo.value.worker.id);
    appointments.value = worker?.appointments || [];
  } catch (err) {
    console.error("Adatlekérési hiba:", err);
  } finally {
    loading.value = false;
  }
});

onUnmounted(() => window.removeEventListener('resize', updateLayout));

// --- Naptár logika ---
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

const timeSlots = computed(() => {
  const slots = [];
  for (let h = 8; h < 20; h++) {
    for (let m of ['00', '15', '30', '45']) slots.push(`${h.toString().padStart(2, '0')}:${m}`);
  }
  return slots;
});

// --- KATTINTÁS ÉS ÜTKÖZÉS ---
const selectTime = (day, time) => {
  // Mindig vegyük az aktuális duration-t
  const duration = Number(bookingInfo.value.totalDuration) || 30;
  
  const startStr = `${day.dateStr}T${time}:00`;
  const startDate = new Date(startStr);
  const endDate = new Date(startDate.getTime() + duration * 60000);
  
  if (startDate < new Date()) {
    alert("Múltbéli időpont nem foglalható!");
    return;
  }

  // Szigorú ütközésvizsgálat
  const hasOverlap = appointments.value.some(app => {
    if (!app.appointment_from.startsWith(day.dateStr)) return false;
    
    const aStart = toDate(app.appointment_from);
    const aEnd = toDate(app.appointment_to);
    
    // Ha az adatbázisban 0 perces hiba van (Start == End), kezeljük le:
    const safeEnd = aStart.getTime() === aEnd.getTime() 
                    ? new Date(aStart.getTime() + 15 * 60000) 
                    : aEnd;

    return (startDate < safeEnd && endDate > aStart);
  });

  if (hasOverlap) {
    alert("Ez az időpont már foglalt vagy ütközik!");
    return;
  }

  // Laravel formátum: YYYY-MM-DD HH:mm:ss
  selectedSlot.value = {
    start: startStr.replace('T', ' '),
    end: endDate.toISOString().replace('T', ' ').substring(0, 19)
  };
  showModal.value = true;
};

// --- BEKÜLDÉS ---
const confirmBooking = async () => {
  if (!customerForm.value.name || !customerForm.value.email || !customerForm.value.phone_number) {
    alert("Minden mező kitöltése kötelező!");
    return;
  }
  
  isSubmitting.value = true;
  try {
    const payload = {
      name: customerForm.value.name,
      email: customerForm.value.email,
      phone_number: customerForm.value.phone_number,
      worker_id: bookingInfo.value.worker.id,
      service: bookingInfo.value.services.map(s => `${s.name} (${s.quantity}x)`).join(', '),
      start: selectedSlot.value.start,
      end: selectedSlot.value.end
    };

    await $fetch(`${config.public.apiBase}/api/appointments`, {
      method: 'POST',
      headers: { 'Accept': 'application/json' },
      body: payload
    });

    alert("Sikeres foglalás!");
    sessionStorage.removeItem('temp_booking');
    router.push('/');
  } catch (err) {
    const errorData = err.data?.errors;
    let msg = "Hiba történt:";
    if (errorData) {
      msg = Object.values(errorData).flat().join('\n');
    }
    alert(msg);
  } finally {
    isSubmitting.value = false;
  }
};

const calculateBusyStyle = (app) => {
  const start = toDate(app.appointment_from);
  let end = toDate(app.appointment_to);
  
  // Ha 0 perces a foglalás a DB-ben, mutassunk legalább 15 percet, hogy látszódjon
  if (start.getTime() === end.getTime()) {
    end = new Date(start.getTime() + 15 * 60000);
  }

  const offsetMinutes = ((start.getHours() * 60) + start.getMinutes()) - (8 * 60);
  const durationMinutes = (end - start) / 60000;
  const pxPerMin = 50 / 15; 

  return { 
    top: `${offsetMinutes * pxPerMin}px`, 
    height: `${durationMinutes * pxPerMin}px` 
  };
};
</script>

<template>
  <div class="min-h-screen bg-slate-50 p-4 font-sans">
    <div v-if="bookingInfo" class="max-w-7xl mx-auto">
      
      <div class="mb-6 flex justify-between items-center bg-white p-4 rounded-3xl shadow-sm border">
        <div class="flex items-center gap-4">
          <NuxtLink to="/booking" class="p-2 bg-slate-100 rounded-full text-indigo-600">◀</NuxtLink>
          <h1 class="text-xl font-black">{{ bookingInfo.worker.name }}</h1>
        </div>
        <div class="bg-indigo-600 text-white px-4 py-2 rounded-xl font-bold">
          {{ bookingInfo.totalDuration }} perc választva
        </div>
      </div>

      <div class="bg-white rounded-3xl shadow-xl border overflow-hidden flex flex-col h-[70vh]">
        <div class="grid grid-cols-[80px_1fr] bg-slate-50 border-b">
          <div class="flex items-center justify-center border-r">
            <button @click="startIndex = Math.max(0, startIndex - 1)" class="p-2">◀</button>
            <button @click="startIndex = Math.min(25, startIndex + 1)" class="p-2">▶</button>
          </div>
          <div class="grid divide-x" :style="{ gridTemplateColumns: `repeat(${daysToShow}, 1fr)` }">
            <div v-for="day in visibleDays" :key="day.dateStr" class="py-3 text-center">
              <div class="text-[10px] uppercase font-bold text-indigo-400">{{ day.dayName }}</div>
              <div class="text-lg font-black">{{ day.dayNum }}</div>
            </div>
          </div>
        </div>

        <div class="flex-1 overflow-y-auto relative scrollbar-thin">
          <div class="grid grid-cols-[80px_1fr]">
            <div class="border-r bg-slate-50/50">
              <div v-for="time in timeSlots" :key="time" class="h-[50px] border-b text-[10px] text-center pt-2 text-slate-400">
                {{ time }}
              </div>
            </div>

            <div class="relative grid divide-x" :style="{ gridTemplateColumns: `repeat(${daysToShow}, 1fr)` }">
              <div v-for="day in visibleDays" :key="day.dateStr" class="relative h-[2400px]">
                <div v-for="time in timeSlots" :key="time" 
                     @click="selectTime(day, time)" 
                     class="h-[50px] border-b hover:bg-indigo-50 cursor-pointer transition-colors">
                </div>
                <div v-for="app in appointments.filter(a => a.appointment_from.startsWith(day.dateStr))" 
                     :key="app.id"
                     class="absolute left-1 right-1 bg-slate-200 border-l-4 border-slate-400 z-10 opacity-90 rounded-r flex items-center px-2 overflow-hidden"
                     :style="calculateBusyStyle(app)">
                  <span class="text-[9px] font-bold text-slate-500 uppercase">Foglalt</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="showModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur flex items-center justify-center z-[100] p-4">
      <div class="bg-white rounded-[2rem] p-8 max-w-md w-full shadow-2xl">
        <h3 class="text-2xl font-black mb-1">Foglalás</h3>
        <p class="text-indigo-600 font-bold mb-6">{{ selectedSlot.start }}</p>

        <div class="space-y-4">
          <input v-model="customerForm.name" placeholder="Név" class="w-full p-4 bg-slate-50 border rounded-2xl outline-indigo-500">
          <input v-model="customerForm.email" placeholder="Email" class="w-full p-4 bg-slate-50 border rounded-2xl outline-indigo-500">
          <input v-model="customerForm.phone_number" placeholder="Telefonszám" class="w-full p-4 bg-slate-50 border rounded-2xl outline-indigo-500">
        </div>

        <div class="grid grid-cols-2 gap-4 mt-8">
          <button @click="showModal = false" class="py-4 font-bold text-slate-400">Mégse</button>
          <button @click="confirmBooking" :disabled="isSubmitting" class="py-4 bg-indigo-600 text-white rounded-2xl font-black shadow-lg disabled:opacity-50">
            {{ isSubmitting ? 'Küldés...' : 'Megerősítés' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>