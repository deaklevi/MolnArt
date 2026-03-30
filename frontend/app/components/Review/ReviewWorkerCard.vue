<template>
    <div 
    @click="$emit('select',worker)"
    class="cursor-pointer text-center p-4 rounded-2xl transition-all duration-300"
    :class="isElected ? 'bg-gray-200 shadow-md scale-105' : 'hover:bg-gray-50'">
        
        <img class="w-20 h-20 mx-auto rounded-full object-cover mb-3" :src="apiBase+worker.profile_image" :alt="worker.profile_image">
        
        <h3 class="font-semibold text-lg text-gray-800">{{ worker.user_name }}</h3>
    </div>
</template>

<script setup>
const config = useRuntimeConfig();
const apiBase = config.public.apiBase;

const props = defineProps({
    worker:{
        type:Object,
        required:true,
        default: () =>({
            user_name: 'MolnárÉva',
            description: 'Több mint egy évtizedes tapasztalattal a hátam mögött hiszek abban, hogy a haj nem csupándivat, hanem önkifejezés. Szenvedélyem, hogy minden vendégemnek olyan frizurát alkossak, ami kiemeliegyéniségét és magabiztosságot ad neki',
            profile_image:'/photos/Molnar_Eva_02.jpg'
        }),   
    },
    isElected: {
        type: Boolean,
        default: false
    }
})

defineEmits(['select']);
</script>