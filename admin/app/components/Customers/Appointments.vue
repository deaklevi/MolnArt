<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    appointments: Array
});

const currentIndex = ref(0);

const currentAppointment = computed(() => {
    return props.appointments && props.appointments.length > 0 
        ? props.appointments[currentIndex.value] 
        : null;
});

const next = () => {
    if (currentIndex.value < props.appointments.length - 1) currentIndex.value++;
};

const prev = () => {
    if (currentIndex.value > 0) currentIndex.value--;
};
</script>

<template>
  <div v-if="currentAppointment" class="bg-slate-50 rounded-xl p-5 border border-slate-100">
    <div class="flex justify-between items-center mb-4">
      <button @click="prev" :disabled="currentIndex === 0" class="disabled:opacity-30 p-2 hover:bg-white rounded-full transition-colors">
        <span class="text-xl">←</span>
      </button>
      
      <div class="text-center">
        <p class="font-bold">{{ new Date(currentAppointment.appointment_from).toLocaleDateString('hu-HU') }}</p>
        <p class="text-xs text-slate-400">{{ currentIndex + 1 }} / {{ appointments.length }}</p>
      </div>

      <button @click="next" :disabled="currentIndex === appointments.length - 1" class="disabled:opacity-30 p-2 hover:bg-white rounded-full transition-colors">
        <span class="text-xl">→</span>
      </button>
    </div>

    <div class="space-y-3">
      <div class="flex justify-between">
        <span class="text-slate-500">Szolgáltatás:</span>
        <span class="font-semibold">{{ currentAppointment.service }}</span>
      </div>

      <div class="pt-4 border-t border-slate-200">
        <p class="text-xs font-bold uppercase text-slate-400 mb-2 tracking-wider">Használt termékek</p>
        <ul v-if="currentAppointment.products?.length" class="space-y-2 overflow-y-auto pr-2 max-h-52">
          <li v-for="product in currentAppointment.products" :key="product.id" class="flex justify-between text-sm">
            <span>{{ product.name }}</span>
            <span class="font-mono bg-slate-200 px-2 rounded">{{ product.quantity }} {{ product.unit }}</span>
          </li>
        </ul>
        <p v-else class="text-sm italic text-slate-400">Nem lettek rögzítve termékek.</p>
      </div>
    </div>
  </div>
  <div v-else class="text-center py-10 text-slate-400 italic">
    Még nincs rögzített időpont.
  </div>
</template>