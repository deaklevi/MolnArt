<template>
  <div class="reservation-page bg-stone-50 min-h-screen">
    
    <section class="bg-white border-b border-stone-200 shadow-sm">
      <div class="container mx-auto px-4 py-8">
        <h1 class="text-center text-2xl font-serif tracking-widest text-gray-900 uppercase mb-8">
          Válassz szakembert
        </h1>
        <div class="flex justify-center gap-8 md:gap-12 flex-wrap">
          <div v-for="worker in publicUsers?.data" 
               :key="worker.id" 
               @click="selectWorker(worker)" 
               class="flex flex-col items-center cursor-pointer group">
            <div class="relative rounded-full p-1 transition-all duration-500" 
                 :class="selectedWorker?.id === worker.id ? 'ring-4 ring-purple-900/30' : 'hover:ring-2 hover:ring-stone-200'">
              <img :src="`${config.public.apiBase}${worker.profile_image}`" 
                   class="w-20 h-20 md:w-24 md:h-24 rounded-full object-cover shadow-md transition-transform duration-500" 
                   :class="selectedWorker?.id === worker.id ? 'scale-100' : 'grayscale-[40%] group-hover:grayscale-0'" />
            </div>
            <span class="mt-3 text-sm font-bold uppercase tracking-tight transition-colors" 
                  :class="selectedWorker?.id === worker.id ? 'text-purple-900' : 'text-gray-500'">
              {{ worker.user_name }}
            </span>
          </div>
        </div>
      </div>
    </section>

    <div class="container mx-auto px-4 py-12 grid grid-cols-1 lg:grid-cols-12 gap-10 max-w-7xl">
      
      <div class="lg:col-span-8">
        <div class="flex items-center justify-between mb-8 border-b border-stone-200 pb-4">
          <h2 class="text-2xl font-serif tracking-wide text-gray-900 uppercase text-stone-800">
            Elérhető szolgáltatások
          </h2>
        </div>

        <div v-if="selectedWorker?.services?.length" class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div v-for="service in selectedWorker.services" :key="service.id" 
               class="bg-white p-6 border border-stone-100 rounded-xl shadow-sm hover:shadow-md transition-all border-l-4 border-l-transparent hover:border-l-purple-900 flex flex-col justify-between">
            <div>
              <div class="flex justify-between items-start mb-3">
                <h3 class="font-bold text-xl text-gray-900 leading-snug">{{ service.name }}</h3>
                <div class="flex items-center text-stone-500 text-sm bg-stone-50 px-2 py-1 rounded">
                  {{ service.time }}p
                </div>
              </div>
              <p class="text-stone-500 text-sm mb-6 leading-relaxed line-clamp-2">
                Professzionális kivitelezés prémium alapanyagokkal.
              </p>
            </div>
            <button @click="addToCart(service)" 
                    class="w-full py-3 bg-gray-900 text-white hover:bg-purple-900 transition-colors uppercase text-xs font-black tracking-widest rounded-lg">
              Hozzáadás a kosárhoz
            </button>
          </div>
        </div>
      </div>

      <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-10 self-start">
        
        <div v-if="cart.length > 0" class="bg-gray-950 text-white p-6 rounded-2xl shadow-xl border border-gray-800">
          <div class="flex justify-between items-center mb-4 border-b border-gray-800 pb-3">
            <h3 class="font-bold uppercase tracking-widest text-xs text-stone-400">Foglalásod</h3>
            <button @click="cart = []" class="text-[10px] text-red-400 hover:text-red-300 uppercase font-bold">Ürítés</button>
          </div>
          
          <ul class="space-y-4 mb-6">
            <li v-for="item in cart" :key="item.id" class="flex flex-col gap-2 text-sm border-b border-gray-900 pb-3 last:border-0">
              <div class="flex justify-between items-center">
                <span class="font-medium truncate pr-2 text-stone-200">{{ item.name }}</span>
                <span class="text-stone-500 font-mono shrink-0">{{ item.time * item.quantity }}p</span>
              </div>
              
              <div class="flex items-center justify-between">
                <div class="flex items-center bg-gray-900 rounded-md overflow-hidden">
                  <button @click="updateQuantity(item.id, -1)" class="px-3 py-1 hover:bg-stone-800 text-purple-400 font-bold">-</button>
                  <span class="px-2 text-xs font-mono min-w-[30px] text-center">{{ item.quantity }}x</span>
                  <button @click="updateQuantity(item.id, 1)" class="px-3 py-1 hover:bg-stone-800 text-purple-400 font-bold">+</button>
                </div>
                <button @click="removeFromCart(item.id)" class="text-stone-600 hover:text-red-400 text-xs">Eltávolítás</button>
              </div>
            </li>
          </ul>

          <div class="border-t border-gray-800 pt-4 mb-6">
            <div class="flex justify-between items-center font-bold">
              <span class="text-stone-400 text-sm uppercase">Teljes idő:</span>
              <span class="text-xl text-purple-400">{{ totalTime }} perc</span>
            </div>
          </div>

          <button @click="proceedToCalendar" 
                  class="w-full py-4 bg-purple-900 text-white hover:bg-purple-800 transition-all uppercase text-sm font-black tracking-widest rounded-xl shadow-lg">
            Időpont kiválasztása
          </button>
        </div>

        <div v-if="selectedWorker" class="bg-white p-8 rounded-2xl shadow-sm border border-stone-100">
          <h3 class="text-xs font-black text-purple-900 uppercase tracking-[0.2em] mb-2">Szakember</h3>
          <h2 class="text-3xl font-serif text-gray-950 mb-4">{{ selectedWorker.user_name }}</h2>
          <div class="h-1 w-12 bg-purple-900 mb-6"></div>
          <p class="text-stone-600 leading-relaxed italic text-sm">
            "{{ selectedWorker.description || 'Várlak szeretettel egy professzionális kezelésre!' }}"
          </p>
        </div>

        <div class="bg-white p-8 rounded-2xl shadow-sm border border-stone-100">
          <div class="flex items-center gap-2 mb-6 text-gray-900">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="font-bold uppercase tracking-widest text-sm text-stone-800">Heti nyitvatartás</h3>
          </div>

          <div class="space-y-3">
            <div v-for="(hours, day) in schedule" :key="day" 
                 class="flex justify-between items-center py-2 border-b border-stone-50 last:border-0">
              <span class="text-sm font-medium text-stone-600">{{ day }}</span>
              <span v-if="hours === 'Zárva'" class="text-[10px] font-black text-red-500 uppercase bg-red-50 px-2 py-1 rounded-md">
                {{ hours }}
              </span>
              <span v-else class="text-sm font-mono text-gray-900 font-bold italic">
                {{ hours }}
              </span>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watchEffect } from 'vue';

