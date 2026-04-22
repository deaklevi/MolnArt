<template>
  <div class="min-h-screen bg-[#f8fafc] p-4 md:p-8 relative">
    <header class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
      <div>
        <h1 class="text-slate-800 text-3xl font-bold tracking-tight">Raktárkészlet</h1>
        <p class="text-slate-500 text-sm mt-1">Aktuális készletszintek és termékadatok.</p>
      </div>
      <NuxtLink to="/dashboard" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-sm font-bold hover:bg-slate-50 transition-all shadow-sm">
        ← Irányítópult
      </NuxtLink>
    </header>

    <div class="max-w-7xl mx-auto mb-8 space-y-4">
      <div class="flex flex-wrap gap-4 items-center">
        <div class="relative flex-1 min-w-[300px]">
          <input v-model="searchQuery" type="text" placeholder="Keresés név alapján..." class="w-full pl-4 pr-4 py-3 bg-white border border-slate-200 rounded-2xl shadow-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 outline-none transition-all" />
        </div>
        <button @click="showLowStock = !showLowStock" :class="showLowStock ? 'bg-rose-600 text-white border-rose-600' : 'bg-white text-rose-600 border-rose-200 hover:bg-rose-50'" class="px-6 py-3 rounded-2xl border font-bold transition-all shadow-sm flex items-center gap-2">
          <span>⚠️</span> Kevés készlet
        </button>
      </div>

      <div class="flex flex-wrap gap-2">
        <button @click="selectedTypeId = null" :class="selectedTypeId === null ? 'bg-slate-800 text-white border-slate-800' : 'bg-white text-slate-600 border-slate-200'" class="px-4 py-2 rounded-xl border text-sm font-bold transition-all shadow-sm">Összes</button>
        <button v-for="brand in brands" :key="brand.id" @click="selectedTypeId = brand.id" :class="selectedTypeId === brand.id ? 'bg-slate-300 text-white border-rose-500' : 'bg-white text-slate-600 border-slate-200 hover:border-slate-300'" class="px-4 py-2 rounded-xl border text-sm font-bold transition-all shadow-sm">{{ brand.label }}</button>
      </div>
    </div>

    <div v-if="pending" class="flex justify-center items-center py-20">
      <div class="animate-spin rounded-full h-10 w-10 border-t-2 border-rose-500"></div>
    </div>

    <div v-else-if="filteredProducts?.length > 0" class="max-w-7xl mx-auto pb-20">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <div v-for="item in filteredProducts" :key="item.id" class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm hover:shadow-md transition-all duration-300 group relative overflow-hidden">
          <div v-if="item.stock <= 10" class="absolute left-0 top-0 bottom-0 w-1.5 bg-rose-500"></div>
          
          <div class="mb-4">
            <div class="flex justify-between items-start">
              <div>
                <h2 class="text-slate-800 text-lg font-bold leading-tight mb-1">{{ item.name }}</h2>
                <span class="text-slate-400 text-[10px] font-mono uppercase tracking-widest">SKU #{{ item.id }}</span>
              </div>
              <button @click="deleteProduct(item.id)" :disabled="isDeleting === item.id" class="p-2.5 text-slate-300 hover:text-rose-500 hover:bg-rose-50 rounded-xl transition-all">
                <svg v-if="isDeleting !== item.id" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                <div v-else class="w-6 h-6 border-2 border-rose-500 border-t-transparent rounded-full animate-spin"></div>
              </button>
            </div>
          </div>

          <div class="flex items-end justify-between border-t border-slate-50 pt-4 mt-2">
            <div class="flex flex-col items-start">
              <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider mb-1">Készleten</span>
              <div class="flex items-center gap-1.5">
                <span class="text-2xl font-bold" :class="item.stock > 10 ? 'text-slate-800' : 'text-rose-600'">{{ item.stock }}</span>
                <span class="text-slate-600 font-semibold">{{ item.unit && 'g' }}</span>
              </div>
            </div>
            <button @click="openEditModal(item)" class="border-2 border-slate-200 rounded-xl text-slate-600 px-4 py-1.5 hover:bg-slate-600 hover:text-white hover:border-slate-600 transition-all duration-300 font-semibold text-sm">
              Módosítás
            </button>
          </div>
        </div>
      </div> 
    </div>

    <div v-if="isEditOpen && editingProduct" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
      <div @click="isEditOpen = false" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
      <div class="relative bg-white rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden animate-in zoom-in duration-300">
        <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center bg-slate-50">
          <h2 class="text-xl font-bold text-slate-800">Termék szerkesztése</h2>
          <button @click="isEditOpen = false" aria-label="Bezárás" class="p-2 -mr-2 text-slate-400 hover:bg-slate-200 hover:text-slate-600 rounded-full transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        <div class="p-8">
          <div class="mb-6">
            <h4 class="text-[10px] uppercase font-bold text-slate-400 tracking-widest mb-1">Kiválasztott termék</h4>
            <p class="font-black text-slate-800 text-lg">{{ editingProduct.name }}</p>
            <div class="mt-2 text-sm text-slate-500">Jelenleg: <span class="text-slate-800 font-bold">{{ editingProduct.stock }} g</span></div>
          </div>
          <div class="space-y-1">
            <label class="text-[10px] uppercase font-bold text-slate-400 ml-1 tracking-widest">Hozzáadandó mennyiség (g)</label>
            <input v-model="addAmount" type="number" class="w-full bg-slate-50 border-2 border-blue-100 rounded-xl p-4 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none text-2xl font-black ">
          </div>
        </div>
        <div class="p-8 pt-0 flex gap-3">
          <button @click="isEditOpen = false" class="flex-1 py-4 bg-slate-100 text-slate-600 rounded-2xl font-bold hover:bg-slate-200 transition-all">Mégse</button>
          <button @click="updateStock" class="flex-1 py-4 bg-blue-600 text-white rounded-2xl font-bold hover:bg-blue-700 transition-all">Mentés</button>
        </div>
      </div>
    </div>

    <div class="fixed bottom-8 right-8 z-40">
      <button @click="isOpen = true" class="flex items-center justify-center w-14 h-14 bg-emerald-600 text-white rounded-full shadow-xl hover:bg-emerald-700 hover:scale-110 transition-all">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
      </button>
    </div>

    <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div @click="isOpen = false" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
      <div class="relative bg-white rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden animate-in zoom-in duration-300">
        <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center bg-slate-50">
          <h2 class="text-xl font-bold text-slate-800">Új termék</h2>
          <button @click="isOpen = false" aria-label="Bezárás" class="p-2 -mr-2 text-slate-400 hover:bg-slate-200 hover:text-slate-600 rounded-full transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        <div class="p-8 space-y-5">
          <div class="space-y-1">
            <label class="text-[10px] uppercase font-bold text-slate-400 tracking-widest ml-1">Termék neve</label>
            <input v-model="formData.name" type="text" placeholder="Pl: INOA 5.1" class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-4 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all">
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-1">
              <label class="text-[10px] uppercase font-bold text-slate-400 tracking-widest ml-1">Készlet</label>
              <input v-model="formData.stock" type="number" min="0" class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-4 focus:ring-2 focus:ring-emerald-500/20 outline-none">
            </div>
            <div class="space-y-1">
              <label class="text-[10px] uppercase font-bold text-slate-400 tracking-widest ml-1">Márka / Típus</label>
              <select v-model="formData.type" class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-4 focus:ring-2 focus:ring-emerald-500/20 outline-none appearance-none">
                <option value="" disabled>Válasszon...</option>
                <option v-for="brand in brands" :key="brand.id" :value="brand.id">{{ brand.label }}</option>
              </select>
            </div>
          </div>
        </div>
        <div class="p-8 pt-0 flex gap-3">
          <button @click="isOpen = false" class="flex-1 py-4 bg-slate-100 text-slate-600 rounded-2xl font-bold hover:bg-slate-200 transition-all">Mégse</button>
          <button @click="saveProduct" class="flex-1 py-4 bg-emerald-600 text-white rounded-2xl font-bold hover:bg-emerald-700 transition-all">Mentés</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
