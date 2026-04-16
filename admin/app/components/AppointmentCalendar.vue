<!-- components/admin/AppointmentCalendar.vue -->
<script setup lang="ts">

import FullCalendar from '@fullcalendar/vue3'
import timeGridPlugin from '@fullcalendar/timegrid'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import type {
  CalendarOptions,
  EventClickArg,
  DateSelectArg,
  EventDropArg,
} from '@fullcalendar/core'
import type { EventResizeDoneArg } from '@fullcalendar/interaction'

const config = useRuntimeConfig()
const baseUrl = config.public.apiBase

// ── TYPES ─────────────────────────────────────────────
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

// ── STATE ─────────────────────────────────────────────
const appointments = ref<any[]>([])
const isLoading    = ref(true)
const showModal    = ref(false)
const selectedAppt = ref<any>(null)
const isNewBooking = ref(false)
const selectedIds  = ref<number[]>([])

// ── HELPERS ───────────────────────────────────────────

// convert backend → input
function formatForInput(date: string) {
  return date?.slice(0, 16)
}

// convert input → backend
function toBackendFormat(local: string) {
  return local.replace('T', ' ') + ':00'
}

const getCsrfToken = (): string => {
  if (import.meta.server) return ''
  const match = document.cookie.match(new RegExp('(^| )XSRF-TOKEN=([^;]+)'))
  return match?.[2] ? decodeURIComponent(match[2]) : ''
}

// ── FETCH USER ────────────────────────────────────────
const { data: user } = await useFetch(`${baseUrl}/api/user`, {
  credentials: 'include',
  server: false,
  lazy: true
})

watch(user as any, (newUser: any) => {
  if (newUser?.services) {
    selectedIds.value = newUser.services.map((s: any) => s.id)
  }
}, { immediate: true })

// ── FETCH SERVICES ────────────────────────────────────
const { data: allServices } = await useFetch(`${baseUrl}/api/services`, {
  server: false,
  lazy: true,
  transform: (res:any) => res.data || res
})

const userServices = computed(() => {
  if (!allServices.value) return []
  return allServices.value.filter((s: any) =>
    selectedIds.value.includes(s.id)
  )
})

// ── FETCH APPOINTMENTS ────────────────────────────────
async function fetchAppointments() {
  isLoading.value = true
  try {
    const res = await $fetch<{ data: any[] }>(`${baseUrl}/api/appointments`, {
      credentials: 'include',
      headers: {
        Accept: 'application/json',
        'X-XSRF-TOKEN': getCsrfToken(),
      },
    })
    appointments.value = res.data
  } finally {
    isLoading.value = false
  }
}


// ── EVENTS ────────────────────────────────────────────
const calendarEvents = computed(() =>
  appointments.value.map((appt) => ({
    id: String(appt.id),
    title: `${appt.customer?.name ?? 'Unknown'} — ${appt.service}`,
    start: appt.appointment_from,
    end: appt.appointment_to,
    backgroundColor: '#7c3aed',
    borderColor: '#6d28d9',
    extendedProps: { raw: appt },
  }))
)

// ── CALENDAR CONFIG ───────────────────────────────────
const calendarOptions = computed<CalendarOptions>(() => ({
  plugins: [timeGridPlugin, dayGridPlugin, interactionPlugin],
  initialView: 'timeGridWeek',

  headerToolbar: {
    left: 'prev,next today',
    center: 'title',
    right: 'dayGridMonth,timeGridWeek,timeGridDay',
  },

  events: calendarEvents.value,
  editable: true,
  selectable: true,
  nowIndicator: true,
  selectMirror: true,

  slotMinTime: '06:00:00',
  slotMaxTime: '22:00:00',
  allDaySlot: false,
  height: 'auto',

  eventClick: handleEventClick,
  eventDrop: handleEventDrop,
  eventResize: handleEventResize,
  select: handleSlotSelect,
  
  slotLabelFormat: {
    hour: '2-digit',
    minute: '2-digit',
    hour12: false,
  },

  eventTimeFormat: {
    hour: '2-digit',
    minute: '2-digit',
    hour12: false,
  },

  dayHeaderFormat: {
    month: '2-digit',
    day: '2-digit',
    omitCommas: true,
  },

  locale: 'hu',
  timeZone: 'UTC',
}))