const config = useRuntimeConfig();
const router = useRouter();

// API adatok lekérése
const { data: publicUsers } = await useAsyncData('users', () => $fetch(`${config.public.apiBase}/api/user_public_data`));

const selectedWorker = ref(null);
const cart = ref([]);

const schedule = {
  'Hétfő': '08:00 - 19:00', 'Kedd': '08:00 - 19:00', 'Szerda': '08:00 - 19:00',
  'Csütörtök': '08:00 - 19:00', 'Péntek': '08:00 - 19:00', 'Szombat': 'Zárva', 'Vasárnap': 'Zárva'
};

// --- KOSÁR LOGIKA ---

const addToCart = (service) => {
  const existingItem = cart.value.find(item => item.id === service.id);
  if (existingItem) {
    existingItem.quantity += 1;
  } else {
    cart.value.push({ ...service, quantity: 1 });
  }
};

const updateQuantity = (id, change) => {
  const item = cart.value.find(i => i.id === id);
  if (item) {
    const newQty = item.quantity + change;
    if (newQty > 0) item.quantity = newQty;
    else removeFromCart(id);
  }
};

const removeFromCart = (id) => {
  cart.value = cart.value.filter(item => item.id !== id);
};

const totalTime = computed(() => {
  return cart.value.reduce((acc, curr) => {
    const minutes = Number(curr.time) || 0;
    return acc + (minutes * curr.quantity);
  }, 0);
});

// Szakember váltáskor ürítjük a kosarat
const selectWorker = (worker) => {
  selectedWorker.value = worker;
  cart.value = []; 
};

// Átirányítás a naptárra az adatok mentésével
const proceedToCalendar = () => {
  if (cart.value.length === 0 || !selectedWorker.value) return;

  const bookingState = {
    worker: {
      id: selectedWorker.value.id,
      name: selectedWorker.value.user_name,
      image: selectedWorker.value.profile_image
    },
    services: cart.value.map(s => ({ ...s, time: Number(s.time) })),
    totalDuration: Number(totalTime.value)
  };
  
  sessionStorage.setItem('temp_booking', JSON.stringify(bookingState));
  router.push('/booking/calendar');
};

// Alapértelmezett szakember beállítása betöltéskor
watchEffect(() => {
  if (publicUsers.value?.data && !selectedWorker.value) {
    selectedWorker.value = publicUsers.value.data[0];
  }
});
</script>