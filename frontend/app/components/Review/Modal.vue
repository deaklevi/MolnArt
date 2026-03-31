<template>
  <div class="fixed inset-0 bg-gray-900 bg-opacity-60 z-40 flex items-center justify-center" @click.self="$emit('close')">

    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg m-4 p-6 md:p-8 transform transition-all" role="dialog" aria-modal="true">
      <h2 class="text-2xl font-bold text-gray-800 mb-6">Értékelés írása</h2>

      <form @submit.prevent="submitFullReview">
        <div class="space-y-6">

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Kire vonatkozik az értékelés?</label>
            <div class="flex flex-wrap justify-center gap-4 p-4 bg-gray-50 rounded-lg">
              <ReviewWorkerCard
                v-for="worker in workers"
                :key="worker.id"
                :worker="worker"
                :is-elected="reviewData.userId === worker.id"
                @select="reviewData.userId = worker.id"
              />
            </div>
          </div>

          <div>
            <label for="reviewer-name" class="block text-sm font-medium text-gray-700">Neved</label>
            <input
              v-model="reviewData.name"
              type="text"
              id="reviewer-name"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm p-2"
              placeholder="Pl. Gipsz Jakab"
              required
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Értékelés (1-5)</label>
            <div class="flex items-center mt-2">
              <svg v-for="star in 5" :key="star" @click="reviewData.rating = star" class="w-8 h-8 cursor-pointer" :class="star <= reviewData.rating? 'text-yellow-400' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.367 2.448a1 1 0 00-.364 1.118l1.287 3.957c.3.921-.755 1.688-1.54 1.118l-3.367-2.448a1 1 0 00-1.175 0l-3.367 2.448c-.784.57-1.838-.197-1.539-1.118l1.287-3.957a1 1 0 00-.364-1.118L2.05 9.384c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69L9.049 2.927z" />
              </svg>
            </div>
          </div>

          <div>
            <label for="review-comment" class="block text-sm font-medium text-gray-700">Véleményed</label>
            <textarea
              v-model="reviewData.comment"
              id="review-comment"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm h-24 p-2"
              placeholder="Oszd meg az élményed..."
              required
            ></textarea>
          </div>
        </div>

        <div class="mt-8 flex justify-end gap-4">
          <button type="button" @click="$emit('close')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none">
            Mégse
          </button>
          <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-purple-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
            Értékelés elküldése
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  workers: {
    type: Array,
    required: true,
  },
  initialWorkerId: {
    type: Number,
    default: null,
  }
});

const emit = defineEmits(['close', 'submit-review']);

const reviewData = ref({
  name: '',
  comment: '',
  rating: 0,
  user_id: props.initialWorkerId,
});

function submitFullReview() {
  if (!reviewData.value.name.trim() ||!reviewData.value.comment.trim() || reviewData.value.rating === 0 ||!reviewData.value.userId) {
    alert('Kérjük, tölts ki minden mezőt és válassz egy munkatársat!');
    return;
  }
  emit('submit-review', reviewData.value);
}
</script>