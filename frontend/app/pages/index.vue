<script setup>
const isMenuOpen = ref(false)
const config = useRuntimeConfig();

// Adatok lekérése
const { data: publicUsers } = await useFetch(`${config.public.apiBase}/api/user_public_data`);
const { data: servicesData } = await useFetch(`${config.public.apiBase}/api/services`);

// Debugoláshoz jó a konzol, de nézd meg a böngésző konzolját is!
console.log("Szolgáltatások:", servicesData.value)
</script>

<template>
  <div> <BaseHeader />
    <BaseGallery />

    <div class="container mx-auto px-4 py-8">
    <h2 class="mb-12 text-center text-3xl font-bold tracking-wide text-gray-900">
      Alap szolgáltatásaink
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-5xl mx-auto">
      <BaseService 
        v-for="service in servicesData?.data?.slice(0, 6)" 
        :key="service.id" 
        :service="service" 
      />
    </div>

    <div v-if="!servicesData?.data" class="text-center py-10 text-gray-400">
      Szolgáltatások betöltése...
    </div>
  </div>
    
    <div id="staff" class="py-16 bg-gray-50">
        <h2 class="mb-12 text-center text-3xl font-bold tracking-wide">Csapatunk</h2>
        <WorkerSection :workers="publicUsers?.data || []" />
    </div>
  </div>
</template>