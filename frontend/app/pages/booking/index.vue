<template>
  <div class="reservation-page bg-stone-50 min-h-screen pb-12">
    
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
            {{ selectedWorker?.user_name }} szolgáltatásai
          </h2>
        </div>

        <div v-if="selectedWorker?.services?.length" class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div v-for="service in selectedWorker.services" :key="service.id" 
               @click="proceedToCalendar(service)"
               class="bg-white p-6 border border-stone-100 rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between cursor-pointer group">
            
            <div>
              <div class="flex justify-between items-start mb-3">
                <h3 class="font-bold text-lg text-gray-900 leading-snug group-hover:text-purple-900 transition-colors">
                  {{ service.name }}
                </h3>
                <div class="flex items-center text-purple-900 text-[10px] font-bold bg-purple-50 px-2 py-1 rounded-md uppercase tracking-tighter">
                  {{ service.time }} perc
                </div>
              </div>
              <p class="text-stone-500 text-sm mb-6 leading-relaxed line-clamp-3">
                {{ service.description || 'Professzionális szolgáltatás kiváló minőségben, személyre szabott figyelemmel.' }}
              </p>
            </div>

            <div class="mt-4 pt-4 border-t border-stone-50 flex items-center">
              
              <div class="flex items-center gap-2 px-5 py-2.5 rounded-full bg-purple-950 text-white text-[10px] font-black uppercase tracking-widest transition-all duration-300 group-hover:bg-purple-800 group-hover:shadow-lg group-hover:shadow-purple-900/20 group-hover:scale-105 active:scale-95">
                Időpont foglalása 
                <span class="transform transition-transform duration-300 group-hover:translate-x-1">→</span>
              </div>
            </div>

          </div>
        </div>
        
        <div v-else class="text-center py-20 bg-white rounded-3xl border border-dashed border-stone-200">
          <p class="text-stone-400 italic font-serif">Nincsenek elérhető szolgáltatások ennél a szakembernél.</p>
        </div>
      </div>

      <div class="lg:col-span-4 space-y-6">
        <div v-if="selectedWorker" class="bg-white p-8 rounded-3xl shadow-sm border border-stone-100 sticky top-40">
          <div class="mb-8">
            <h3 class="text-xs font-black text-purple-900 uppercase tracking-[0.2em] mb-4 border-b border-stone-50 pb-2">Bemutatkozás</h3>
            <h2 class="text-2xl font-serif text-gray-950 mb-3">{{ selectedWorker.user_name }}</h2>
            <p class="text-stone-600 italic text-sm leading-relaxed">
              "{{ selectedWorker.description || 'Várlak szeretettel a szalonomban, ahol a minőség és a figyelem találkozik.' }}"
            </p>
          </div>

          <div>
            <h3 class="text-xs font-black text-purple-900 uppercase tracking-[0.2em] mb-4 border-b border-stone-50 pb-2">Nyitvatartás</h3>
            <div class="space-y-3">
              <div v-for="(hours, day) in schedule" :key="day" class="flex justify-between text-sm">
                <span class="text-stone-500">{{ day }}</span>
                <span :class="hours === 'Zárva' ? 'text-red-400' : 'text-gray-900 font-medium'">{{ hours }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const config = useRuntimeConfig();
const router = useRouter();
const route = useRoute(); // Ez kell az URL figyeléséhez

// 1. Adatok lekérése az API-ból
const { data: publicUsers } = await useAsyncData('users', () => 
  $fetch(`${config.public.apiBase}/api/user_public_data`)
);

const selectedWorker = ref(null);

const schedule = {
  'Hétfő': '08:00 - 19:00', 
  'Kedd': '08:00 - 19:00', 
  'Szerda': '08:00 - 19:00',
  'Csütörtök': '08:00 - 19:00', 
  'Péntek': '08:00 - 19:00', 
  'Szombat': 'Zárva', 
  'Vasárnap': 'Zárva'
};

// 2. SZINKRONIZÁLÁS: Ez a függvény nézi meg, ki van az URL-ben
const syncWorkerFromUrl = () => {
  const workers = publicUsers.value?.data;
  if (!workers || workers.length === 0) return;

  const urlWorkerId = route.query.workerId;

  if (urlWorkerId) {
    // Megkeressük az URL-ben lévő ID alapján a szakembert
    const found = workers.find(w => String(w.id) === String(urlWorkerId));
    if (found) {
      selectedWorker.value = found;
      return;
    }
  }

  // Ha nincs ID az URL-ben, vagy nem létezik, az első szakember lesz az alapértelmezett
  selectedWorker.value = workers[0];
};

// 3. FIGYELŐK: Frissítünk, ha megjön az adat VAGY ha változik az URL
watch(publicUsers, () => syncWorkerFromUrl(), { immediate: true });
watch(() => route.query.workerId, () => syncWorkerFromUrl());

// Szakember manuális váltása (kattintás a körökre)
const selectWorker = (worker) => {
  selectedWorker.value = worker;
  // Frissítjük az URL-t is, hogy szinkronban maradjon a felülettel
  router.replace({ query: { workerId: worker.id } });
};

const proceedToCalendar = (service) => {
  if (!selectedWorker.value) return;

  const bookingState = {
    worker: {
      id: selectedWorker.value.id,
      name: selectedWorker.value.user_name,
      image: selectedWorker.value.profile_image
    },
    services: [{ ...service, quantity: 1, time: Number(service.time) }],
    totalDuration: Number(service.time)
  };
  
  sessionStorage.setItem('temp_booking', JSON.stringify(bookingState));
  router.push('/booking/calendar');
};
</script>

