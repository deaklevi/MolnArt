<script setup>
const props = defineProps({
  service: {
    type: Object,
    required: true
  }
})
</script>

<template>
  <div class="flex items-center justify-between p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow">
    <div>
      <p class="text-lg font-bold text-gray-800">{{ service.name }}</p>
      <p class="text-sm text-gray-500 flex items-center gap-1">
        <span class="i-heroicons-clock text-gray-400"></span> 
        {{ service.time }} perc
      </p>
    </div>
    <button 
      type="button" 
      class="px-5 py-2 text-sm font-medium text-white bg-[#36082A] rounded-md hover:bg-[#470C37] transition-colors focus:ring-4 focus:ring-blue-300"
    >
      Foglalás
    </button>
  </div>
</template>