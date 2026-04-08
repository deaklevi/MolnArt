<template>
<div class="flex items-center gap-3 bg-white border border-gray-200 rounded-xl px-4 py-1 shadow-md">
    <div class="flex-1 min-w-0">
      <h3 class="text-sm font-medium text-gray-800 truncate">{{ product.name }}</h3>
    </div>

    <div class="flex items-center gap-3">
      <input
        :value="product.quantity"
        type="number"
        min="0"
        class="w-20 border border-gray-300 rounded-md px-2 py-1 text-sm text-center focus:outline-none focus:ring-2 focus:ring-violet-400"
        @input="e => emit('update:quantity', Number(e.target.value))"
      />
      <span class="text-md italic text-gray-400 w-5">{{ product.unit }}</span>
      <button
        type="button"
        class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-300 hover:bg-red-50 hover:text-red-500 transition-colors"
        @click="emit('remove')"
      >
        ✕
      </button>
    </div>

  </div>

</template>

<script setup>
    
    const props = defineProps({
        product:{
            type: Object,
            required: true,
            default: ()=> ({
                name : "Hair Dye",
                unit : "ml",
                stock : 500
            })
        }
    })

    const emit = defineEmits(['update:quantity', 'remove'])
    const onInput = (e) => {
        emit('update:quantity', Number(e.target.value))
    }
</script>