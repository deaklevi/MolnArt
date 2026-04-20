<script setup lang="ts">
import { ref, computed } from 'vue'

const props = defineProps<{
  show: boolean
  services: { id: number; name: string; time: number }[]
}>()

const emit = defineEmits<{
  close: []
  select: [service: { id: number; name: string; time: number }]
}>()

const search = ref('')

const filteredServices = computed(() => {
  if (!props.services) return []
  return props.services.filter((s) =>
    s.name.toLowerCase().includes(search.value.toLowerCase())
  )
})

function selectAndClear(service: any) {
  emit('select', service)
  search.value = '' // kereső ürítése kiválasztás után
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 bg-black/40 flex items-center justify-center z-[60]">
    <div class="bg-white w-full max-w-md rounded-xl p-4 space-y-3">
      <div class="flex justify-between items-center">
        <h3 class="font-semibold">Szolgáltatás kiválasztása</h3>
        <button @click="emit('close')">✕</button>
      </div>

      <input
        v-model="search"
        placeholder="Keresés..."
        class="w-full border rounded-lg px-3 py-2 text-sm"
      />

      <div class="max-h-60 overflow-y-auto space-y-2">
        <div
          v-for="service in filteredServices"
          :key="service.id"
          @click="selectAndClear(service)"
          class="p-3 rounded-lg border cursor-pointer hover:bg-violet-50"
        >
          {{ service.name }} - <span class="text-gray-500 italic">{{ service.time }} perc</span>
        </div>
      </div>
    </div>
  </div>
</template>