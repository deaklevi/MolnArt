<!-- components/admin/AppointmentCalendar.vue -->
<script setup lang="ts">
definePageMeta({ middleware: 'auth' });
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

// ── State ──────────────────────────────────────────────────────────
const appointments = ref<any[]>([])
const isLoading    = ref(true)
const showModal    = ref(false)
const selectedAppt = ref<any>(null)
const isNewBooking = ref(false)

// ── Data fetching ──────────────────────────────────────────────────
async function fetchAppointments() {
  isLoading.value = true
  try {
    const res = await $fetch<{ data: any[] }>(`${baseUrl}/api/appointments`, {
      credentials: 'include',
    })
    appointments.value = res.data
  } finally {
    isLoading.value = false
  }
}

// ── Map to FullCalendar event format ───────────────────────────────
const calendarEvents = computed(() =>
  appointments.value.map((appt) => ({
    id:    String(appt.id),
    title: `${appt.customer?.name ?? 'Unknown'} — ${appt.service}`,
    start: appt.appointment_from,
    end:   appt.appointment_to,
    backgroundColor: '#7c3aed',
    borderColor:     '#6d28d9',
    extendedProps: { raw: appt },
  }))
)

// ── FullCalendar options ───────────────────────────────────────────
const calendarOptions = computed<CalendarOptions>(() => ({
  plugins: [timeGridPlugin, dayGridPlugin, interactionPlugin],
  initialView:  'timeGridWeek',
  headerToolbar: {
    left:   'prev,next today',
    center: 'title',
    right:  'dayGridMonth,timeGridWeek,timeGridDay',
  },
  events:        calendarEvents.value,
  editable:      true,   
  selectable:    true,   
  selectMirror:  true,
  nowIndicator:  true,
  slotMinTime:   '06:00:00',
  slotMaxTime:   '22:00:00',
  allDaySlot:    false,
  height:        'auto',
  eventClick:    handleEventClick,
  eventDrop:     handleEventDrop,
  eventResize:   handleEventResize,
  select:        handleSlotSelect,

    slotLabelFormat: {
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

// ── Handlers ──────────────────────────────────────────────────────
function handleEventClick(info: EventClickArg) {
  selectedAppt.value = { ...info.event.extendedProps.raw }
  isNewBooking.value = false
  showModal.value    = true
}

function handleSlotSelect(info: DateSelectArg) {
  selectedAppt.value = {
    appointment_from: info.startStr,
    appointment_to:   info.endStr,
  }
  isNewBooking.value = true
  showModal.value    = true
}

async function handleEventDrop(info: EventDropArg) {
  await patchAppointmentTime(
    info.event.id,
    info.event.startStr,
    info.event.endStr,
    info.revert,
  )
}

async function handleEventResize(info: EventResizeDoneArg) {
  await patchAppointmentTime(
    info.event.id,
    info.event.startStr,
    info.event.endStr,
    info.revert,
  )
}

// Shared logic for drop and resize
async function patchAppointmentTime(
  id: string,
  from: string,
  to: string,
  revert: () => void,
) {
  try {
    await $fetch(`${baseUrl}/api/appointments/${id}`, {
      method:      'PUT',
      credentials: 'include',
      body: {
        appointment_from: from,
        appointment_to:   to,
      },
    })
    await fetchAppointments()
  } catch {
    revert()
    alert('Time slot conflict — booking reverted.')
  }
}

async function handleSave(formData: object) {
  const url = isNewBooking.value
    ? `${baseUrl}/api/appointments`
    : `${baseUrl}/api/appointments/${selectedAppt.value.id}`
  const method = isNewBooking.value ? 'POST' : 'PUT'

  await $fetch(url, { method, credentials: 'include', body: formData })
  showModal.value = false
  await fetchAppointments()
}

async function handleDelete(id: number) {
  await $fetch(`${baseUrl}/api/appointments/${id}`, {
    method:      'DELETE',
    credentials: 'include',
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
      @save="handleSave"
      @delete="handleDelete"
      @close="showModal = false"
    />
  </div>
</template>