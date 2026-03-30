<template>
  <div class="hidden md:grid h-[500px] grid-cols-3 gap-4">
      <div class="relative overflow-hidden">
          <div class="pointer-events-none absolute top-0 left-0 w-full h-16 bg-gradient-to-b from-white to-transparent z-10"></div>
          <div class="pointer-events-none absolute bottom-0 left-0 w-full h-16 bg-gradient-to-t from-white to-transparent z-10"></div>
          <div class="animate-scroll-up">
            <ReviewCard
              v-for="(review, index) in loopedReviews"
              :key="`col1-${index}`"
              :review="review"
              class="mb-4"
            />
          </div>
        </div>

        <div class="relative overflow-hidden">
          <div class="pointer-events-none absolute top-0 left-0 w-full h-16 bg-gradient-to-b from-white to-transparent z-10"></div>
          <div class="pointer-events-none absolute bottom-0 left-0 w-full h-16 bg-gradient-to-t from-white to-transparent z-10"></div>
          <div class="animate-scroll-down">
            <ReviewCard
              v-for="(review, index) in loopedReviews"
              :key="`col3-${index}`"
              :review="review"
              class="mb-4"
            />
          </div>
        </div>

        <div class="relative overflow-hidden">
          <div class="pointer-events-none absolute top-0 left-0 w-full h-16 bg-gradient-to-b from-white to-transparent z-10"></div>
          <div class="pointer-events-none absolute bottom-0 left-0 w-full h-16 bg-gradient-to-t from-white to-transparent z-10"></div>
          <div class="animate-scroll-up">
            <ReviewCard
              v-for="(review, index) in loopedReviews"
              :key="`col2-${index}`"
              :review="review"
              class="mb-4"
            />
          </div>
          
        </div>
        
  </div>
    

<div class="md:hidden grid grid-rows-2 gap-4 h-[300px] overflow-hidden">
  <div class="relative overflow-hidden">
      
    <div class="pointer-events-none absolute left-0 top-0 h-full w-12 bg-gradient-to-r from-white to-transparent z-10"></div>
    <div class="pointer-events-none absolute right-0 top-0 h-full w-12 bg-gradient-to-l from-white to-transparent z-10"></div>
    
    <div class="flex animate-scroll-left gap-4">
      <ReviewCard
        v-for="(review, index) in loopedReviews"
        :key="`row1-${index}`"
        :review="review"
        class="min-w-[250px]"
      />
    </div>
  </div>

  <div class="relative overflow-hidden"> 
    <div class="pointer-events-none absolute left-0 top-0 h-full w-12 bg-gradient-to-r from-white to-transparent z-10"></div>
    <div class="pointer-events-none absolute right-0 top-0 h-full w-12 bg-gradient-to-l from-white to-transparent z-10"></div>

    <div class="flex animate-scroll-right gap-4">
      <ReviewCard
        v-for="(review, index) in loopedReviews"
        :key="`row1-${index}`"
        :review="review"
        class="min-w-[250px]"
      />
    </div>
    </div>
  </div>

</template>

<script setup>
import { computed } from 'vue'
import ReviewCard from '/components/review/ReviewCard.vue'

const props = defineProps({
  reviews: {
    type: Array,
    default: () => []
  }
})

const loopedReviews = computed(() => [...props.reviews, ...props.reviews])
</script>

<style scoped>
@keyframes scrollDown {
  0% { transform: translateY(-50%); }
  100% { transform: translateY(0%); }
}

@keyframes scrollUp {
  0% { transform: translateY(0%); }
  100% { transform: translateY(-50%); }
}

@keyframes scrollLeft{
  0% {transform: translateX(0%);}
  100% {transform: translateX(-50%);}
}

@keyframes scrollRight {
  0% { transform: translateX(-50%); }
  100% { transform: translateX(0%); }
}

.animate-scroll-down {
  animation: scrollDown 60s linear infinite;
}

.animate-scroll-up {
  animation: scrollUp 60s linear infinite;
}

.animate-scroll-left{
  animation: scrollLeft 60s linear infinite;
}

.animate-scroll-right{
  animation: scrollRight 60s linear infinite;
}

</style>