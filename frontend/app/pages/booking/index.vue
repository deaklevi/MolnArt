<template>
  <div class="reservation-page bg-stone-50 min-h-screen pb-24 md:pb-12">
    <section class="bg-white border-b border-stone-200 shadow-sm top-0 z-30">
      <div class="container mx-auto px-4 py-4 md:py-6">
        <h1 class="text-center text-sm md:text-2xl font-serif tracking-widest text-gray-900 uppercase mb-4 md:mb-6">
          Válassz szakembert
        </h1>
        <div class="flex justify-center gap-4 md:gap-12 overflow-x-auto pb-2 pt-3 no-scrollbar">
          <div v-for="worker in publicUsers?.data" 
               :key="worker.id" 
               @click="selectWorker(worker)" 
               class="flex flex-col items-center cursor-pointer group flex-shrink-0">
            <div class="relative rounded-full p-1.5 transition-all duration-500" :class="selectedWorker?.id === worker.id ? 'ring-4 ring-purple-900/30 ring-offset-2' : 'hover:ring-2 hover:ring-stone-200 hover:ring-offset-1'">
              <img :src="`${config.public.apiBase}${worker.profile_image}`" class="w-16 h-16 md:w-24 md:h-24 rounded-full object-cover shadow-md transition-all duration-300" :class="selectedWorker?.id === worker.id ? 'scale-100' : 'grayscale-[40%]'" />
            </div>
            <span class="mt-2 text-[11px] md:text-sm font-bold uppercase tracking-tight" 
                  :class="selectedWorker?.id === worker.id ? 'text-purple-900' : 'text-gray-500'">
              {{ worker.user_name }}
            </span>
          </div>
        </div>
      </div>
    </section>

    <div class="container mx-auto px-4 py-8 grid grid-cols-1 lg:grid-cols-12 gap-10 max-w-7xl">
      <div class="lg:col-span-8">
        <div class="flex items-center justify-between mb-8 border-b border-stone-200 pb-4">
          <h2 class="text-lg md:text-2xl font-serif tracking-wide text-gray-900 uppercase">
            {{ selectedWorker?.user_name }} szolgáltatásai
          </h2>
          <h2 class="text-sm md:text-lg font-serif tracking-wide text-gray-900 uppercase">
            {{ selectedWorker.services.id }} 
          </h2>
        </div>

        <div v-if="selectedWorker?.services?.length" class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
          <div v-for="service in selectedWorker.services" :key="service.id" 
               @click="openCalendarFor(service)"
               class="cursor-pointer bg-white p-5 md:p-6 border border-stone-100 rounded-2xl shadow-sm hover:shadow-xl transition-all flex flex-col justify-between group">
            
            <div>
              <div class="flex justify-between items-start mb-3">
                <h3 class="font-bold text-base md:text-lg text-gray-900 leading-snug group-hover:text-purple-900 transition-colors">
                  {{ service.name }}
                </h3>
                <div class="flex items-center text-purple-900 text-xs bg-purple-50 px-2 py-1 rounded-md uppercase">
                  {{ service.time }}p
                </div>
              </div>
              <p class="text-stone-500 text-xs md:text-sm mb-6 line-clamp-2">
                {{ service.description || 'Professzionális szolgáltatás.' }}
              </p>
            </div>

            <div class="mt-4 pt-4 border-t border-stone-50 flex items-center justify-between">
              <span class="font-serif text-base md:text-lg text-gray-900">{{ service.price }} Ft</span>
              <div class="flex items-center gap-2 px-4 py-2 rounded-full bg-purple-950 text-white text-[9px] font-black uppercase tracking-widest">
                Időpont →
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="lg:col-span-4">
        <div class="lg:sticky lg:top-5 lg:max-h-[calc(100vh-104px)] space-y-6 pr-1 custom-scrollbar">

          <div v-if="cart.length > 0" class="hidden lg:block bg-white rounded-3xl p-6 shadow-xl border border-purple-100 transition-all">
            <h3 class="text-xs font-black text-purple-900 uppercase tracking-[0.2em] mb-6 border-b border-stone-100 pb-2">Kiválasztott időpontok</h3>
            <div class="space-y-4 mb-8 max-h-[240px] overflow-y-auto">
              <div v-for="(item, index) in cart" :key="item.id" class="flex justify-between items-start bg-stone-50 p-4 rounded-2xl border border-stone-100">
                <div>
                  <p class="font-bold text-sm text-gray-900">{{ item.name }}</p>
                  <p class="text-[10px] text-purple-700 font-bold uppercase mt-1">{{ item.startTime }}</p>
                </div>
                <button @click="cart.splice(index, 1)" class="text-stone-300 hover:text-red-500">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
            </div>
            <div class="pt-4 border-t border-stone-100">
              <div class="flex justify-between items-end mb-6">
                <p class="text-2xl font-serif text-purple-900">{{ totalSum }} Ft</p>
              </div>
              <button @click="handleFinalConfirm" class="w-full bg-purple-900 text-white py-4 rounded-2xl font-black uppercase tracking-widest hover:bg-stone-900 transition-all">
                Foglalás beküldése
              </button>
            </div>
          </div>
          <Teleport to="body">
              <div v-if="isReservationModalOpen" class="fixed inset-0 h-screen w-screen z-[9999] flex items-center justify-center p-4 bg-stone-900/40 backdrop-blur-sm">
              <div class="bg-white rounded-3xl p-8 shadow-2xl w-full max-w-sm border border-stone-100">
                <h3 class="text-lg font-bold text-gray-900 mb-6 uppercase tracking-widest text-center">Foglalás</h3>

                <div class="flex p-1 bg-stone-100 rounded-xl mb-6">
                  <button @click="authMode = 'guest'" 
                          :class="authMode === 'guest' ? 'bg-white shadow' : 'text-stone-400'"
                          class="flex-1 py-2 text-[10px] font-bold uppercase rounded-lg transition-all">
                    Vendégként
                  </button>
                  <button @click="authMode = 'google'" 
                          :class="authMode === 'google' ? 'bg-white shadow' : 'text-stone-400'"
                          class="flex-1 py-2 text-[10px] font-bold uppercase rounded-lg transition-all">
                    Google fiókkal
                  </button>
                </div>
                <div v-if="authMode === 'guest'" class="space-y-4">
                  <div>
                    <label class="block text-[10px] font-bold text-stone-400 uppercase mb-1">Név</label>
                    <input v-model="customerData.name" type="text" placeholder="Teljes név" class="w-full px-4 py-3 rounded-xl border border-stone-200 focus:ring-2 focus:ring-purple-900 outline-none">
                  </div>
                  <div>
                    <label class="block text-[10px] font-bold text-stone-400 uppercase mb-1">Email</label>
                    <input v-model="customerData.email" type="email" placeholder="email@example.hu" class="w-full px-4 py-3 rounded-xl border border-stone-200 focus:ring-2 focus:ring-purple-900 outline-none">
                  </div>
                  <div>
                    <label class="block text-[10px] font-bold text-stone-400 uppercase mb-1">Telefonszám</label>
                    <input v-model="customerData.phone_number" type="text" placeholder="+36201234567" class="w-full px-4 py-3 rounded-xl border border-stone-200 focus:ring-2 focus:ring-purple-900 outline-none">
                  </div>
                </div>

                <div v-else class="py-8 text-center">
                  <button @click="loginWithGoogle" class="w-full flex items-center justify-center gap-3 border border-stone-200 bg-white py-3 rounded-xl hover:bg-stone-50 transition-all mb-4">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" class="w-5 h-5" alt="Google">
                    <span class="text-sm font-bold text-gray-700">Folytatás Google-fiókkal</span>
                  </button>
                </div>

                <div class="flex gap-3 mt-8">
                  <button @click="isReservationModalOpen = false" class="flex-1 py-3 rounded-xl font-bold text-xs border border-bg-stone-100 uppercase text-stone-500 hover:bg-stone-100 transition-all">
                    Mégse
                  </button>
                  
                  <button v-if="authMode === 'guest'" @click="submitReservation" class="flex-1 bg-purple-900 text-white py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-purple-800 transition-all">
                    Foglalás
                  </button>
                </div>
              </div>
            </div>
            
          </Teleport>
          <div v-if="selectedWorker" class="bg-white p-6 md:p-8 rounded-3xl shadow-sm border border-stone-100">
              <h3 class="text-xs font-black text-purple-900 uppercase tracking-widest mb-4">Bemutatkozás</h3>
              <h2 class="text-xl font-serif text-gray-950 mb-3">{{ selectedWorker.user_name }}</h2>
              <p class="text-stone-600 italic text-xs leading-relaxed mb-6">"{{ selectedWorker.description || 'Várlak szeretettel!' }}"</p>
              <h3 class="text-xs font-black text-purple-900 uppercase tracking-widest mb-4 border-t pt-4">Nyitvatartás</h3>
              <div class="space-y-2">
                <div v-for="(hours, day) in schedule" :key="day" class="flex justify-between text-xs">
                  <span class="text-stone-500">{{ day }}</span>
                  <span :class="hours === 'Zárva' ? 'text-red-400' : 'text-gray-900'">{{ hours }}</span>
                </div>
              </div>
            </div>
        </div>
      </div>
    </div>
    
    <div v-if="cart.length > 0" class="lg:hidden fixed bottom-0 left-0 right-0 bg-white shadow-[0_-10px_25px_rgba(0,0,0,0.1)] z-40 rounded-t-[2rem] border-t border-purple-100 p-5 animate-in slide-in-from-bottom duration-300">
      <div class="flex items-center justify-between mb-4">
        <div>
          <p class="text-[10px] uppercase text-stone-400 font-bold tracking-widest">Kiválasztva ({{ cart.length }})</p>
          <p class="text-2xl font-serif text-purple-900">{{ totalSum }} Ft</p>
        </div>
        <button @click="handleFinalConfirm" class="bg-purple-900 text-white px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-widest">
          Foglalás
        </button>
      </div>
      <div class="flex gap-2 overflow-x-auto no-scrollbar">
        <div v-for="(item, index) in cart" :key="item.id" class="flex-shrink-0 bg-stone-50 px-3 py-2 rounded-lg border border-stone-100 flex items-center gap-2">
          <span class="text-base font-light">{{ item.name }}</span>
          <button @click="cart.splice(index, 1)" class="text-red-400 text-xs">✕</button>
        </div>
      </div>
    </div> 

    <CalendarModal 
      :is-open="isCalendarOpen"
      :pending-service="pendingService"
      :worker-name="selectedWorker?.user_name"
      :worker-id="selectedWorker?.id"
      :cart-items="cart"
      @close="isCalendarOpen = false"
      @confirm="handleAddToCart" 
    />
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAppointmentStore } from '~/stores/appointmentStore';
const appointmentStore = useAppointmentStore();

