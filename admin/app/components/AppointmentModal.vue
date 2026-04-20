<script setup lang="ts">
import type { AppointmentForm } from '~/stores/appointmentStore'

const props = defineProps<{
  appointment: Record<string, any>
  isNew: boolean
  services: { id: number; name: string; time:number }[]
}>()

const emit = defineEmits<{
  save:   [data: AppointmentForm]
  delete: [id: number]
  close:  []
}>()

const config = useRuntimeConfig()
const baseUrl = config.public.apiBase

// Állapotok
const showProductModal = ref(false)
const showServiceModal = ref(false)
const showCustomerDropdown = ref(false)

const allProducts = ref<any[]>([])
const customers = ref<any[]>([])
const breaks = ref<any[]>([])
const selectedCustomerId = ref<number | null>(props.appointment.customer?.id ?? null)

let selection = ref(0)
function handleSelect(number: number){
  selection.value = number
}

// ── ÜGYFÉL KEZELÉS ──────────────────────────────────────────
const filteredCustomers = computed(() => {
  const q = form.name.trim().toLowerCase()
  if (!q) return []
  return customers.value.filter(
    c => c.name.toLowerCase().includes(q) || c.email.toLowerCase().includes(q)
  )
})

function onNameInput(){
  selectedCustomerId.value = null  
  showCustomerDropdown.value = true
}

function selectCustomer(customer: any) {
  form.name         = customer.name
  form.email        = customer.email
  form.phone_number = customer.phone_number
  selectedCustomerId.value  = customer.id
  showCustomerDropdown.value = false
}

function onNameBlur() {
  setTimeout(() => { showCustomerDropdown.value = false }, 150)
}

// ── SZOLGÁLTATÁS KEZELÉS ────────────────────────────────────
const matchedService = props.services?.find(s => s.name === props.appointment.service)
const displayService = ref(
  matchedService
    ? `${matchedService.name} - ${matchedService.time} perc`
    : props.appointment.service ?? ''
)

function selectService(service: { id: number; name: string; time:number }) {
  form.service = service.name
  displayService.value = `${service.name} - ${service.time} perc`
  showServiceModal.value = false
}

// ── ŰRLAP ÉS TERMÉKEK ────────────────────────────────────────
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

function addProduct(product: any) {
  const exists = products.value.find(p => p.id === product.id)
  if (exists) {
    exists.quantity += 1
    return
  }
  products.value.push(product)
}

const submit = () => emit('save', {
  ...form,
  customer_id: selectedCustomerId.value,
  used_products: products.value.map((p:any)=>({
    product_id: p.id,
    quantity: p.quantity,
  }))
} as AppointmentForm)

// ── SZÜNETEK / ELFOGLALTSÁG API ──────────────────────────────
async function fetchBreaks() {
  const res = await $fetch<{ data: any[] }>(`${baseUrl}/api/unavailabilities`, {
    credentials: 'include'
  })
  breaks.value = res.data ?? []
}

async function createBreak(data: { date: string, start: string, end: string }) {
  await $fetch(`${baseUrl}/api/unavailabilities`, {
    method: 'POST',
    credentials: 'include',
    body: data
  })
  await fetchBreaks()
}

async function deleteBreak(id: number) {
  await $fetch(`${baseUrl}/api/unavailabilities/${id}`, {
    method: 'DELETE',
    credentials: 'include'
  })
  await fetchBreaks()
}

// ── INIT ────────────────────────────────────────────────────
onMounted(async () => {
  fetchBreaks()
  const [productsRes, customersRes] = await Promise.all([
    $fetch<{ data: any[] }>(`${baseUrl}/api/products`,  { credentials: 'include' }),
    $fetch<{ data: any[] }>(`${baseUrl}/api/customers`, { credentials: 'include' }),
  ])
  allProducts.value = productsRes.data  ?? []
  customers.value   = customersRes.data ?? []
})
</script>

