<template>
  <div
    @click="$emit('workerSelected')"
    class="w-72 h-96 rounded-xl shadow-lg overflow-hidden cursor-pointer flex-shrink-0"
  >
    <img class="w-full h-full object-cover" :src="worker.profile_image" :alt="worker.user_name">
  </div>
</template>

<script setup>
defineProps({
  worker: {
    type: Object,
    required: true
  }
});

defineEmits(['workerSelected']);
</script>