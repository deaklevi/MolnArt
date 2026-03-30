<template>
    <div class="grid h-[500px] grid-cols-3 gap-4">
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

.animate-scroll-down {
  animation: scrollDown 30s linear infinite;
}

.animate-scroll-up {
  animation: scrollUp 30s linear infinite;
}
</style>