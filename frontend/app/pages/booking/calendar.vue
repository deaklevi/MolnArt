<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRoute, useRouter } from 'vue-router'; // Add hozzá a useRoute-ot

const config = useRuntimeConfig();
const router = useRouter();

const route = useRoute(); // Ez kell az URL paraméterekhez

const loginWithGoogle = () => {
  if (!selectedSlot.value.start) {
    errorMessage.value = "Kérlek, előbb válassz egy időpontot!";
    return;
  }
  
  // Elmentjük, hogy mit választott a júzer, mielőtt elmegy a Google-höz
  const slotToSave = {
    start: selectedSlot.value.start,
    end: selectedSlot.value.end,
    display: selectedSlot.value.display
  };
  
  sessionStorage.setItem('last_selected_slot', JSON.stringify(slotToSave));
  
  // Átirányítás a Laravel-re
  window.location.href = `${config.public.apiBase}/api/auth/google/redirect`;
};

// --- Állapotok ---
const bookingInfo = ref(null);
const appointments = ref([]);
const loading = ref(true);
const startIndex = ref(0);
const daysToShow = ref(5);
const showModal = ref(false);
const isSubmitting = ref(false);

// ÚJ: Visszajelzés állapotok
const bookingSuccess = ref(false); // Sikeres-e a foglalás
const errorMessage = ref("");      // Aktuális hibaüzenet szövege

const selectedSlot = ref({ start: '', end: '', display: '' });
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

const updateLayout = () => {
  if (process.client) {
    daysToShow.value = window.innerWidth < 1024 ? 2 : 5;
  }
};

onMounted(async () => {
  updateLayout();
  window.addEventListener('resize', updateLayout);
  
  // 1. Alapadatok betöltése
  const saved = sessionStorage.getItem('temp_booking');
  if (!saved) {
    // Csak akkor dobunk vissza, ha tényleg semmi nincs a kosárban
    return router.push('/booking'); 
  }
  bookingInfo.value = JSON.parse(saved);

  // 2. Időpontok betöltése az API-ból
  try {
    const res = await $fetch(`${config.public.apiBase}/api/user_public_data`);
    const worker = res.data.find(u => u.id === bookingInfo.value.worker.id);
    appointments.value = worker?.appointments || [];
  } catch (err) {
    console.error("Hiba az időpontoknál:", err);
  } finally {
    loading.value = false;
  }

  // 3. Google visszatérés és Modal nyitás
  const googleData = sessionStorage.getItem('google_user_data');
  const lastSlot = sessionStorage.getItem('last_selected_slot');

  if (googleData && lastSlot) {
    const slot = JSON.parse(lastSlot);
    const user = JSON.parse(googleData);
    
    selectedSlot.value = slot;
    customerForm.value.name = user.name;
    customerForm.value.email = user.email;

    // Megnyitjuk a modalt az adatokkal
    showModal.value = true;

    // Takarítás, hogy frissítésnél ne ugorjon fel újra
    sessionStorage.removeItem('google_user_data');
    sessionStorage.removeItem('last_selected_slot');
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
    for (let m of ['00', '15', '30', '45']) {
      slots.push(`${h.toString().padStart(2, '0')}:${m}`);
    }
  }
  return slots;
});

// --- Kiválasztás ---
const selectTime = (day, time) => {
  errorMessage.value = ""; // Alaphelyzetbe állítjuk a hibát
  const duration = Number(bookingInfo.value.totalDuration);
  const startStr = `${day.dateStr}T${time}:00`;
  const startDate = new Date(startStr);
  const endDate = new Date(startDate.getTime() + duration * 60000);
  
  if (startDate < new Date()) {
    errorMessage.value = "Múltbéli időpont nem foglalható!";
    setTimeout(() => errorMessage.value = "", 3000);
    return;
  }

  const hasOverlap = appointments.value.some(app => {
    if (!app.appointment_from.startsWith(day.dateStr)) return false;
    const aStart = toDate(app.appointment_from);
    let aEnd = toDate(app.appointment_to);
    if (aStart.getTime() === aEnd.getTime()) aEnd = new Date(aStart.getTime() + 15 * 60000);
    return (startDate < aEnd && endDate > aStart);
  });

  if (hasOverlap) {
    // Itt nem alertet dobunk, hanem egy szép üzenetet
    errorMessage.value = "Hoppá! Ez az időpont már foglalt.";
    setTimeout(() => errorMessage.value = "", 3000);
    return;
  }

  selectedSlot.value = {
    start: formatToMySQL(startDate),
    end: formatToMySQL(endDate),
    display: `${day.dateStr} ${time}`
  };
  showModal.value = true;
};

