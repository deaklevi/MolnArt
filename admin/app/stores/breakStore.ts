import { defineStore } from "pinia";
import { ref } from "vue";

export const useBreakStore = defineStore('break', () =>{
    const breaks = ref<any[]>([])
    const config = useRuntimeConfig();
    const baseUrl = config.public.apiBase

    async function fetchBreaks() {
    const res = await $fetch<{ data: any[] }>(`${baseUrl}/api/unavailabilities`, {
      credentials: 'include'
    })
    breaks.value = res.data ?? []}

    async function createBreak(data:{date:string,start:string,end:string}){
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

    return { 
      breaks, 
      fetchBreaks, 
      createBreak, 
      deleteBreak 
    }
})