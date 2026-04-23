import { defineStore } from 'pinia'
export type Serice = {
    id?: number
    name: string
    time: number
}

export const useServiceStore = defineStore('services', ()=>{
    const config = useRuntimeConfig()
    const baseUrl = config.public.apiBase
    const getCsrfToken = (): string => {
        if (import.meta.server) return ''
        const match = document.cookie.match(new RegExp('(^| )XSRF-TOKEN=([^;]+)'))
        return match?.[2] ? decodeURIComponent(match[2]) : ''
    }

    const services = ref<any[]>([]);


    async function fetchServices(){
        try{
            const res = await $fetch<{data: Serice[]}>(`${baseUrl}/api/services`,{
                credentials:'include',
                headers:{
                    Accept: 'application/json',
                    'X-XSRF-TOKEN': getCsrfToken(),
                },
            })
            services.value = res.data ?? [];
        }catch(error){
            console.error('hiba a szolgáltatások betöltésekor', error)
        }
    }

    async function createService(serviceData: Serice) {
        try {
          await $fetch(`${baseUrl}/sanctum/csrf-cookie`, { credentials: 'include' })

          await $fetch(`${baseUrl}/api/services`, {
            method: 'POST',
            credentials: 'include',
            headers: {
              'X-XSRF-TOKEN': getCsrfToken(),
              Accept: 'application/json',
            },
            body: serviceData,
          })

          await fetchServices()
        } catch (error) {
          console.error('Hiba a szolgáltatás létrehozásakor:', error)
        }
    }

    async function updateService(id: number, serviceData: Partial<Serice>) {
        try {
          await $fetch(`${baseUrl}/sanctum/csrf-cookie`, { credentials: 'include' })

          await $fetch(`${baseUrl}/api/services/${id}`, {
            method: 'PUT',
            credentials: 'include',
            headers: {
              'X-XSRF-TOKEN': getCsrfToken(),
              Accept: 'application/json',
            },
            body: serviceData,
          })

          await fetchServices()
        } catch (error) {
          console.error('Hiba a szolgáltatás frissítésekor:', error)
        }
    }

    async function deleteService(id: number) {
        try {
          await $fetch(`${baseUrl}/sanctum/csrf-cookie`, { credentials: 'include' })

          await $fetch(`${baseUrl}/api/services/${id}`, {
            method: 'DELETE',
            credentials: 'include',
            headers: {
              'X-XSRF-TOKEN': getCsrfToken(),
              Accept: 'application/json',
            },
          })

          await fetchServices()
        } catch (error) {
          console.error('Hiba a szolgáltatás törlésekor:', error)
        }
    }

    return {
        services,
        fetchServices,
        createService,
        updateService,
        deleteService,
    }

})