const submitReservation = async () => {
  if (!customerData.value.name || !customerData.value.email) {
    alert('Kérlek töltsd ki a neved és az emailed!');
    return;
  }

  try {
    for (const item of cart.value) {
      const payload = {
        appointment_from: item.startTime, 
        appointment_to: item.endTime,
        service: item.name,
        user_id: selectedWorker.value.id,
        name: customerData.value.name,
        email: customerData.value.email,
        phone_number: customerData.value.phone_number
      };
      await appointmentStore.saveAppointment(payload, true);
    }

    alert('Sikeres foglalás!');
    cart.value = [];
    isReservationModalOpen.value = false;
  } catch (error) { }
};
// A meglévők mellé add hozzá:
const authMode = ref('guest'); // 'guest' vagy 'google'

const handleFinalConfirm = () => {
  if (cart.value.length === 0) return;
  authMode.value = 'guest'; // Alapértelmezett a vendég mód
  isReservationModalOpen.value = true;
};

// Google bejelentkezés metódus
const handleGoogleLogin = () => {
  // Ide kell a backend Google OAuth végpontjára mutató hivatkozás
  window.location.href = `${config.public.apiBase}/auth/google`;
};
const config = useRuntimeConfig();
const router = useRouter();
const route  = useRoute();

const cart = ref([]);
const isCalendarOpen = ref(false);
const pendingService = ref(null);
const selectedWorker = ref(null);

