<!-- components/admin/AppointmentModal.vue -->
<script setup lang="ts">
const props = defineProps<{
  appointment: Record<string, any>
  isNew: boolean
  services: { id: number; name: string }[]
}>()

const emit = defineEmits<{
  save:   [data: object]
  delete: [id: number]
  close:  []
}>()

const showServiceModal = ref(false)
const search = ref('')

const filteredServices = computed(() => {
  if (!props.services) return []

  return props.services.filter((s) =>
    s.name.toLowerCase().includes(search.value.toLowerCase())
  )
})

function selectService(service: { id: number; name: string }) {
  form.service = service.name
  showServiceModal.value = false
}

const form = reactive({
  service: props.appointment.service ?? '',
  appointment_from: props.appointment.appointment_from ?? '',
  appointment_to: props.appointment.appointment_to   ?? '',
  name: props.appointment.customer?.name ?? '',
  email: props.appointment.customer?.email ?? '',
  phone_number: props.appointment.customer?.phone_number ?? '',
  customer_id: props.appointment.customer?.id ?? null,
  user_id: props.appointment.user_id ?? null,
})

const products = ref([...(props.appointment.products ?? [])])


const submit = () => emit('save', { ...form,
  used_products: products.value.map((p:any)=>({
    product_id: p.id,
    quantity: p.quantity,
  }))
})

let selection = ref(0);
function handleSelect(number: number){
  selection.value = number;
  console.log(selection);
}
</script>

<template >

  <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg min-h-[75%] p-6 space-y-4">
      
      <div class="flex flex-row justify-around pb-4">
        <h2 class="cursor-pointer hover:underline" @click="handleSelect(0)">Időpont</h2>
        <h2 class="cursor-pointer hover:underline" @click="handleSelect(1)">Foglalhatóság</h2>
        <h2 class="cursor-pointer hover:underline" @click="handleSelect(2)">Egyéb elfoglaltság</h2>
      </div>

    <div id="appointment" v-if="selection=== 0">
        <div class="flex justify-between items-center">
        <h2 class="text-lg font-semibold mb-6">
          {{ isNew ? 'Időpont hozzáadása' : 'Időpont szerkesztés' }}
        </h2>
        <button @click="emit('close')" class="text-gray-400 hover:text-gray-600">✕</button>
      </div>

      <div class="space-y-4">
        <div class="grid grid-cols-2 gap-3">
          <input v-model="form.name" placeholder="Customer name"
          class="w-full border rounded-lg px-3 py-2 text-sm" />
          <input v-model="form.phone_number" placeholder="Phone"
          class="w-full border rounded-lg px-3 py-2 text-sm" />
        </div>

        <input v-model="form.email" placeholder="Email"
        class="w-full border rounded-lg px-3 py-2 text-sm" />
        
        <div class="grid grid-cols-10 gap-2">
          <input 
            v-model="form.service"
            placeholder="Service"
            class="w-full border rounded-lg px-3 py-2 text-sm col-span-9"
            readonly
          />

          <button @click="showServiceModal = true" class="flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
              fill="none" stroke="currentColor" stroke-width="2">
              <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
            </svg>
          </button>
        </div>
        
          
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
      
      <!-- products -->
      <h2 class="pt-2 text-md font-semibold my-3">Használt termékek</h2>
      <div class="h-[15rem] flex flex-col gap-2 overflow-y-auto">
        <AppointmentProductCard  
          v-for="(product,i) in products"
          :key="product.id"
          :product="product"
          :orderNum="i"
          @update:quantity="products[i].quantity = $event"
          @remove="products.splice(i, 1)"
          class="mb-2"
          
        />

      </div>
      <div class="flex justify-between pt-5">
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
      
    <div v-if="selection === 1">
      <h2 class="text-lg font-semibold">Állitsd be mettől meddig lehet foglalni</h2>

      <ScheduleModalSection />
      
    </div>


    </div>
  </div>


  <div v-if="showServiceModal" class="fixed inset-0 bg-black/40 flex items-center justify-center z-[60]">
  <div class="bg-white w-full max-w-md rounded-xl p-4 space-y-3">

    <!-- header -->
    <div class="flex justify-between items-center">
      <h3 class="font-semibold">Szolgáltatás kiválasztása</h3>
      <button @click="showServiceModal = false">✕</button>
    </div>

    <!-- search -->
    <input
      v-model="search"
      placeholder="Keresés..."
      class="w-full border rounded-lg px-3 py-2 text-sm"
    />

    <!-- list -->
    <div class="max-h-60 overflow-y-auto space-y-2">
      <div
        v-for="service in filteredServices"
        :key="service.id"
        @click="selectService(service)"
        class="p-3 rounded-lg border cursor-pointer hover:bg-violet-50"
      >
        {{ service.name }}
      </div>
    </div>

  </div>
</div>
</template>