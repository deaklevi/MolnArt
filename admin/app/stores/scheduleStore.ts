import { defineStore } from "pinia";
import { ref } from "vue";

export const useScheduleStore = defineStore('schedule',() => {
    const schedule = ref<any[]>([])
    const config = useRuntimeConfig()
    const baseUrl = config.public.apiBase

    async function fetchSchedule() {
        try {
          const res = await $fetch<{ data: any[] }>(`${baseUrl}/api/schedule`, {
            credentials: 'include'
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