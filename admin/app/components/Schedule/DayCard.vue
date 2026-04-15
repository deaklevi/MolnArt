<script setup lang="ts">
type DayState = {
  date: string
  label: string
  open: boolean
  start: string
  end: string
  id: number | null
  saving: boolean
  dirty: boolean
}

defineProps<{
  day: DayState
}>()

defineEmits<{
  (e: 'toggle'): void
  (e: 'update-start', value: string): void
  (e: 'update-end', value: string): void
  (e: 'save'): void
}>()
</script>

<template>
  <div class="border rounded-lg p-2 bg-gray-50 space-y-2">

    <div class="flex items-center justify-between">
      <div>
        <p class="font-semibold text-gray-800">{{ day.label }}</p>
        <p class="text-xs text-gray-400">{{ day.date }}</p>
      </div>

      <button
        class="text-sm px-3 py-1 rounded-lg border transition"
        :class="day.open ? 'bg-green-50 border-green-200 text-green-600' : 'bg-gray-50 border-gray-200'"
        @click="$emit('toggle')"
      >
        {{ day.open ? 'Nyitva' : 'Zárva' }}
      </button>
    </div>

    <div v-if="day.open" class="grid grid-cols-2 gap-2">
      <div class="space-y-1">
        <label class="text-xs text-gray-500">Kezdés</label>
        <input
          type="time"
          class="w-full border rounded-lg px-2 py-1 text-sm"
          :value="day.start"
          @input="$emit('update-start', ($event.target as HTMLInputElement).value)"
        />
      </div>

      <div class="space-y-1">
        <label class="text-xs text-gray-500">Befejezés</label>
        <input
          type="time"
          class="w-full border rounded-lg px-2 py-1 text-sm"
          :value="day.end"
          @input="$emit('update-end', ($event.target as HTMLInputElement).value)"
        />
      </div>
    </div>

    <div class="flex justify-end" v-if="day.dirty">
      <button
        class="px-4 py-1.5 text-sm rounded-lg bg-black text-white disabled:opacity-50"
        :disabled="day.saving || !day.dirty"
        @click="$emit('save')"
      >
        {{ day.saving ? 'Mentés...' : 'Mentés' }}
      </button>
    </div>

  </div>
</template>