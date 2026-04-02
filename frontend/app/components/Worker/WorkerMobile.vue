<template>
  <div
    class="relative transition-all duration-500 ease-in-out transform select-none"
    :class="[
      isActive 
        ? 'scale-125 z-20 opacity-100' 
        : 'scale-90 opacity-50 z-10' 
    ]"
  >
    <div class="aspect-[3/4] w-full rounded-xl border-2 border-white bg-white overflow-hidden">
      <img 
        class="w-full h-full object-cover pointer-events-none" 
        :src="apiBase + worker.profile_image" 
        :alt="worker.user_name"
      >
    </div>
  </div>
</template>

<script setup>
const config = useRuntimeConfig();
const apiBase = config.public.apiBase;

defineProps({
  worker: { type: Object, required: true },
  isActive: { type: Boolean, default: false }
});
</script>