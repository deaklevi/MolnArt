import { defineStore } from 'pinia'
import { ref } from 'vue'
//type
export type AppointmentForm = {
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

export const useAppointmentStore = defineStore('appointment', () => {
  const config = useRuntimeConfig()
  const baseUrl = config.public.apiBase

  //state
  const appointments = ref<any[]>([])
  const breaks = ref<any[]>([])
  const isLoading = ref(true)

  //token
  const getCsrfToken = (): string => {
    if (import.meta.server) return ''
    const match = document.cookie.match(new RegExp('(^| )XSRF-TOKEN=([^;]+)'))
    return match?.[2] ? decodeURIComponent(match[2]) : ''
  }

  function formatForInput(date: string) {
    return date?.slice(0, 16)
  }

  function toBackendFormat(local: string) {
    return local.replace('T', ' ') + ':00'
  }

  //methods
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

  async function patchAppointmentTime(id: string, from: string, to: string) {
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
  }


  async function saveAppointment(formData: AppointmentForm, isNewBooking: boolean, id?: number | string) {
    const url = isNewBooking ? `${baseUrl}/api/appointments` : `${baseUrl}/api/appointments/${id}`
    const method = isNewBooking ? 'POST' : 'PUT'

    await $fetch(`${baseUrl}/sanctum/csrf-cookie`, { credentials: 'include' })

    await $fetch(url, {
      method,
      credentials: 'include',
      headers: {
        'X-XSRF-TOKEN': getCsrfToken(),
        Accept: 'application/json',
      },
      body: {
        ...formData,
        customer_id: formData.customer_id ?? null, // Ha nincs, null
        user_id: formData.user_id, // ITT HASZNÁLJUK A DINAMIKUS ID-T
        appointment_from: formData.appointment_from, // A formatálást az index.vue-ban végezzük el
        appointment_to: formData.appointment_to,
      }
    })
  }

  async function deleteAppointment(id: number) {
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
    await fetchAppointments()
  }

  return {
    appointments,
    isLoading,
    fetchAppointments,
    patchAppointmentTime,
    saveAppointment,
    deleteAppointment,
    formatForInput,
  }
})