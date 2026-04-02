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
    <LayoutsBaseLayout>
    <BaseGallery />

    <div id="services" class="container mx-auto px-4 py-8">
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

      <NuxtLink to="/booking" class="flex justify-center my-10 mx-10">
        <button 
          type="button" 
          class="w-full md:w-1/3 lg:w-1/4 px-6 py-3 text-base font-semibold text-white bg-[#36082A] rounded-lg shadow-md hover:bg-[#470C37] transform active:scale-95 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-[#36082A]/50"
        >
          Foglaljon most
        </button>
      </NuxtLink>
    </div>
    
    <div id="staff" class="py-20">
        <h2 class="mb-12 text-center text-4xl font-serif tracking-wide uppercase">Csapatunk</h2>
        <WorkerSection :workers="publicUsers?.data || []" />
    </div>
    
    <section class="py-16">
    <h2 class="text-center text-2xl font-bold mb-20 mt-10">
      Nézd meg értékeléseinket
    </h2>
    <ReviewReviewsSlot :reviews="reviews.data" />
    </section>
    
    <NuxtLink to="/review" class="flex justify-center my-10 mx-10">
      <button 
        type="button" 
        class="w-full md:w-1/3 lg:w-1/4 px-6 py-3 text-base font-semibold text-white bg-[#36082A] rounded-lg shadow-md hover:bg-[#470C37] transform active:scale-95 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-[#36082A]/50"
      >
        Nézze meg értékeléseinket
      </button>
  </NuxtLink>


    


    </LayoutsBaseLayout>
    <div id="aboutus">
      <BaseAboutUs/>
    </div>
    <BaseFooter/>
  </div>
</template>