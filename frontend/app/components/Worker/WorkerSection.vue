<template>
  <div class="w-full max-w-6xl mx-auto py-10 overflow-hidden bg-white">
    <div class="md:hidden">
      <h2 class="text-center text-3xl font-serif mb-8 text-[#2D1636]">Csapatunk</h2>

      <div 
        ref="scrollContainer"
        @scroll="handleScroll"
        class="flex items-center overflow-x-auto snap-x snap-mandatory px-[calc(50%-70px)] gap-4 no-scrollbar touch-pan-x py-10"
      >
        <div 
          v-for="(worker, index) in workers" 
          :key="worker.id" 
          class="snap-center flex-shrink-0"
          @click="scrollToIndex(index)"
          style="width: 140px;" 
        >
          <WorkerMobile
            :worker="worker"
            :isActive="activeIndex === index"
          />
        </div>
      </div>

      <Transition name="fade" mode="out-in">
        <div :key="activeIndex" class="mt-4 px-6 text-center min-h-[160px]">
          <h3 class="text-3xl font-bold text-[#1A202C] mb-1 italic">
            {{ workers[activeIndex]?.user_name }}
          </h3>
          <p class="text-gray-500 italic mb-6 text-lg">
            "{{ workers[activeIndex]?.role || 'Munkatárs' }}"
          </p>
          
          <NuxtLink to="/booking" class="inline-block w-full max-w-[280px]">
            <button class="w-full py-4 bg-[#2D0A22] text-white font-bold rounded-lg shadow-md active:scale-95 transition-all">
              Foglalás megkezdése
            </button>
          </NuxtLink>
        </div>
      </Transition>

      <div class="flex justify-center gap-3 mt-4">
        <div 
          v-for="(_, index) in workers" :key="index"
          @click="scrollToIndex(index)"
          class="h-2 rounded-full transition-all duration-300 cursor-pointer"
          :class="activeIndex === index ? 'bg-[#2D0A22] w-6' : 'bg-gray-300 w-2'"
        ></div>
      </div>
    </div>

    <div class="hidden md:flex flex-wrap justify-center gap-8">
      <WorkerCard v-for="worker in workers" :key="worker.id" :worker="worker" />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  workers: { type: Array, required: true }
});

const scrollContainer = ref(null);
const activeIndex = ref(0);

// Egyszerűbb kiszámítás: elem szélessége (140px) + a gap (16px = gap-4)
const ITEM_WIDTH = 156; 

const handleScroll = () => {
  if (!scrollContainer.value) return;
  const scrollLeft = scrollContainer.value.scrollLeft;
  const newIndex = Math.round(scrollLeft / ITEM_WIDTH);
  
  if (newIndex !== activeIndex.value && newIndex >= 0 && newIndex < props.workers.length) {
    activeIndex.value = newIndex;
  }
};

const scrollToIndex = (index) => {
  if (!scrollContainer.value) return;
  activeIndex.value = index;
  scrollContainer.value.scrollTo({
    left: index * ITEM_WIDTH,
    behavior: 'smooth'
  });
};
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; touch-action: pan-x; }

.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease, transform 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; transform: translateY(10px); }
</style>