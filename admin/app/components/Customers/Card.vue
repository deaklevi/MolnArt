<script setup>
const props = defineProps({
    customer:Object,
})

</script>

<template>
    <div class="bg-white shadow-md rounded-2xl p-4 border max-w-md" id="card">
      <h2 class="font-bold text-xl mb-2">{{ customer.name }}</h2>
      <p class="font-semibold">{{ customer.email }}</p>
      <p class="italic">{{ customer.phone_number }}</p>
    </div>
</template>

