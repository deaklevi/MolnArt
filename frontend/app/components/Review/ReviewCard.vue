<template>
  <div class="bg-white rounded-xl shadow-md p-4 flex gap-3 items-start">
    <div class="w-8 h-8 md:h-10 md:w-10 lg:h-12 lg:w-12 rounded-full bg-purple-500 flex items-center justify-center text-white font-bold overflow-hidden">
      <span v-if="!review.profile_image">
        {{ review.name.charAt(0) }}
      </span>
      <img
        v-else
        :src="apiBase + review.profile_image"
        :alt="review.name"
        class="w-full h-full object-cover"
      />
    </div>

    <div class="flex-1">
      <div class="flex justify-between items-center">
        <h4 class="font-semibold text-md md:text-lg lg:text-xl">
          {{ review.name }}
        </h4>

        <div class="flex text-yellow-400 text-md md:text-lg lg:text-xl">
          <span v-for="i in 5" :key="i">
            <span v-if="i <= review.rating">★</span>
            <span v-else class="text-gray-300">★</span>
          </span>
        </div>
      </div>

      <p class="text-md md:text-lg text-gray-600 mt-2 line-clamp-3">
        {{ review.comment }}
      </p>
    </div>
  </div>
</template>

<script setup>
const config = useRuntimeConfig()
const apiBase = config.public.apiBase

const props = defineProps({
  review: {
    type: Object,
    required: true
  }
})
</script>