// --- Beküldés ---
const confirmBooking = async () => {
  if (!customerForm.value.name || !customerForm.value.email || !customerForm.value.phone_number) {
    errorMessage.value = "Kérlek, tölts ki minden mezőt!";
    return;
  }
  
  isSubmitting.value = true;
  errorMessage.value = "";

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
      headers: { 
        'Accept': 'application/json',
        'Content-Type': 'application/json' 
      },
      body: payload
    });

    bookingSuccess.value = true;
    sessionStorage.removeItem('temp_booking'); // Csak siker esetén ürítjük!
    
    setTimeout(() => {
      router.push('/');
    }, 2500);

  } catch (err) {
    console.error("Szerver hiba:", err);
    // Ha a Laravel 422-t küld (validációs hiba), írjuk ki a konkrét okot
    if (err.data?.errors) {
      errorMessage.value = Object.values(err.data.errors).flat()[0];
    } else {
      errorMessage.value = err.data?.message || "Hiba történt a mentés során. Próbáld újra!";
    }
  } finally {
    isSubmitting.value = false;
  }
};

const calculateBusyStyle = (app) => {
  const start = toDate(app.appointment_from);
  let end = toDate(app.appointment_to);
  if (start.getTime() === end.getTime()) end = new Date(start.getTime() + 15 * 60000);
  const offsetMinutes = ((start.getHours() * 60) + start.getMinutes()) - (8 * 60);
  const durationMinutes = (end - start) / 60000;
  const pxPerMin = 50 / 15; 
  return { top: `${offsetMinutes * pxPerMin}px`, height: `${durationMinutes * pxPerMin}px` };
};
</script>

