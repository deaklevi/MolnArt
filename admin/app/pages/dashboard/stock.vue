<template>
  <div class="min-h-screen bg-[#f8fafc] p-4 md:p-8">
    <header class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
      <div>
        <h1 class="text-slate-800 text-3xl font-bold tracking-tight">Raktárkészlet</h1>
        <p class="text-slate-500 text-sm mt-1">Aktuális készletszintek és termékadatok.</p>
      </div>
      
      <NuxtLink to="/dashboard" class="px-5 py-2 bg-white hover:bg-slate-50 text-slate-600 border border-slate-200 shadow-sm rounded-xl font-semibold transition-all flex items-center gap-2">
        <span>←</span> Vissza
      </NuxtLink>
    </header>

    <div class="max-w-7xl mx-auto mb-8 space-y-4">
      <div class="flex flex-wrap gap-4 items-center">
        <div class="relative flex-1 min-w-[300px]">
          <input 
            v-model="searchQuery"
            type="text" 
            placeholder="Keresés név alapján..." 
            class="w-full pl-4 pr-4 py-3 bg-white border border-slate-200 rounded-2xl shadow-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 outline-none transition-all"
          />
        </div>

        <button 
          @click="showLowStock = !showLowStock"
          :class="showLowStock ? 'bg-rose-600 text-white border-rose-600' : 'bg-white text-rose-600 border-rose-200 hover:bg-rose-50'"
          class="px-6 py-3 rounded-2xl border font-bold transition-all shadow-sm flex items-center gap-2"
        >
          <span :class="showLowStock ? 'animate-pulse' : ''">⚠️</span>
          Kevés készlet
        </button>
      </div>

      <div class="flex flex-wrap gap-2">
        <button 
          @click="selectedTypeId = null"
          :class="selectedTypeId === null ? 'bg-slate-800 text-white border-slate-800' : 'bg-white text-slate-600 border-slate-200'"
          class="px-4 py-2 rounded-xl border text-sm font-bold transition-all shadow-sm"
        >
          Összes
        </button>
        
        <button 
          v-for="brand in brands" 
          :key="brand.id"
          @click="selectedTypeId = brand.id"
          :class="selectedTypeId === brand.id ? 'bg-xarose-500 text-white border-rose-500' : 'bg-white text-slate-600 border-slate-200 hover:border-slate-300'"
          class="px-4 py-2 rounded-xl border text-sm font-bold transition-all shadow-sm"
        >
          {{ brand.label }}
        </button>
      </div>
    </div>

    <div v-if="pending" class="flex justify-center items-center py-20">
      <div class="animate-spin rounded-full h-10 w-10 border-t-2 border-rose-500"></div>
    </div>

    <div v-else-if="filteredProducts.length > 0" class="max-w-7xl mx-auto">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <div v-for="item in filteredProducts" :key="item.id" class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm hover:shadow-md transition-all duration-300 group relative overflow-hidden" >
          <div v-if="item.stock <= 10" class="absolute left-0 top-0 bottom-0 w-1.5 bg-rose-500"></div>
          
          <div class="mb-4">
            <h2 class="text-slate-800 text-lg font-bold leading-tight mb-1">{{ item.name }}</h2>
            <span class="text-slate-400 text-[10px] font-mono uppercase tracking-widest">SKU #{{ item.id }} / Type #{{ item.type }}</span>
          </div>

          <div class="flex justify-between items-end border-t border-slate-50 pt-4 mt-2">
            <div class="flex flex-col">
              <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider italic">{{ item.unit }}</span>
            </div>
            <div class="flex flex-col items-end">
              <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider mb-1">Készleten</span>
              <div class="flex items-center gap-1.5">
                <span class="text-2xl font-bold" :class="item.stock > 10 ? 'text-slate-800' : 'text-rose-600'">{{ item.stock }}</span>
                <span class="text-slate-600 font-semibold">{{ item.unit }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="max-w-md mx-auto text-center py-20 bg-white rounded-3xl border border-dashed border-slate-300">
      <div class="text-4xl mb-3">🔍</div>
      <p class="text-slate-500 font-medium italic">Nincs a szűrésnek megfelelő termék.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const config = useRuntimeConfig();

// A Seeder alapján pontosított kategóriák (type ID-k)
const brands = [
  { id: 1, label: 'INOA' },
  { id: 2, label: 'Majirel Cool Cover' },
  { id: 3, label: 'Majirel' },
  { id: 4, label: 'Dia Light' },
  { id: 5, label: 'Dia Color' },
  { id: 6, label: 'Dia color előhívó' },
  { id: 7, label: 'Majinel előhív' },
  { id: 8, label: 'INOA előhív' },
  { id: 9, label: 'szőkítő ' },
  { id: 10, label: 'szőkítő por' },
  { id: 11, label: 'Egyéb (Hair Care)' }
];

const selectedTypeId = ref(null); // Itt tároljuk az aktív ID-t
const showLowStock = ref(false);
const searchQuery = ref('');

// Adatok lekérése
const { data: products, pending } = await useAsyncData('products', () => 
  $fetch(`${config.public.apiBase}/api/products`)
);

// Kombinált szűrés
const filteredProducts = computed(() => {
  if (!products.value || !products.value.data) return [];

  return products.value.data.filter(item => {
    // 1. Keresőmező (név alapján)
    const matchesSearch = item.name.toLowerCase().includes(searchQuery.value.toLowerCase());
    
    // 2. Kevés készlet szűrő (ha be van kapcsolva, csak a 10 alattiakat mutatja)
    const matchesLowStock = showLowStock.value ? item.stock <= 300 : true;

    // 3. Kategória szűrés (Szigorú ID egyezés a seeder type mezőjével)
    const matchesType = selectedTypeId.value === null || item.type === selectedTypeId.value;

    return matchesSearch && matchesLowStock && matchesType;
  });
});
</script>