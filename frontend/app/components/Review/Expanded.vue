<template>
<div class="bg-white rounded-lg shadow-xl p-6 flex-gap-3 items-start w-full h-full">
    <div class="w-8 h-8 md:h-12 md:w-12 lg:h-14 lg:w-14 rounded-full bg-purple-500 flex items-center justify-center text-white font-bold overflow-hidden">
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
            <h4 class="font-semibold text-xl mt-2">
              {{ review.name }}
            </h4>
            <div class="flex text-yellow-400 text-3xl">
              <span v-for="i in 5" :key="i">
                <span v-if="i <= review.rating">★</span>
                <span v-else class="text-gray-300">★</span>
              </span>
            </div>
        </div>
        <p class="text-md md:text-lg text-gray-600 mt-5 line-clamp-3">
          {{ review.comment }}
        </p>
    </div>
</div>

</template>

<script setup>
defineProps({
    review:Object,
    required:true,
    default: () =>({
        id:1,
        name: 'Jane Doe',
        comment: 'Több mint egy évtizedes tapasztalattal a hátam mögött hiszek abban, hogy a haj nem csupán divat, hanem önkifejezés. Szenvedélyem, hogy minden vendégemnek olyan frizurát alkossak, ami kiemeli egyéniségét és magabiztosságot ad neki',
        user_id: 1,
        profile_image :null
    })
});
</script>