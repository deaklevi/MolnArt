<!-- components/admin/AppointmentModal.vue -->
<script setup lang="ts">
const props = defineProps<{
  appointment: Record<string, any>
  isNew: boolean
  services: { id: number; name: string }[]
}>()

const showProductModal = ref(false)
const allProducts = ref<any[]>([])

const config = useRuntimeConfig()
const baseUrl = config.public.apiBase


type AppointmentForm = {
  appointment_from: string
  appointment_to: string
  service?: string
  customer_id?: number | null
  user_id?: number | null
  email?: string
  name?: string
  phone_number?: string
  used_products?: any[]
}

const emit = defineEmits<{
  save:   [data: AppointmentForm]
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
})

const products = ref<any[]>(
  (props.appointment.products ?? []).map((p: any) => ({
    ...p,
    quantity: p.quantity ?? 1
  }))
)


const submit = () => emit('save', {
  ...form,
  used_products: products.value.map((p:any)=>({
    product_id: p.id,
    quantity: p.quantity,
  }))
} as AppointmentForm)

let selection = ref(0);
function handleSelect(number: number){
  selection.value = number;
  console.log(selection);
}

function addProduct(product: any) {
  const exists = products.value.find(p => p.id === product.id)

  if (exists) {
    exists.quantity += 1
    return
  }

  products.value.push(product)
}

onMounted(async () => {
  fetchBreaks();
  const res = await $fetch<{ data: any[] }>(`${baseUrl}/api/products`, {
    credentials: 'include'
  })

  allProducts.value = res.data ?? []
})

const breakForm = ref({
  date: '',
  start: '',
  end: ''
})



//breaks
const breaks = ref<any[]>([])

async function fetchBreaks() {
    const res = await $fetch<{ data: any[] }>(`${baseUrl}/api/unavailabilities`, {
    credentials: 'include'
  })

  breaks.value = res.data ?? []
}

async function createBreak() {
  if (!breakForm.value.date || !breakForm.value.start || !breakForm.value.end) return

  await $fetch(`${baseUrl}/api/unavailabilities`, {
    method: 'POST',
    credentials: 'include',
    body: {
      date: breakForm.value.date,
      start: breakForm.value.start,
      end: breakForm.value.end
    }
  })

  breakForm.value.start = ''
  breakForm.value.end = ''

  await fetchBreaks()
}

async function deleteBreak(id: number) {
  await $fetch(`${baseUrl}/api/unavailabilities/${id}`, {
    method: 'DELETE',
    credentials: 'include'
  })

  await fetchBreaks()
}


</script>

<template >

  <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg h-[90vh] flex flex-col p-4">
      <div class="flex flex-row justify-around pb-4 border-b">
        <h2 class="cursor-pointer hover:underline" @click="handleSelect(0)">Időpont</h2>
        <h2 class="cursor-pointer hover:underline" @click="handleSelect(1)">Foglalhatóság</h2>
        <h2 class="cursor-pointer hover:underline" @click="handleSelect(2)">Egyéb elfoglaltság</h2>
        <button @click="emit('close')" class="text-gray-400 hover:text-gray-600">✕</button>
      </div>
      

    
      <div class="flex-1 overflow-y-auto pt-3">
        <div id="appointment" v-if="selection=== 0">
        <div class="flex justify-between items-center">
        <h2 class="text-lg font-semibold mb-6">
          {{ isNew ? 'Időpont hozzáadása' : 'Időpont szerkesztés' }}
        </h2>
       
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
      <div class="flex justify-between items-center">
        <h2 class="pt-2 text-md font-semibold my-3">
          Használt termékek
        </h2>
      
        <button
          @click="showProductModal = true"
          class="text-sm px-3 py-1 bg-violet-600 text-white rounded-md"
        >
          + Hozzáadás
        </button>
      </div>

      <div class="h-[20rem] flex flex-col gap-2 overflow-y-auto">
        <div class="h-[20rem] flex flex-col gap-2 overflow-y-auto">
          <AppointmentProductCard  
            v-for="(product,i) in products"
            :key="product.id"
            :product="product"
            :orderNum="i"
            @update:quantity="products[i].quantity = $event"
            @remove="products.splice(i, 1)"
          />
        </div>

      </div>
      <div class="flex justify-between pt-10">
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

        <div v-if="selection === 1" class="space-y-3">
          <h2 class="text-lg font-semibold">Állitsd be mettől meddig lehet foglalni</h2>
    
          
          <ScheduleModalSection />
          
        </div>

        <div v-if="selection === 2" class="space-y-4">

  <h2 class="text-lg font-semibold">
    Egyéb elfoglaltság (szünetek / blokkolt idő)
  </h2>

  <!-- CREATE BLOCK -->
  <div class="p-3 border rounded-lg space-y-2">

    <input
      v-model="breakForm.date"
      type="date"
      class="w-full border rounded p-2"
    />

    <div class="grid grid-cols-2 gap-2">
      <input
        v-model="breakForm.start"
        type="time"
        class="border rounded p-2"
      />
      <input
        v-model="breakForm.end"
        type="time"
        class="border rounded p-2"
      />
    </div>

    <button
      @click="createBreak"
      class="w-full bg-red-600 text-white py-2 rounded"
    >
      + Elfoglaltság hozzáadása
    </button>
  </div>

  <!-- LIST -->
  <div class="space-y-2">

    <div
      v-for="b in breaks"
      :key="b.id"
      class="flex justify-between items-center border rounded p-2"
    >

      <div>
        <p class="text-sm font-medium">{{ b.date }}</p>
        <p class="text-xs text-gray-500">
          {{ b.start }} - {{ b.end }}
        </p>
      </div>

      <button
        @click="deleteBreak(b.id)"
        class="text-red-500 text-sm"
      >
        törlés
      </button>

    </div>

  </div>

</div>
    
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
<ProductPickerModal
  v-model="showProductModal"
  :products="allProducts"
  @add="addProduct"
/>

</template>