const config = useRuntimeConfig();
const isOpen = ref(false);
const isEditOpen = ref(false);
const editingProduct = ref(null);
const addAmount = ref(0);
const isDeleting = ref(null);

const formData = ref({
  name: '',
  stock: 0,
  type: '',  // empty string so placeholder shows
  note: ''
});

const openEditModal = (item) => {
  editingProduct.value = { ...item };
  addAmount.value = 0;
  isEditOpen.value = true;
};

const deleteProduct = async (id) => {
  if (!confirm('Biztosan törölni szeretnéd ezt a terméket?')) return;
  isDeleting.value = id;
  try {
    await $fetch(`${config.public.apiBase}/api/products/${id}`, {
      method: 'DELETE',
      headers: { 'Accept': 'application/json' }
    });
    await refresh();
  } catch (err) {
    console.error(err.data);
    alert('Hiba történt a törlés során.');
  } finally {
    isDeleting.value = null;
  }
};

const brands = [
  { id: 1, label: 'INOA' },
  { id: 2, label: 'Majirel Cool Cover' },
  { id: 3, label: 'Majirel' },
  { id: 4, label: 'Dia Light' },
  { id: 5, label: 'Dia Color' },
  { id: 6, label: 'Dia color előhívó' },
  { id: 7, label: 'Majinel előhív' },
  { id: 8, label: 'INOA előhív' },
  { id: 9, label: 'Szőkítő ' },
  { id: 10, label: 'Szőkítő por' },
  { id: 11, label: 'Egyéb (Hair Care)' }
];