<template>
  <div class="min-h-screen bg-stone-50 p-2 md:p-6 font-sans">
    <Transition name="fade">
        <div v-if="errorMessage && !showModal" class="fixed top-10 left-1/2 -translate-x-1/2 z-[200] bg-red-600 text-white px-6 py-3 rounded-full shadow-2xl font-bold flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
            {{ errorMessage }}
        </div>
    </Transition>

    <div v-if="bookingInfo" class="max-w-7xl mx-auto">
      <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4 px-2">
        <div class="flex items-center gap-4">
          <NuxtLink to="/booking" class="p-2 bg-white border border-stone-200 rounded-full shadow-sm hover:bg-stone-100 transition-colors text-stone-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
          </NuxtLink>
          <div>
            <h1 class="text-xl md:text-2xl font-serif text-gray-900 uppercase tracking-widest">{{ bookingInfo.worker.name }}</h1>
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-purple-900">Időpontválasztás</p>
          </div>
        </div>
        <div class="bg-purple-900 text-white px-6 py-3 rounded-2xl shadow-lg flex items-center gap-3">
          <span class="text-xs font-black uppercase tracking-widest">{{ bookingInfo.totalDuration }} perc</span>
          <div class="w-[1px] h-4 bg-purple-400"></div>
          <span class="text-[10px] font-medium opacity-80 uppercase tracking-tight">Összesen</span>
        </div>
      </div>

      <div class="bg-white rounded-[2.5rem] shadow-xl border border-stone-200 overflow-hidden flex flex-col h-[75vh]">
        <div class="grid grid-cols-[70px_1fr] md:grid-cols-[100px_1fr] border-b border-stone-200 bg-stone-50/50">
          <div class="flex items-center justify-center border-r border-stone-200 gap-1">
            <button @click="startIndex = Math.max(0, startIndex - 1)" :disabled="startIndex === 0" class="p-2 hover:bg-white rounded-full disabled:opacity-30">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-900" viewBox="0 0 20 20" fill="currentColor"><path d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" /></svg>
            </button>
            <button @click="startIndex = Math.min(25, startIndex + 1)" :disabled="startIndex + daysToShow >= 30" class="p-2 hover:bg-white rounded-full disabled:opacity-30">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-900" viewBox="0 0 20 20" fill="currentColor"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" /></svg>
            </button>
          </div>
          <div class="grid divide-x divide-stone-100" :style="{ gridTemplateColumns: `repeat(${daysToShow}, 1fr)` }">
            <div v-for="day in visibleDays" :key="day.dateStr" class="py-4 text-center">
              <div class="text-[10px] uppercase font-black tracking-widest text-stone-400 mb-1">{{ day.dayName }}</div>
              <div class="text-xl font-serif font-bold text-stone-800">{{ day.dayNum }}</div>
            </div>
          </div>
        </div>

        <div class="flex-1 overflow-y-auto relative bg-white scroll-smooth">
          <div class="grid grid-cols-[70px_1fr] md:grid-cols-[100px_1fr]">
            <div class="border-r border-stone-100 bg-stone-50/30">
              <div v-for="time in timeSlots" :key="time" class="h-[50px] border-b border-stone-50 flex items-start justify-center pt-2">
                <span class="text-[11px] font-mono text-stone-400">{{ time }}</span>
              </div>
            </div>
            <div class="relative grid divide-x divide-stone-100" :style="{ gridTemplateColumns: `repeat(${daysToShow}, 1fr)` }">
              <div v-for="day in visibleDays" :key="'col-'+day.dateStr" class="relative h-[2400px]">
                <div v-for="time in timeSlots" :key="time" @click="selectTime(day, time)" class="h-[50px] border-b border-stone-50 hover:bg-purple-50 cursor-pointer transition-colors"></div>
                <div v-for="app in appointments.filter(a => a.appointment_from.startsWith(day.dateStr))" :key="app.id" class="absolute left-1 right-1 bg-stone-100 border-l-4 border-stone-400 opacity-90 rounded-r shadow-sm flex items-center px-2 overflow-hidden pointer-events-none" :style="calculateBusyStyle(app)">
                  <span class="text-[9px] font-black text-stone-500 uppercase">Foglalt</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="showModal" class="fixed inset-0 bg-stone-900/80 backdrop-blur-md flex items-center justify-center z-[100] p-4">
      <div class="bg-white rounded-[2.5rem] p-8 max-w-md w-full shadow-2xl relative overflow-hidden transition-all duration-500">
        <div class="absolute top-0 left-0 w-full h-2 bg-purple-900"></div>
        
        <div v-if="bookingSuccess" class="py-12 text-center animate-in fade-in zoom-in duration-300">
            <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
            </div>
            <h3 class="text-3xl font-serif text-stone-900 mb-2">Sikeres foglalás!</h3>
            <p class="text-stone-500 font-medium">Várunk szeretettel a megadott időpontban.</p>
            <p class="mt-8 text-[10px] font-black uppercase text-stone-300 tracking-[0.3em]">Visszairányítás...</p>
        </div>

        <div v-else>
            <h3 class="text-2xl font-serif text-stone-900 mb-2 mt-4">Adatok megadása</h3>
            <p class="text-[11px] font-bold text-purple-900 uppercase tracking-[0.2em] mb-4 border-b border-stone-100 pb-4">
              {{ selectedSlot.display }}
            </p>
          
            <div class="mb-6">
              <button @click="loginWithGoogle" class="w-full flex items-center justify-center gap-3 p-4 bg-stone-50 border border-stone-200 rounded-2xl hover:bg-stone-100 transition-all font-bold text-stone-700 shadow-sm group">
                <img src="https://www.svgrepo.com/show/355037/google.svg" class="w-5 h-5 group-hover:scale-110 transition-transform" alt="Google logo">
                Kitöltés Google fiókkal
              </button>
              <div class="relative flex items-center py-4">
                <div class="flex-grow border-t border-stone-100"></div>
                <span class="flex-shrink mx-4 text-[9px] font-black text-stone-300 uppercase tracking-widest">vagy kézzel</span>
                <div class="flex-grow border-t border-stone-100"></div>
              </div>
            </div>

            <div class="space-y-4">
              <input v-model="customerForm.name" placeholder="Teljes név" class="w-full p-4 bg-stone-50 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-purple-900/20 font-medium transition-all">
              <input v-model="customerForm.email" type="email" placeholder="E-mail cím" class="w-full p-4 bg-stone-50 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-purple-900/20 font-medium transition-all">
              <input v-model="customerForm.phone_number" placeholder="Telefonszám" class="w-full p-4 bg-stone-50 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-purple-900/20 font-medium transition-all">
            </div>

            <div v-if="errorMessage" class="mt-4 p-3 bg-red-50 text-red-600 rounded-xl text-xs font-bold text-center animate-bounce">
                {{ errorMessage }}
            </div>

            <div class="grid grid-cols-2 gap-4 mt-10">
              <button @click="showModal = false; errorMessage = ''" class="py-4 font-black uppercase text-[10px] tracking-widest text-stone-400 hover:text-stone-600 transition-colors">Mégse</button>
              <button @click="confirmBooking" :disabled="isSubmitting" 
                      class="py-4 bg-gray-950 text-white rounded-2xl font-black uppercase text-[10px] tracking-widest shadow-xl hover:bg-purple-900 disabled:opacity-50 transition-all flex items-center justify-center gap-2">
                <span v-if="isSubmitting" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                {{ isSubmitting ? 'Küldés...' : 'Véglegesítés' }}
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

/* Animációk */
.fade-enter-active, .fade-leave-active { transition: all 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; transform: translate(-50%, -20px); }

@keyframes spin {
    to { transform: rotate(360deg); }
}
.animate-spin { animation: spin 0.8s linear infinite; }
</style>