<template>
    <BaseHeader/>
    <BaseGallery/>
      
    
    <div id="staff" class="py-16 bg-gray-50">
        <h2 class="mb-12 text-center text-3xl font-bold tracking-wide">Csapatunk</h2>
        <WorkerSection :workers="publicUsers?.data ||[]" />
    </div>

</template>
<script>

<script setup>
import { ref } from 'vue';
const isMenuOpen = ref(false)

const config = useRuntimeConfig();

const { data: publicUsers } = await useFetch(`${config.public.apiBase}/api/user_public_data`);

console.log("asdasda")
if (publicUsers.value) {
    console.log(publicUsers.value)
}




</script>