const totalSum = computed(() => cart.value.reduce((total, i) => total + Number(i.price || 0), 0));

const isReservationModalOpen = ref(false);
const customerData = ref({ 
  name: '', 
  email: '', 
  phone_number: ''
});

// A Google gomb funkciója
const loginWithGoogle = () => {
  // Mielőtt elnavigálunk, mentsük el a kosarat localStorage-ba, hogy ne vesszen el!
  localStorage.setItem('cart_backup', JSON.stringify(cart.value));
  
  // Átirányítás a backend Google hitelesítő végpontjára
  window.location.href = `${config.public.apiBase}/api/auth/google`;
};  

import { onMounted } from 'vue';

onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search);
  const name = urlParams.get('g_name');
  const email = urlParams.get('g_email');
  const auth = urlParams.get('g_auth');

  if (auth === 'success') {
    // Kitöltjük az adatokat
    customerData.value.name = name || '';
    customerData.value.email = email || '';
    
    // Megnyitjuk a modalt, hogy lássa a kitöltött adatokat
    isReservationModalOpen.value = true;
    
    // Opcionális: tisztítsd meg az URL-t, hogy ne látszódjanak a query paraméterek
    window.history.replaceState({}, document.title, window.location.pathname);
  }
});

const handleAddToCart = (bookingData) => {
  cart.value.push({
    id: Date.now(),
    name: bookingData.name,
    price: bookingData.price,
    time: bookingData.time,
    startTime: bookingData.startTime,
    endTime: bookingData.endTime,
    worker: selectedWorker.value.user_name,
  });
  isCalendarOpen.value = false;
};

