<template>
  <form @submit.prevent="submitReview" class="space-y-4">
    <div>
      <label for="review-comment" class="block text-sm font-medium text-gray-700">Értékelés hozzáadása</label>
      <div class="mt-1">
        <textarea
          v-model="comment"
          id="review-comment"
          name="comment"
          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm h-24 md:h-48"
          placeholder="Oszd meg az élményed..."
          required
        ></textarea>
      </div>
    </div>
    <div>
      <button
        type="submit"
        class="inline-flex w-full justify-center rounded-md border border-transparent bg-purple-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2"
      >
        Értékelés hozzáadása
      </button>
    </div>
  </form>
</template>

<script setup>
import { ref } from 'vue';

const emit = defineEmits(['submit-review']);

const comment = ref('');

function submitReview() {
  if (comment.value.trim() === '') return;
  emit('submit-review', comment.value);
  comment.value = '';
}
</script>