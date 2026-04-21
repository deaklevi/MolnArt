<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useCustomerStore } from '@/stores/customerStore';

import type { Customer } from '@/stores/customerStore'; 

const customerStore = useCustomerStore();

const selectedCustomer = ref<Customer | null>(null);

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
      <div class="grid grid-cols-2 gap-4 mt-10">
        <div class="space-y-4">
          <CustomersCard 
            v-for="customer in customerStore.customers" 
            :key="customer.id"
            :customer="customer" 
            :class="{ 'ring-2 ring-blue-500': selectedCustomer?.id === customer.id }"
            @click="selectCustomer(customer)"
            class="cursor-pointer transition-all"
          />
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