import { defineStore } from "pinia";
import { ref } from "vue";

export const useScheduleStore = defineStore('schedule',() => {
    const schedule = ref<any[]>([])
    const config = useRuntimeConfig()
    const baseUrl = config.public.apiBase

    const getCsrfToken = (): string => {
        if (import.meta.server) return ''
        const match = document.cookie.match(new RegExp('(^| )XSRF-TOKEN=([^;]+)'))
        return match?.[2] ? decodeURIComponent(match[2]) : ''
    } 
    async function fetchSchedule() {
        try {
          const res = await $fetch<{ data: any[] }>(`${baseUrl}/api/schedule`, {
            credentials: 'include',
            headers: {
             Accept: 'application/json',
             'X-XSRF-TOKEN': getCsrfToken(),
            },
            
          })
          schedule.value = res.data ?? []
        } catch (error) {
          console.error('Failed to fetch schedule:', error)
        }
    }

    return { 
      schedule, 
      fetchSchedule 
    }
})