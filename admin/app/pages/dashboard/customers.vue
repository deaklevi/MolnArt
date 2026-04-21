<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useCustomerStore } from '@/stores/customerStore';
import type { Customer } from '@/stores/customerStore'; 

const customerStore = useCustomerStore();
const selectedCustomer = ref<Customer | null>(null);
const searchQuery = ref('');
const filteredCustomers = computed(() => {
  const query = searchQuery.value.toLowerCase().trim();
  if (!query) {
    return customerStore.customers;
  }

  return customerStore.customers.filter((customer) => {
    return (
      customer.name.toLowerCase().includes(query) ||
      (customer.email && customer.email.toLowerCase().includes(query)) ||
      (customer.phone_number && customer.phone_number.includes(query))
    );
  });
});

onMounted(async () => {
  await customerStore.fetchCustomers();
  
  if (customerStore.customers.length > 0) {
    selectedCustomer.value  = customerStore.customers[0]!;
  }
});

const selectCustomer = (customer: Customer) => {
  selectedCustomer.value = customer;
};
</script>

<template>
  <div class="max-w-6xl mx-auto p-6 font-sans">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
      <div>
        <h1 class="text-3xl font-black text-slate-900 tracking-tight">Kuncsaftok</h1>
        <p class="text-slate-500 font-medium">Kezeld ügyfeleidet egyszerűen</p>
      </div>
      <NuxtLink to="/dashboard" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-sm font-bold hover:bg-slate-50 transition-all shadow-sm">
        ← Irányítópult
      </NuxtLink>
    </div>

    <ClientOnly class="mt-10">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-10">
        <div class="flex flex-col h-[75vh]">
          
          <div class="mb-6 relative">
            <input 
              v-model="searchQuery" 
              type="text" 
              placeholder="Keresés név, email vagy telefon alapján..." 
              class="w-full pl-10 pr-4 py-3 bg-white border border-slate-200 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
            >
            <span class="absolute left-3 top-3.5 text-slate-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </span>
          </div>

          <div class="space-y-3 overflow-y-auto pr-2 pb-4">
            <template v-if="filteredCustomers.length > 0">
              <CustomersCard 
                v-for="customer in filteredCustomers" 
                :key="customer.id"
                :customer="customer" 
                :class="{ 'ring-2 ring-[#36082A] ring-inset': selectedCustomer?.id === customer.id }"
                @click="selectCustomer(customer)"
                class="cursor-pointer transition-all hover:shadow-md "
              />
            </template>
            
            <div v-else class="text-center py-10 text-slate-500 bg-slate-50 rounded-xl border border-slate-200 border-dashed">
              Nincs a keresésnek megfelelő ügyfél.
            </div>
          </div>

        </div>

        <div>
          <CustomersDetails v-if="selectedCustomer" :customer="selectedCustomer" />
          <div v-else class="text-center p-10 border-2 border-dashed rounded-xl text-gray-400">
            Válassz egy ügyfelet a megtekintéshez
          </div>
        </div>
        
      </div>
    </ClientOnly>
  </div>
</template>