// ── HANDLERS ─────────────────────────────────────────

// CLICK EXISTING
function handleEventClick(info: EventClickArg) {
  selectedAppt.value = {
    ...info.event.extendedProps.raw,
    appointment_from: formatForInput(info.event.startStr),
    appointment_to: formatForInput(info.event.endStr),
  }
  isNewBooking.value = false
  showModal.value = true
}

// SELECT NEW SLOT
function handleSlotSelect(info: DateSelectArg) {
  selectedAppt.value = {
    appointment_from: formatForInput(info.startStr),
    appointment_to: formatForInput(info.endStr),
  }
  isNewBooking.value = true
  showModal.value = true
}

// DRAG
async function handleEventDrop(info: EventDropArg) {
  await patchAppointmentTime(
    info.event.id,
    info.event.startStr,
    info.event.endStr,
    info.revert
  )
}

// RESIZE
async function handleEventResize(info: EventResizeDoneArg) {
  await patchAppointmentTime(
    info.event.id,
    info.event.startStr,
    info.event.endStr,
    info.revert
  )
}

// UPDATE TIME
async function patchAppointmentTime(
  id: string,
  from: string,
  to: string,
  revert: () => void
) {
  try {
    await $fetch(`${baseUrl}/api/appointments/${id}`, {
      method: 'PUT',
      credentials: 'include',
      headers: {
        'X-XSRF-TOKEN': getCsrfToken(),
      },
      body: {
        appointment_from: toBackendFormat(formatForInput(from)),
        appointment_to: toBackendFormat(formatForInput(to)),
      },
    })
    await fetchAppointments()
  } catch {
    revert()
    alert('Időpont ütközés!')
  }
}

// SAVE (CREATE / UPDATE)
async function handleSave(formData: AppointmentForm) {
  const url = isNewBooking.value
    ? `${baseUrl}/api/appointments`
    : `${baseUrl}/api/appointments/${selectedAppt.value.id}`

  const method = isNewBooking.value ? 'POST' : 'PUT'

  await $fetch(`${baseUrl}/sanctum/csrf-cookie`, {
    credentials: 'include'
  })

  await $fetch(url, {
    method,
    credentials: 'include',
    headers: {
      'X-XSRF-TOKEN': getCsrfToken(),
      Accept: 'application/json',
    },
    body: {
      ...formData,
      customer_id: formData.customer_id ?? 1, 
      user_id: 1,
      appointment_from: toBackendFormat(formData.appointment_from),
      appointment_to: toBackendFormat(formData.appointment_to),
    }
  })

  showModal.value = false
  await fetchAppointments()
}

// DELETE
async function handleDelete(id: number) {
  await $fetch(`${baseUrl}/sanctum/csrf-cookie`, {
    credentials: 'include'
  })

  await $fetch(`${baseUrl}/api/appointments/${id}`, {
    method: 'DELETE',
    credentials: 'include',
    headers: {
      'X-XSRF-TOKEN': getCsrfToken(),
    }
  })

  showModal.value = false
  await fetchAppointments()
}

onMounted(fetchAppointments)

</script>

<template>
  <div class="p-6">
    <div v-if="isLoading" class="text-center py-20 text-gray-400">
      Loading appointments...
    </div>

    <ClientOnly v-else>
      <FullCalendar :options="calendarOptions" />
    </ClientOnly>

    <AppointmentModal
      v-if="showModal"
      :appointment="selectedAppt"
      :is-new="isNewBooking"
      :services="userServices"
      @save="handleSave"
      @delete="handleDelete"
      @close="showModal = false"
    />
  </div>
</template>