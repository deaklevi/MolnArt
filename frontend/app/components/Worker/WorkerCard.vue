<template>
    <div class="group relative md:w-60 lg:w-72 overflow-hidden rounded-xl shadow-lg cursor-pointer hover:scale-105 transform transition">
        <img class="w-full h-full object-cover" :src="apiBase+worker.profile_image" :alt="worker.user_name">
        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-60 transition-all duration-300"></div>

        <div class="absolute inset-0 flex flex-col items-center justify-end p-6 text-center text-white opacity-0 group-hover:opacity-100 transform translate-y-4 group-hover:translate-y-0 transition-all duration-300">

            <h3 class="text-2xl font-bold mb-2">{{ worker.user_name }}</h3>
            <p class="text-base mb-4">{{ worker.description }}</p>
            <button class="bg-[#D0BEAC] text-black font-semibold p-2 rounded-lg w-[90%] mx-auto transition-colors hover:bg-opacity-90"> Foglalás most </button>
        </div>
    </div>

</template>

<script setup>

    const config = useRuntimeConfig();
    const apiBase = config.public.apiBase;
    console.log(apiBase)
    const props = defineProps({
        worker:{
            type:Object,
            required: true,
            default: () =>({
                user_name: 'Jane Doe',
                description: 'Több mint egy évtizedes tapasztalattal a hátam mögött hiszek abban, hogy a haj nem csupán divat, hanem önkifejezés. Szenvedélyem, hogy minden vendégemnek olyan frizurát alkossak, ami kiemeli egyéniségét és magabiztosságot ad neki',
                imageURL:'/photos/MolnarEva.jpg'
            })
        }
    })
</script>