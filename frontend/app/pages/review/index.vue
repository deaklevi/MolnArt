<template>
<LayoutsBaseLayout>
<div class="flex flex-col md:grid md:grid-cols-2 md:grid-rows-2 gap-8 p-8">
    <div class="order-1 md:row-span-1 md:col-span-1">
        <div class="flex flex-wrap justify-center gap-6">
            <ReviewWorkerCard
                v-for="worker in publicUsers?.data"
                :key="worker.id"
                :worker="worker"
                :is-elected="selectedWorker && selectedWorker.id === worker.id"
                @select="handleWorkerSelection"
            />
        </div>
        <div v-if="selectedWorker" class="mt-8 p-4 bg-white rounded-lg shadow-md text-center">
            <p class="text-gray-700 mb-4">{{ selectedWorker.description }}</p>
        </div>
    </div>

    <div class="order-2 md:row-span-2 md:col-span-1 space-y-8">
        <div v-if="selectedWorker && workerReviews.length > 0" class="md:mb-[10rem]">
            <ReviewChart :avg-rating="averageRating"/>
        </div>
        <div v-else-if="selectedWorker" class="text-center text-gray-500 mt-10">
            <p>Még nincs értékelés</p>
        </div>

        <div v-if="selectedWorker">
            <ReviewForm @submit-review="handleReviewSubmit" />
        </div>
    </div>

    <div class="order-3 md:row-span-1 md:col-span-1">
        <div class="mt-10" v-if="selectedWorker && workerReviews.length > 0">
            <div>
              <ReviewExpanded :review="currentReview" />
            </div>
            <div class="flex items-center justify-center mt-6">
                <div class="flex items-center gap-4 bg-gray-100 rounded-full p-1 shadow-inner">
                    <button
                        @click="prevReview"
                        :disabled="workerReviews.length <= 1"
                        class="p-2 rounded-full text-gray-600 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-purple-400 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        aria-label="Előző értékelés"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                        </svg>
                    </button>
                    <div class="h-6 w-px bg-gray-300"></div>
                    <span class="text-gray-700 font-semibold tabular-nums w-12 text-center">
                        {{ currentReviewIndex + 1 }} / {{ workerReviews.length }}
                    </span>
                    <div class="h-6 w-px bg-gray-300"></div>
                    <button
                        @click="nextReview"
                        :disabled="workerReviews.length <= 1"
                        class="p-2 rounded-full text-gray-600 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-purple-400 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        aria-label="Következő értékelés"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
</LayoutsBaseLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
function handleReviewSubmit(comment) {
  console.log('Review Submitted:', comment);
  alert(`Thank you for your review: "${comment}"`);
}

const config = useRuntimeConfig();
const { data: publicUsers } = await useAsyncData('users', () => $fetch(`${config.public.apiBase}/api/user_public_data`));
const selectedWorker = ref(null);
const currentReviewIndex = ref(0);
const workerReviews = computed(() => selectedWorker.value?.reviews || []);
const currentReview = computed(() => {
    if (workerReviews.value.length > 0) return workerReviews.value[currentReviewIndex.value];
    return null;
});
function handleWorkerSelection(worker) {
    selectedWorker.value = worker;
    currentReviewIndex.value = 0;
}
if (publicUsers.value?.data && publicUsers.value.data.length > 0) {
    selectedWorker.value = publicUsers.value.data[0];
}
function nextReview(){
    if (workerReviews.value.length > 0) {
        currentReviewIndex.value = (currentReviewIndex.value + 1) % workerReviews.value.length;
    }
}
function prevReview() {
    if (workerReviews.value.length > 0) {
        if (currentReviewIndex.value === 0) {
            currentReviewIndex.value = workerReviews.value.length - 1;
        } else {
            currentReviewIndex.value--;
        }
    }
}
const averageRating = computed(() => {
  if (!workerReviews.value || workerReviews.value.length === 0) return 0;
  const total = workerReviews.value.reduce((sum, review) => sum + review.rating, 0);
  return total / workerReviews.value.length;
});
</script>