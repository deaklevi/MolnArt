<template>
  <div class="reservation-page bg-stone-50 min-h-screen pb-32 lg:pb-12">
    
    <section class="bg-white border-b border-stone-200 shadow-sm sticky top-0 z-30">
      <div class="container mx-auto px-4 py-6">
        <h1 class="text-center text-lg md:text-2xl font-serif tracking-widest text-gray-900 uppercase mb-6">
          Válassz szakembert
        </h1>
        <div class="flex justify-center gap-6 md:gap-12 flex-wrap">
          <div v-for="worker in publicUsers?.data" 
               :key="worker.id" 
               @click="selectWorker(worker)" 
               class="flex flex-col items-center cursor-pointer group">
            <div class="relative rounded-full p-1 transition-all duration-500" 
                 :class="selectedWorker?.id === worker.id ? 'ring-4 ring-purple-900/30' : 'hover:ring-2 hover:ring-stone-200'">
              <img :src="`${config.public.apiBase}${worker.profile_image}`" 
                   class="w-16 h-16 md:w-24 md:h-24 rounded-full object-cover shadow-md transition-transform duration-500" 
                   :class="selectedWorker?.id === worker.id ? 'scale-100' : 'grayscale-[40%] group-hover:grayscale-0'" />
            </div>
            <span class="mt-2 text-[10px] md:text-sm font-bold uppercase tracking-tight transition-colors" 
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
          <h2 class="text-xl md:text-2xl font-serif tracking-wide text-gray-900 uppercase">
            Elérhető szolgáltatások
          </h2>
        </div>

        <div v-if="selectedWorker?.services?.length" class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div v-for="service in selectedWorker.services" :key="service.id" 
               class="bg-white p-6 border border-stone-100 rounded-xl shadow-sm hover:shadow-md transition-all border-l-4 border-l-transparent hover:border-l-purple-900 flex flex-col justify-between">
            <div>
              <div class="flex justify-between items-start mb-3">
                <h3 class="font-bold text-lg text-gray-900 leading-snug">{{ service.name }}</h3>
                <div class="flex items-center text-stone-500 text-xs bg-stone-50 px-2 py-1 rounded">
                  {{ service.time }}p
                </div>
              </div>
              <p class="text-stone-500 text-sm mb-6 leading-relaxed line-clamp-2">
                Professzionális kivitelezés prémium alapanyagokkal.
              </p>
            </div>
            <button @click="addToCart(service)" 
                    class="w-full py-3 bg-gray-900 text-white hover:bg-purple-900 transition-colors uppercase text-xs font-black tracking-widest rounded-lg">
              Hozzáadás
            </button>
          </div>
        </div>
      </div>

      <div class="hidden lg:block lg:col-span-4 space-y-6 lg:sticky lg:top-40 self-start">
        <div v-if="cart.length > 0" class="bg-gray-950 text-white p-6 rounded-2xl shadow-xl border border-gray-800">
          <div class="flex justify-between items-center mb-4 border-b border-gray-800 pb-3">
            <h3 class="font-bold uppercase tracking-widest text-xs text-stone-400">Foglalásod</h3>
            <button @click="cart = []" class="text-[10px] text-red-400 hover:text-red-300 uppercase font-bold">Ürítés</button>
          </div>
          
          <ul class="space-y-4 mb-6 max-h-60 overflow-y-auto pr-2 custom-scrollbar">
            <li v-for="item in cart" :key="item.id" class="flex flex-col gap-2 text-sm border-b border-gray-900 pb-3 last:border-0">
              <div class="flex justify-between items-center">
                <span class="font-medium truncate pr-2 text-stone-200">{{ item.name }}</span>
                <span class="text-stone-500 font-mono shrink-0">{{ item.time * item.quantity }}p</span>
              </div>
              <div class="flex items-center justify-between">
                <div class="flex items-center bg-gray-900 rounded-md overflow-hidden">
                  <button @click="updateQuantity(item.id, -1)" class="px-3 py-1 hover:bg-stone-800 text-purple-400">-</button>
                  <span class="px-2 text-xs font-mono">{{ item.quantity }}x</span>
                  <button @click="updateQuantity(item.id, 1)" class="px-3 py-1 hover:bg-stone-800 text-purple-400">+</button>
                </div>
                <button @click="removeFromCart(item.id)" class="text-stone-600 hover:text-red-400 text-xs">Törlés</button>
              </div>
            </li>
          </ul>

          <div class="border-t border-gray-800 pt-4 mb-6">
            <div class="flex justify-between items-center font-bold">
              <span class="text-stone-400 text-sm uppercase">Összesen:</span>
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
          <h2 class="text-2xl font-serif text-gray-950 mb-2">{{ selectedWorker.user_name }}</h2>
          <p class="text-stone-600 italic text-sm">"{{ selectedWorker.description || 'Várlak szeretettel!' }}"</p>
        </div>
      </div>
    </div>

    <transition name="slide-up">
      <div v-if="cart.length > 0" class="lg:hidden fixed bottom-0 left-0 right-0 z-50 bg-gray-950 text-white p-4 shadow-[0_-10px_25px_-5px_rgba(0,0,0,0.3)] border-t border-gray-800 rounded-t-3xl">
        <div class="container mx-auto">
          <div class="flex items-center justify-between mb-4 px-2">
            <div>
              <span class="text-stone-400 text-[10px] uppercase block tracking-tighter">Kiválasztott szolgáltatások: {{ cart.length }} db</span>
              <span class="text-lg font-bold text-purple-400">{{ totalTime }} perc összesen</span>
            </div>
            <button @click="proceedToCalendar" 
              class="bg-purple-900 text-white px-6 py-3 rounded-xl font-black uppercase text-xs tracking-widest shadow-lg active:scale-95 transition-transform">
              Időpont
            </button>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<style scoped>
.slide-up-enter-active, .slide-up-leave-active {
  transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
.slide-up-enter-from, .slide-up-leave-to {
  transform: translateY(100%);
}

.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: #111;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #444;
  border-radius: 10px;
}
</style>

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