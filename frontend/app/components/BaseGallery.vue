<template>
  <section id="gallery" class="bg-white py-10 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4">
      
      <div v-if="images && images.length >= 3" class="lg:hidden">
        <div ref="scrollContainer" class="flex overflow-x-auto snap-x snap-mandatory scrollbar-hide gap-6 pb-10 items-center">
          <div class="shrink-0 w-[10vw]"></div> 
          <div v-for="(img, index) in images" :key="'mob-' + index" class="snap-center shrink-0">
            <img :src="img" class="w-[70vw] aspect-[3/4] object-cover rounded-2xl shadow-xl border border-gray-100 transition-transform duration-500" />
          </div>
          <div class="shrink-0 w-[10vw]"></div>
        </div>

        <div class="flex justify-center items-center gap-16">
          <button @click="scroll('left')" class="p-2 active:scale-90 transition text-black">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
          </button>
          <button @click="scroll('right')" class="p-2 active:scale-90 transition text-black">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
          </button>
        </div>
      </div>

      <div v-if="images && images.length >= 6" class="hidden lg:flex relative h-[650px] justify-center items-center">
        <div class="relative w-[1100px] h-full mx-auto">
          <img :src="images[0]" class="absolute top-20 left-10 w-64 h-80 object-cover transition " /> 
          <img :src="images[1]" class="absolute top-0 left-1/2 -translate-x-1/2 w-64 h-80 object-cover z-10 transition" />
          <img :src="images[2]" class="absolute top-20 right-10 w-64 h-80 object-cover transition " />
          <img :src="images[3]" class="absolute bottom-10 left-0 w-56 h-56 object-cover z-10 transition " />
          <img :src="images[7]" class="absolute bottom-10 left-1/4 w-[500px] h-64 object-cover object-[center_30%] transition" />
          <img :src="images[5]" class="absolute bottom-0 right-20 w-56 h-56 object-cover z-10 transition " />
        </div>
      </div>

    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue'

const { data: images } = await useFetch('/api/gallery')
const scrollContainer = ref(null)

const scroll = (direction) => {
  if (!scrollContainer.value) return
  const scrollAmount = scrollContainer.value.clientWidth * 0.8
  scrollContainer.value.scrollBy({ 
    left: direction === 'left' ? -scrollAmount : scrollAmount, 
    behavior: 'smooth' 
  })
}
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

@media (max-width: 1023px) {
  .snap-center img {
    transform: scale(0.95);
    transition: transform 0.3s ease;
  }
}
</style>