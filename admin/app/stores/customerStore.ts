import { defineStore } from 'pinia'

export type Customer = {
  id?: number
  name: string
  email?: string
  phone_number?: string
}

export const useCustomerStore = defineStore('customer', () => {
  const config = useRuntimeConfig()
  const baseUrl = config.public.apiBase

  const customers = ref<Customer[]>([])
  const isLoading = ref(false)


  const getCsrfToken = (): string => {
    if (import.meta.server) return ''
    const match = document.cookie.match(new RegExp('(^| )XSRF-TOKEN=([^;]+)'))
    return match?.[2] ? decodeURIComponent(match[2]) : ''
  }


  async function fetchCustomers() {
    isLoading.value = true
    try {
      const res = await $fetch<{ data: Customer[] }>(`${baseUrl}/api/customers`, {
        credentials: 'include',
        headers: {
          Accept: 'application/json',
          'X-XSRF-TOKEN': getCsrfToken(),
        },
      })
      customers.value = res.data ?? []
    } catch (error) {
      console.error('Hiba az ügyfelek betöltésekor:', error)
    } finally {
      isLoading.value = false
    }
  }

  async function createCustomer(customerData: Customer) {
    await $fetch(`${baseUrl}/sanctum/csrf-cookie`, { credentials: 'include' })

    await $fetch(`${baseUrl}/api/customers`, {
      method: 'POST',
      credentials: 'include',
      headers: {
        'X-XSRF-TOKEN': getCsrfToken(),
        Accept: 'application/json',
      },
      body: customerData,
    })
    
    await fetchCustomers()
  }

  async function updateCustomer(id: number, customerData: Partial<Customer>) {
    await $fetch(`${baseUrl}/sanctum/csrf-cookie`, { credentials: 'include' })

    await $fetch(`${baseUrl}/api/customers/${id}`, {
      method: 'PUT',
      credentials: 'include',
      headers: {
        'X-XSRF-TOKEN': getCsrfToken(),
        Accept: 'application/json',
      },
      body: customerData,
    })

    await fetchCustomers()
  }

  async function deleteCustomer(id: number) {
    await $fetch(`${baseUrl}/sanctum/csrf-cookie`, { credentials: 'include' })

    await $fetch(`${baseUrl}/api/customers/${id}`, {
      method: 'DELETE',
      credentials: 'include',
      headers: {
        'X-XSRF-TOKEN': getCsrfToken(),
        Accept: 'application/json',
      },
    })

    await fetchCustomers()
  }

  return {
    customers,
    isLoading,
    fetchCustomers,
    createCustomer,
    updateCustomer,
    deleteCustomer,
  }
})