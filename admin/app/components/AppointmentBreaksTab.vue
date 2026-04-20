<script setup lang="ts">
import { ref } from 'vue'

defineProps<{
  breaks: any[]
}>()

const emit = defineEmits<{
  add: [data: { date: string; start: string; end: string }]
  delete: [id: number]
}>()

const breakForm = ref({
  date: '',
  start: '',
  end: ''
})

function handleAdd() {
  if (!breakForm.value.date || !breakForm.value.start || !breakForm.value.end) return
  emit('add', { ...breakForm.value })
  
  breakForm.value.start = ''
  breakForm.value.end = ''
}
</script>

<template>
  <div class="space-y-4">
    <h2 class="text-lg font-semibold">Egyéb elfoglaltság (szünetek / blokkolt idő)</h2>

    <div class="p-3 border rounded-lg space-y-2">
      <input v-model="breakForm.date" type="date" class="w-full border rounded p-2" />
      <div class="grid grid-cols-2 gap-2">
        <input v-model="breakForm.start" type="time" class="border rounded p-2" />
        <input v-model="breakForm.end" type="time" class="border rounded p-2" />
      </div>
      <button @click="handleAdd" class="w-full bg-red-600 text-white py-2 rounded">
        + Elfoglaltság hozzáadása
      </button>
    </div>

    <div class="space-y-2">
      <div
        v-for="b in breaks"
        :key="b.id"
        class="flex justify-between items-center border rounded p-2"
      >
        <div>
          <p class="text-sm font-medium">{{ b.date }}</p>
          <p class="text-xs text-gray-500">{{ b.start }} - {{ b.end }}</p>
        </div>
        <button @click="emit('delete', b.id)" class="text-red-500 text-sm">
          törlés
        </button>
      </div>
    </div>
  </div>
</template>