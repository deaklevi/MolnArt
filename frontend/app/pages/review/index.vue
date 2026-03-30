<template>
<LayoutsBaseLayout>

<div class="grid grid-cols-2 gap-8 p-8">

    <div class="p-6 rounded-lg">
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

        <div class="mt-10 h-full " v-if="selectedWorker && workerReviews.length > 0">
            <ReviewExpanded :review="currentReview" />

            <div class="flex justify-between items-center mt-4">
                <button @click="prevReview" class="px-4 py-2 bg-purple-600 text-white rounded-lg shadow-md hover:bg-purple-700 transition">
                    Előző
                </button>
                <span class="text-gray-600 font-semibold">
                    {{ currentReviewIndex + 1 }} / {{ workerReviews.length }}
                </span>
                <button @click="nextReview" class="px-4 py-2 bg-purple-600 text-white rounded-lg shadow-md hover:bg-purple-700 transition">
                    Következő
                </button>
            </div>
        </div>
    </div>

    <!-- <div class="p-6 rounded-lg">
        <h2 class="text-xl font-bold mb-4">Second Column</h2>
        <p>Your other content goes here.</p>
    </div> -->

</div>

</LayoutsBaseLayout>
</template>

<script setup>
import { ref, computed } from 'vue';

const config = useRuntimeConfig();

const { data: publicUsers } = await useAsyncData('users', () => $fetch(`${config.public.apiBase}/api/user_public_data`));


const selectedWorker = ref(null);
const currentReviewIndex = ref(0);
const workerReviews = computed(() => selectedWorker.value?.reviews || []);

const currentReview = computed(() => {
    if (workerReviews.value.length >0){
        return workerReviews.value[currentReviewIndex.value];
    }
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


</script>