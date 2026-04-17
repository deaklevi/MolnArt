<script setup lang="ts">
const config  = useRuntimeConfig()
const baseUrl = config.public.apiBase

const getCsrfToken = (): string => {
  if (import.meta.server) return ''
  const match = document.cookie.match(new RegExp('(^| )XSRF-TOKEN=([^;]+)'))
  return match?.[2] ? decodeURIComponent(match[2]) : ''
}

type ScheduleEntry = { id: number; date: string; start: string; end: string }

type DayState = {
  date:   string
  label:  string
  open:   boolean
  start:  string
  end:    string
  id:     number | null
  saving: boolean
  dirty:  boolean
}

const HU_DAYS = ['Hétfő', 'Kedd', 'Szerda', 'Csütörtök', 'Péntek', 'Szombat', 'Vasárnap']

const weekOffset = ref(0)
const isLoading  = ref(false)
const days       = ref<DayState[]>([])

function getWeekDates(offset: number): string[] {
  const today  = new Date()
  const day    = today.getDay()
  const diff   = today.getDate() - day + (day === 0 ? -6 : 1)
  const monday = new Date(today)
  monday.setDate(diff + offset * 7)
  monday.setHours(0, 0, 0, 0)

  return Array.from({ length: 7 }, (_, i) => {
    const d = new Date(monday)
    d.setDate(d.getDate() + i)
    return d.toISOString().slice(0, 10)
  })
}

const weekDates = computed(() => getWeekDates(weekOffset.value))

const weekLabel = computed(() => {
  const dates  = weekDates.value
  const format = (s: string) => {
    const d = new Date(s)
    return d.toLocaleDateString('hu-HU', { month: 'short', day: 'numeric' })
  }
  return `${format(dates[0]!)} – ${format(dates[6]!)}`
})

async function fetchSchedules() {
  isLoading.value = true
  try {
    const from = weekDates.value[0]!
    const to   = weekDates.value[6]!

    await $fetch(`${baseUrl}/sanctum/csrf-cookie`, {
      credentials: 'include'
    })

    const res = await $fetch<{ data: ScheduleEntry[] }>(
      `${baseUrl}/api/schedule?from=${from}&to=${to}`,
      {
        credentials: 'include',
        headers: {
          Accept: 'application/json',
        },
      }
    )

    days.value = weekDates.value.map((date, i) => {
      const existing = res.data.find((s) => s.date === date)

      return {
        date,
        label: HU_DAYS[i]!,
        open: !!existing,
        start: existing?.start ?? '08:00',
        end: existing?.end ?? '16:00',
        id: existing?.id ?? null,
        saving: false,
        dirty: false,
      }
    })
  } finally {
    isLoading.value = false
  }
}

async function saveDay(d: DayState) {
  d.saving = true
  try {
    const headers = {
      'X-XSRF-TOKEN': getCsrfToken(),
      Accept: 'application/json',
    }
    
    await $fetch(`${baseUrl}/sanctum/csrf-cookie`, {
      credentials: 'include'
    })

    if (d.open) {
      if (d.id) {
        await $fetch(`${baseUrl}/api/schedule/${d.id}`, {
          method: 'PUT',
          credentials: 'include',
          headers,
          body: { start: d.start, end: d.end },
        })
      } else {
        const res = await $fetch<{ data: ScheduleEntry }>(`${baseUrl}/api/schedule`, {
          method: 'POST',
          credentials: 'include',
          headers,
          body: { date: d.date, start: d.start, end: d.end },
        })
        d.id = res.data.id
      }
    } else if (d.id) {
      await $fetch(`${baseUrl}/api/schedule/${d.id}`, {
        method: 'DELETE',
        credentials: 'include',
        headers,
      })
      d.id = null
    }

    d.dirty = false
  } finally {
    d.saving = false
  }
}

watch(weekOffset, fetchSchedules, { immediate: true })
</script>

<template>
  <div class="space-y-3 py-2">
    <div class="flex items-center justify-between px-1">
      <button
        type="button"
        class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors"
        @click="weekOffset--"
      >
        ←
      </button>
      <span class="text-sm font-medium text-gray-700">{{ weekLabel }}</span>
      <button
        type="button"
        class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors"
        @click="weekOffset++"
      >
        →
      </button>
    </div>

    <div v-if="isLoading" class="text-sm text-gray-400 text-center py-6">
      Betöltés...
    </div>

    <template v-else>
      <ScheduleDayCard
        v-for="d in days"
        :key="d.date"
        :day="d"
        @toggle="d.open = !d.open; d.dirty = true"
        @update-start="d.start = $event; d.dirty = true"
        @update-end="d.end = $event; d.dirty = true"
        @save="saveDay(d)"
      />
    </template>
  </div>
</template>