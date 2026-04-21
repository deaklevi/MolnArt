<script setup>


const props = defineProps({
    customer : Object
})

</script>

<template>
  <div class="border border-slate-200 h-fit bg-white shadow-lg rounded-2xl p-6">
    <div class="text-center border-b pb-6">
      <h2 class="font-black text-3xl text-slate-800">{{ customer.name }}</h2>
      <p class="text-slate-500 mt-2">{{ customer.email }} • {{ customer.phone_number }}</p>
    </div>

    <div class="mt-6">
      <h3 class="font-bold text-lg mb-4 text-center mt-10">Időpont előzmények</h3>
      <CustomersAppointments :appointments="customer.appointments" class="h-[24rem]" />
    </div>
  </div>
</template>