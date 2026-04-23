<script setup lang="ts">
import { storeToRefs } from 'pinia'
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

import { useAppointmentStore, type AppointmentForm } from '@/stores/appointmentStore'
import { useBreakStore } from '@/stores/breakStore'
import { useScheduleStore } from '@/stores/scheduleStore'
import { ref, reactive, computed, watch, onMounted, onBeforeUnmount } from 'vue'

const config = useRuntimeConfig()
const baseUrl = config.public.apiBase
const calendarRef = ref<any>(null)

const appointmentStore = useAppointmentStore()
const breakStore = useBreakStore()
const scheduleStore = useScheduleStore()
const { appointments, isLoading } = storeToRefs(appointmentStore)

const showModal = ref(false)
const selectedAppt = ref<any>(null)
const isNewBooking = ref(false)
const selectedIds = ref<number[]>([])
const isMobile = ref(false)

const calendarOptions = reactive<CalendarOptions>({
  plugins: [timeGridPlugin, dayGridPlugin, interactionPlugin],
  initialView: window.innerWidth < 768 ? 'timeGridDay' : 'timeGridWeek',
  firstDay: 1,
  editable: true,
  selectable: true,
  nowIndicator: true,
  selectMirror: true,
  slotMinTime: '07:00:00',
  slotMaxTime: '21:30:00',
  allDaySlot: false,
  height: '85%',
  locale: 'hu',
  timeZone: 'UTC',
  slotDuration: '00:30:00',
  slotLabelInterval: '00:30:00',
  snapDuration: '00:05:00',
  contentHeight: 'auto',
  expandRows: true,
  longPressDelay: 300,
  eventLongPressDelay: 300,
  selectLongPressDelay: 300,
  eventStartEditable: true,
  eventDurationEditable: true,
  eventClick: handleEventClick,
  eventDrop: handleEventDrop,
  eventResize: handleEventResize,
  select: handleSlotSelect,
  slotLabelFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
  eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
  dayHeaderFormat: { month: '2-digit', day: '2-digit', omitCommas: true },
  buttonText: { today: "Ma", week: "Hét", day: "Nap" },
})

const updateResponsiveUI = () => {
  isMobile.value = window.innerWidth < 768
  calendarOptions.headerToolbar = isMobile.value
    ? { left: 'prev,next today', center: 'title', right: 'timeGridDay,timeGridWeek' }
    : { left: 'prev,next today', center: 'title', right: 'timeGridWeek,timeGridDay' }

  calendarOptions.titleFormat = isMobile.value 
    ? { month: 'short', day: 'numeric' } 
    : { year: 'numeric', month: 'long', day: 'numeric' }
}

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

const { data: allServices } = await useFetch(`${baseUrl}/api/services`, {
  server: false,
  lazy: true,
  transform: (res: any) => res.data || res
})

const userServices = computed(() => {
  if (!allServices.value) return []
  return allServices.value.filter((s: any) =>
    selectedIds.value.includes(s.id)
  )
})

async function loadAppointments() {
  await appointmentStore.fetchAppointments()
}

const calendarEvents = computed(() => {
  const normalAppointments = appointmentStore.appointments.map((app: any) => ({
    id: app.id,
    title: `${app.customer.name} `,
    start: app.appointment_from,
    end: app.appointment_to,
    extendedProps: { raw: JSON.parse(JSON.stringify(app)) },
  }))

  const backgroundBreaks = breakStore.breaks.map((b: any) => ({
    start: `${b.date}T${b.start}`,
    end: `${b.date}T${b.end}`,
    display: 'background',
    color: '#ef4444' 
  }))

  const workingHours = (scheduleStore.schedule || []).map((wh: any) => ({
    start: `${wh.date}T${wh.start}`,
    end: `${wh.date}T${wh.end}`,
    display: 'background',
    color: 'lightgreen', 
  }))

  return [...workingHours, ...backgroundBreaks, ...normalAppointments]
})

watch(calendarEvents, (newEvents) => {
  calendarOptions.events = newEvents
}, { immediate: true })

function handleEventClick(info: EventClickArg) {
  selectedAppt.value = {
    ...info.event.extendedProps.raw,
    appointment_from: appointmentStore.formatForInput(info.event.startStr),
    appointment_to: appointmentStore.formatForInput(info.event.endStr),
  }
  isNewBooking.value = false
  showModal.value = true
}

function handleSlotSelect(info: DateSelectArg) {
  selectedAppt.value = {
    appointment_from: appointmentStore.formatForInput(info.startStr),
    appointment_to: appointmentStore.formatForInput(info.endStr),
  }
  isNewBooking.value = true
  showModal.value = true
}

async function updateTime(id: string, startStr: string, endStr: string, revert: () => void) {
  try {
    await appointmentStore.patchAppointmentTime(id, startStr, endStr)
  } catch {
    revert()
    alert('Időpont ütközés vagy hiba történt!')
  }
}

async function handleEventDrop(info: EventDropArg) {
  await updateTime(info.event.id, info.event.startStr, info.event.endStr, info.revert)
}

async function handleEventResize(info: EventResizeDoneArg) {
  await updateTime(info.event.id, info.event.startStr, info.event.endStr, info.revert)
}

async function handleSave(formData: AppointmentForm) {
  await appointmentStore.saveAppointment(formData, isNewBooking.value, selectedAppt.value?.id)
  showModal.value = false
}

async function handleDelete(id: number) {
  await appointmentStore.deleteAppointment(id)
  showModal.value = false
}

onMounted(async () => {
  updateResponsiveUI()
  window.addEventListener('resize', updateResponsiveUI)
  loadAppointments()
  breakStore.fetchBreaks()
  await scheduleStore.fetchSchedule()
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', updateResponsiveUI)
})
</script>

<template>
  <div class="p-6 h-screen flex flex-col relative">
    
    <div 
      v-if="isLoading" 
      class="absolute inset-0 z-50 flex items-center justify-center bg-white/60 text-gray-500 rounded-md backdrop-blur-sm"
    >
      Loading appointments...
    </div>

    <ClientOnly class="flex-1">
      <FullCalendar 
        ref="calendarRef" 
        :options="calendarOptions" 
        class="h-full" 
      />
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