const openCalendarFor = (service) => {
  pendingService.value = service;
  isCalendarOpen.value = true;
};

const { data: publicUsers } = await useAsyncData('users', () =>
  $fetch(`${config.public.apiBase}/api/user_public_data`)
);

const schedule = {
  'Hétfő': '08:00 - 19:00', 'Kedd': '08:00 - 19:00', 'Szerda': '08:00 - 19:00',
  'Csütörtök': '08:00 - 19:00', 'Péntek': '08:00 - 19:00', 'Szombat': 'Zárva', 'Vasárnap': 'Zárva',
};

const syncWorkerFromUrl = () => {
  const workers = publicUsers.value?.data;
  if (!workers || workers.length === 0) return;
  const urlWorkerId = route.query.workerId;
  if (urlWorkerId) {
    const found = workers.find(w => String(w.id) === String(urlWorkerId));
    if (found) { selectedWorker.value = found; return; }
  }
  selectedWorker.value = workers[0];
};

watch(publicUsers, () => syncWorkerFromUrl(), { immediate: true });
watch(() => route.query.workerId, () => syncWorkerFromUrl());
watch(selectedWorker, (newWorker, oldWorker) => {
  if (oldWorker && newWorker && oldWorker.id !== newWorker.id && cart.value.length > 0) {
    cart.value = [];
  }
});
watch(isReservationModalOpen, (val) => {
  if (val) {
    document.body.style.overflow = 'hidden';
  } else {
    document.body.style.overflow = '';
  }
});
const selectWorker = (worker) => {
  selectedWorker.value = worker;
  router.replace({ query: { workerId: worker.id } });
};
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
</style>