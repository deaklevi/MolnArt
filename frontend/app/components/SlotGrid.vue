<template>
  <!-- Loading skeleton -->
  <div v-if="loading" class="grid grid-cols-3 gap-3">
    <div v-for="n in 9" :key="n" class="h-14 rounded-2xl bg-stone-100 animate-pulse" />
  </div>

  <!-- Slots -->
  <div v-else-if="slots.length > 0" class="grid grid-cols-3 gap-3">
    <button
      v-for="slot in slots"
      :key="slot.start"
      @click="$emit('select', slot)"
      class="py-4 rounded-2xl border text-sm font-bold transition-all bg-white border-stone-100 hover:border-purple-900 hover:text-purple-900 shadow-sm hover:shadow-md"
    >
      {{ slot.start }}
    </button>
  </div>

  <!-- Empty -->
  <div v-else class="py-20 text-center opacity-40 italic font-serif">
    Erre a napra minden hely elkelt.
  </div>
</template>

<script setup>
defineProps({
  slots:   { type: Array,   default: () => [] },
  loading: { type: Boolean, default: false },
})

defineEmits(['select'])
</script>