<template>
  <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg h-[90vh] flex flex-col p-4">
      
      <div class="flex flex-row justify-around pb-4 border-b">
        <h2 class="cursor-pointer hover:underline" :class="{'font-bold text-violet-600': selection === 0}" @click="handleSelect(0)">Időpont</h2>
        <h2 class="cursor-pointer hover:underline" :class="{'font-bold text-violet-600': selection === 1}" @click="handleSelect(1)">Foglalhatóság</h2>
        <h2 class="cursor-pointer hover:underline" :class="{'font-bold text-violet-600': selection === 2}" @click="handleSelect(2)">Egyéb elfoglaltság</h2>
        <button @click="emit('close')" class="text-gray-400 hover:text-gray-600">✕</button>
      </div>
      
      <div class="flex-1 overflow-y-auto pt-3">
        
        <div id="appointment" v-if="selection === 0">
          <h2 class="text-lg font-semibold mb-6">
            {{ isNew ? 'Időpont hozzáadása' : 'Időpont szerkesztés' }}
          </h2>

          <div class="space-y-4">
            <div class="grid grid-cols-2 gap-3">
              <div class="relative" @blur.capture="onNameBlur">
                <input
                  v-model="form.name"
                  placeholder="Customer name"
                  class="w-full border rounded-lg px-3 py-2 text-sm"
                  :class="selectedCustomerId ? 'border-violet-400 bg-violet-50' : ''"
                  autocomplete="off"
                  @input="onNameInput"
                  @focus="showCustomerDropdown = true"
                />
                <span v-if="selectedCustomerId" class="absolute right-2 top-1/2 -translate-y-1/2 text-xs text-violet-500 font-medium pointer-events-none">
                  ✓ meglévő ügyfél
                </span>
                
                <ul v-if="showCustomerDropdown && filteredCustomers.length" class="absolute z-10 w-full bg-white border rounded-lg shadow-lg mt-1 max-h-48 overflow-y-auto">
                  <li
                    v-for="customer in filteredCustomers" :key="customer.id"
                    @mousedown.prevent="selectCustomer(customer)"
                    class="px-3 py-2 cursor-pointer hover:bg-violet-50 text-sm"
                  >
                    <span class="font-medium">{{ customer.name }}</span>
                    <span class="text-gray-400 ml-2 text-xs">{{ customer.email }}</span>
                  </li>
                </ul>
              </div>
              <input v-model="form.phone_number" placeholder="Phone" class="w-full border rounded-lg px-3 py-2 text-sm" />
            </div>

            <input v-model="form.email" placeholder="Email" class="w-full border rounded-lg px-3 py-2 text-sm" />
            
            <div class="grid grid-cols-10 gap-2">
              <input 
                v-model="displayService" placeholder="Service"
                class="w-full border rounded-lg px-3 py-2 text-sm col-span-9" readonly
              />
              <button @click="showServiceModal = true" class="flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
                  <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                </svg>
              </button>
            </div>
              
            <div class="grid grid-cols-2 gap-2">
              <div>
                <label class="text-xs text-gray-500">Tól</label>
                <input v-model="form.appointment_from" type="datetime-local" class="w-full border rounded-lg px-3 py-2 text-sm" />
              </div>
              <div>
                <label class="text-xs text-gray-500">Ig</label>
                <input v-model="form.appointment_to" type="datetime-local" class="w-full border rounded-lg px-3 py-2 text-sm" />
              </div>
            </div>
          </div>
          
          <div class="flex justify-between items-center mt-4 mb-3">
            <h2 class="text-md font-semibold">Használt termékek</h2>
            <button @click="showProductModal = true" class="text-sm px-3 py-1 bg-violet-600 text-white rounded-md">
              + Hozzáadás
            </button>
          </div>

          <div class="h-[20rem] flex flex-col gap-2 overflow-y-auto">
            <AppointmentProductCard  
              v-for="(product,i) in products" :key="product.id"
              :product="product" :orderNum="i"
              @update:quantity="products[i].quantity = $event"
              @remove="products.splice(i, 1)"
            />
          </div>

          <div class="flex justify-between pt-10">
            <button v-if="!isNew" @click="emit('delete', appointment.id)" class="text-white text-sm bg-red-600 rounded-md px-3 py-2">
              Lemondás
            </button>
            <button @click="submit" class="ml-auto bg-violet-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-violet-700">
              {{ isNew ? 'Foglalás' : 'Mentés' }}
            </button>
          </div>
        </div>

        <div v-if="selection === 1" class="space-y-3">
          <h2 class="text-lg font-semibold">Állítsd be mettől meddig lehet foglalni</h2>
          <ScheduleModalSection />
        </div>

        <div v-if="selection === 2">
          <AppointmentBreaksTab 
            :breaks="breaks" 
            @add="createBreak" 
            @delete="deleteBreak" 
          />
        </div>

      </div>
    </div>
  </div>

  <ServicePickerModa
    :show="showServiceModal"
    :services="services"
    @select="selectService"
    @close="showServiceModal = false"
  />

  <ProductPickerModal
    v-model="showProductModal"
    :products="allProducts"
    @add="addProduct"
  />
</template>