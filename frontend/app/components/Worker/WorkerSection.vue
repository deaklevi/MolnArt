<template>
    <div class="w-full max-w-6xl mx-auto">
        <div class="md:hidden">
            <div class="flex overflow-x-auto snap-x snap-mandatory space-x-6 pb-6">
                <div v-for="(worker, index) in workers" :key="worker.id" class="snap-start">
                    <WorkerMobile
                        :worker="worker"
                        @workerSelected="selectWorker(index)"
                    />
                </div>
            </div>

            <div v-if="selectedWorker" class="mt-8 p-4 bg-white rounded-lg shadow-md text-center">
                <h3 class="text-2xl font-bold mb-2">{{ selectedWorker.name }}</h3>
                <p class="text-gray-700 mb-4">{{ selectedWorker.description }}</p>
                <button class="bg-[#D0BEAC] text-black font-semibold p-2 rounded-lg w-full max-w-xs mx-auto"> Foglalás most </button>
            </div>
        </div>

        <div class="hidden md:flex flex-col items-center">
            <div class="flex justify-center gap-8">
                <WorkerCard
                    v-for="worker in workers"
                    :key="worker.id"
                    :worker="worker"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
    workers:{
        type:Array,
        required:true
    }
});

const selectedWorker = ref(props.workers? props.workers[0] : null);

function selectWorker(index) {
  selectedWorker.value = props.workers[index];
}
</script>

<style scoped>
.overflow-x-auto::-webkit-scrollbar { display: none; }
.overflow-x-auto { -ms-overflow-style: none; scrollbar-width: none; }
</style>