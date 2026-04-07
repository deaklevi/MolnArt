<!-- components/admin/AppointmentModal.vue -->
<script setup lang="ts">
const props = defineProps<{
  appointment: Record<string, any>
  isNew: boolean
}>()

const emit = defineEmits<{
  save:   [data: object]
  delete: [id: number]
  close:  []
}>()

const form = reactive({
  service:          props.appointment.service          ?? '',
  appointment_from: props.appointment.appointment_from ?? '',
  appointment_to:   props.appointment.appointment_to   ?? '',
  name:             props.appointment.customer?.name         ?? '',
  email:            props.appointment.customer?.email        ?? '',
  phone_number:     props.appointment.customer?.phone_number ?? '',
  customer_id:      props.appointment.customer?.id           ?? null,
  user_id:          props.appointment.user_id                ?? null,
})

const submit = () => emit('save', { ...form })
</script>

<template>
  <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6 space-y-4">

      <div class="flex justify-between items-center">
        <h2 class="text-lg font-semibold">
          {{ isNew ? 'Időpont hozzáadása' : 'Időpont szerkesztés' }}
        </h2>
        <button @click="emit('close')" class="text-gray-400 hover:text-gray-600">✕</button>
      </div>

      <div class="space-y-3">
        <input v-model="form.service"      placeholder="Service"
          class="w-full border rounded-lg px-3 py-2 text-sm" />
        <input v-model="form.name"         placeholder="Customer name"
          class="w-full border rounded-lg px-3 py-2 text-sm" />
        <input v-model="form.email"        placeholder="Email"
          class="w-full border rounded-lg px-3 py-2 text-sm" />
        <input v-model="form.phone_number" placeholder="Phone"
          class="w-full border rounded-lg px-3 py-2 text-sm" />

        <div class="grid grid-cols-2 gap-2">
          <div>
            <label class="text-xs text-gray-500">Tól</label>
            <input v-model="form.appointment_from" type="datetime-local"
              class="w-full border rounded-lg px-3 py-2 text-sm" />
          </div>
          <div>
            <label class="text-xs text-gray-500">Ig</label>
            <input v-model="form.appointment_to" type="datetime-local"
              class="w-full border rounded-lg px-3 py-2 text-sm" />
          </div>
        </div>
      </div>

      <div class="flex justify-between pt-2">
        <button v-if="!isNew"
          @click="emit('delete', appointment.id)"
          class="text-white text-sm bg-red-600 rounded-md px-3">
          Lemondás
        </button>
        <button @click="submit"
          class="ml-auto bg-violet-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-violet-700">
          {{ isNew ? 'Foglalás' : 'Mentés' }}
        </button>
      </div>

    </div>
  </div>
</template>