const selectedTypeId = ref(null);
const showLowStock = ref(false);
const searchQuery = ref('');

const { data: products, pending, refresh } = await useAsyncData('products', () => 
  $fetch(`${config.public.apiBase}/api/products`)
);

const saveProduct = async () => {
  // Prevent saving if initial stock is negative
  if (formData.value.stock < 0) {
    alert('A készlet nem lehet negatív!');
    return;
  }

  try {
    await $fetch(`${config.public.apiBase}/api/products`, {
      method: 'POST',
      body: {
        name: formData.value.name,
        stock: Number(formData.value.stock),
        type: Number(formData.value.type),
        unit: 'g'
      },
      // ... rest of your code
    });
    // ...
  } catch (err) {
    // ...
  }
};

const updateStock = async () => {
  // Calculate the potential new total
  const newTotal = editingProduct.value.stock + Number(addAmount.value);

  // Stop if it goes below zero
  if (newTotal < 0) {
    alert(`Hiba: A művelet nem hajtható végre, mert a készlet ${newTotal}g lenne.`);
    return;
  }

  try {
    await $fetch(`${config.public.apiBase}/api/products/${editingProduct.value.id}`, {
      method: 'PATCH',
      body: { amount: Number(addAmount.value) },
      headers: { 'Accept': 'application/json' }
    });
    await refresh(); 
    isEditOpen.value = false;
    editingProduct.value = null;
    addAmount.value = 0;
  } catch (err) {
    console.error(err.data);
    alert('Hiba történt a mentés során.');
  }
};

const filteredProducts = computed(() => {
  if (!products.value || !products.value.data) return [];
  return products.value.data.filter(item => {
    const matchesSearch = item.name.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesLowStock = showLowStock.value ? item.stock <= 300 : true;
    const matchesType = selectedTypeId.value === null || item.type === selectedTypeId.value;
    return matchesSearch && matchesLowStock && matchesType;
  });
});
</script>