<script setup>
const config = useRuntimeConfig();

// Adatok lekérése (useAsyncData-val stabilabb SSR alatt)
const { data: publicUsers } = await useAsyncData('users', () => $fetch(`${config.public.apiBase}/api/user_public_data`));
const { data: servicesData } = await useAsyncData('services', () => $fetch(`${config.public.apiBase}/api/services`));
const {data: reviews} = await useFetch(`${config.public.apiBase}/api/reviews`);
  
</script>

<template>
  <div> 
    <BaseHeader />
    <BaseGallery />

    <div class="container mx-auto px-4 py-8">
      <h2 class="mb-12 text-center text-4xl font-serif tracking-wide text-gray-900 uppercase">
        Alap szolgáltatásaink
      </h2>

      <div v-if="servicesData?.data" class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto">
        <BaseService 
          v-for="service in servicesData.data.slice(0, 6)" 
          :key="service.id" 
          :service="service" 
        />
      </div>

      <div v-else class="text-center py-10 text-gray-400 animate-pulse">
        Szolgáltatások betöltése...
      </div>
    </div>
    
    <div id="staff" class="py-20 bg-stone-50">
        <h2 class="mb-12 text-center text-4xl font-serif tracking-wide uppercase">Csapatunk</h2>
        <WorkerSection :workers="publicUsers?.data || []" />
    </div>
    
    <section class="py-16">
    <h2 class="text-center text-2xl font-bold mb-20 mt-10">
      Nézd meg értékeléseinket
    </h2>
    <ReviewsSlot :reviews="reviews.data" />
  </div>
</template>