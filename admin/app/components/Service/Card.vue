<script setup>
const props = defineProps({
  service: Object,
  isSelected: Boolean
})

const emit = defineEmits(['toggle', 'update', 'delete'])

const isEditModalOpen = ref(false)
const editForm = ref({ ...props.service })

const openEdit = (e) => {
  e.stopPropagation() 
  editForm.value = { ...props.service }
  isEditModalOpen.value = true
}

const handleDelete = (e) => {
  e.stopPropagation()
  if (confirm('Biztosan törölni szeretnéd ezt a szolgáltatást?')) {
    emit('delete', props.service.id)
  }
}

const submitUpdate = () => {
  emit('update', props.service.id, editForm.value)
  isEditModalOpen.value = false
}
</script>

<template>
  <div 
    @click="$emit('toggle', service.id)"
    :class="[
      'group p-5 border-2 rounded-2xl cursor-pointer transition-all flex items-center justify-between relative',
      isSelected ? 'border-[#36082A] bg-white' : 'border-slate-50 bg-slate-50/50 hover:border-slate-200'
    ]"
  >
    <div class="flex items-center gap-4">
      <div :class="['w-6 h-6 rounded-lg border-2 flex items-center justify-center transition-all', isSelected ? 'bg-[#36082A] border-[#36082A]' : 'bg-white border-slate-200']">
        <svg v-if="isSelected" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
        </svg>
      </div>

      <div>
        <div class="font-bold text-slate-900 group-hover:text-[#36082A] transition-colors">{{ service.name }}</div>
        <div class="text-xs font-bold text-slate-400 uppercase tracking-tighter">{{ service.time }} perc</div>
      </div>
    </div>

    <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
      <button @click="openEdit" class="p-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-[#36082A] hover:text-white transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
        </svg>
      </button>
      <button @click="handleDelete" class="p-2 bg-rose-100 text-rose-600 rounded-lg hover:bg-rose-600 hover:text-white transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m4-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v2" />
        </svg>
      </button>
    </div>

    <Teleport to="body">
      <div v-if="isEditModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
        <div class="bg-white rounded-[2rem] p-8 w-full max-w-md shadow-2xl" @click.stop>
          <h3 class="text-xl font-black text-slate-900 mb-6">Szolgáltatás szerkesztése</h3>
          <div class="space-y-4">
            <div>
              <label class="block text-xs font-black uppercase text-slate-400 mb-1 ml-1">Megnevezés</label>
              <input v-model="editForm.name" class="w-full p-3 rounded-xl ring-1 ring-slate-200 focus:ring-2 focus:ring-[#36082A] outline-none" />
            </div>
            <div>
              <label class="block text-xs font-black uppercase text-slate-400 mb-1 ml-1">Időtartam (perc)</label>
              <input v-model="editForm.time" type="number" class="w-full p-3 rounded-xl ring-1 ring-slate-200 focus:ring-2 focus:ring-[#36082A] outline-none" />
            </div>
            <div class="flex gap-3 pt-4">
              <button @click="isEditModalOpen = false" class="flex-1 px-4 py-3 rounded-xl font-bold text-slate-500 hover:bg-slate-100 transition-all">Mégse</button>
              <button @click="submitUpdate" class="flex-1 px-4 py-3 rounded-xl font-bold bg-[#36082A] text-white hover:opacity-90 transition-all">Mentés</button>
            </div>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>