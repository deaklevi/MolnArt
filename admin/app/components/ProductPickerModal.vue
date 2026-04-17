<script setup lang="ts">
const props = defineProps<{
  modelValue: boolean
  products: any[]
}>()

const emit = defineEmits<{
  'update:modelValue': [boolean]
  add: [any]
}>()

const search = ref('')

const filteredProducts = computed(() => {
  if (!props.products) return []
  return props.products.filter(p =>
    p.name.toLowerCase().includes(search.value.toLowerCase())
  )
})

function close() {
  emit('update:modelValue', false)
}

function add(product: any) {
  emit('add', {
    ...product,
    quantity: 1
  })
}
</script>

<template>
  <div v-if="modelValue" class="fixed inset-0 bg-black/40 flex items-center justify-center z-[70]">
    <div class="bg-white w-full max-w-md rounded-xl p-4 space-y-3">

      <div class="flex justify-between items-center">
        <h3 class="font-semibold">Termék hozzáadása</h3>
        <button @click="close">✕</button>
      </div>

      <input
        v-model="search"
        placeholder="Keresés..."
        class="w-full border rounded-lg px-3 py-2 text-sm"
      />

      <div class="max-h-60 overflow-y-auto space-y-2">
        <div
          v-for="p in filteredProducts"
          :key="p.id"
          class="flex justify-between items-center p-3 border rounded-lg"
        >
          <div>
            <p class="font-medium text-sm">{{ p.name }}</p>
            <p class="text-xs text-gray-400">
              {{ p.stock }} {{ p.unit }} raktáron
            </p>
          </div>

          <button
            @click="add(p)"
            class="px-3 py-1 text-sm bg-violet-600 text-white rounded-md"
          >
            Hozzáad
          </button>
        </div>
      </div>

    </div>
  </div>
</template>