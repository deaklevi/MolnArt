<script setup>
import { ref, watch } from 'vue'
import { useCustomerStore } from '@/stores/customerStore'

const props = defineProps({
  customer: Object
})

const customerStore = useCustomerStore()

const isEditing = ref(false)
const isSaving = ref(false)
const editForm = ref({
  name: '',
  email: '',
  phone_number: ''
})

const startEditing = () => {
  editForm.value = {
    name: props.customer.name,
    email: props.customer.email,
    phone_number: props.customer.phone_number
  }
  isEditing.value = true
}

const cancelEditing = () => {
  isEditing.value = false
}

const saveCustomer = async () => {
  isSaving.value = true
  try {
    await customerStore.updateCustomer(props.customer.id, editForm.value)
    isEditing.value = false
  } catch (error) {
    console.error('Hiba történt mentéskor:', error)
  } finally {
    isSaving.value = false
  }
}
watch(() => props.customer.id, () => {
  isEditing.value = false
})
</script>

<template>
  <div class="border border-slate-200 h-fit bg-white shadow-lg rounded-2xl p-6 relative">
    
    <button 
      v-if="!isEditing" 
      @click="startEditing"
      class="absolute top-4 right-4 text-slate-400 hover:text-blue-500 transition-colors p-2"
      title="Ügyfél szerkesztése"
    >
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
      </svg>
    </button>

    <div class="text-center border-b pb-6">
      <div v-if="!isEditing">
        <h2 class="font-black text-3xl text-slate-800">{{ customer.name }}</h2>
        <p class="text-slate-500 mt-2">{{ customer.email }} <span class="font-semibold text-lg"> | </span> {{ customer.phone_number }}</p>
      </div>

      <div v-else class="flex flex-col gap-3 max-w-xs mx-auto text-left mt-2">
        <div>
          <label class="text-xs font-bold text-slate-500 uppercase">Név</label>
          <input v-model="editForm.name" type="text" class="w-full mt-1 px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <div>
          <label class="text-xs font-bold text-slate-500 uppercase">E-mail</label>
          <input v-model="editForm.email" type="email" class="w-full mt-1 px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <div>
          <label class="text-xs font-bold text-slate-500 uppercase">Telefonszám</label>
          <input v-model="editForm.phone_number" type="text" class="w-full mt-1 px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        
        <div class="flex justify-between gap-2 mt-4">
          <button @click="cancelEditing" :disabled="isSaving" class="px-4 py-2 w-full border border-slate-300 text-slate-600 rounded-lg font-bold hover:bg-slate-50 transition-colors disabled:opacity-50">
            Mégse
          </button>
          <button @click="saveCustomer" :disabled="isSaving" class="px-4 py-2 w-full bg-[#36082A] text-white rounded-lg font-bold hover:bg-blue-700 transition-colors disabled:opacity-50 flex justify-center items-center">
            <span v-if="isSaving">...</span>
            <span v-else>Mentés</span>
          </button>
        </div>
      </div>
    </div>

    <div class="mt-6">
      <h3 class="font-bold text-lg mb-4 text-center mt-10">Időpont előzmények</h3>
      <CustomersAppointments :appointments="customer.appointments" class="h-[24rem]" />
    </div>
  </div>
</template>