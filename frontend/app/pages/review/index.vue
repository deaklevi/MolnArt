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
    </div>

    <!-- <div class="p-6 rounded-lg">
        <h2 class="text-xl font-bold mb-4">Second Column</h2>
        <p>Your other content goes here.</p>
    </div> -->

</div>

</LayoutsBaseLayout>
</template>

<script setup>
import { ref } from 'vue';

const config = useRuntimeConfig();

const { data: publicUsers } = await useAsyncData('users', () => $fetch(`${config.public.apiBase}/api/user_public_data`));

const selectedWorker = ref(null);

function handleWorkerSelection(worker) {
    selectedWorker.value = worker;
}
console.log(publicUsers.value.data[1])

if (publicUsers.value?.data && publicUsers.value.data.length > 0) {
    selectedWorker.value = publicUsers.value.data[0];
}
</script>