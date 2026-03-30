<template>
    <nav class="absolute top-0 left-0 w-full z-30">
        <div class="max-w-7xl mx-auto flex justify-between items-center p-6 md:p-8">
            
            <NuxtLink to="/" class="flex items-center z-50 relative group">
                <span class="text-3xl md:text-4xl font-serif text-white tracking-wider transition-colors duration-300">
                    Moln<span class="text-[#b5a494]">Art</span>
                </span>
            </NuxtLink>

            <button @click="toggleMenu" class="md:hidden relative w-10 h-10 flex items-center justify-center z-50 focus:outline-none">
                <span class="absolute w-8 h-[2px] transition-all duration-300 ease-in-out" :class="[isMenuOpen ? 'rotate-45 translate-y-0 bg-[#b5a494]' : '-translate-y-2.5 bg-white']"></span>
                <span class="absolute w-8 h-[2px] transition-all duration-300 ease-in-out" :class="[isMenuOpen ? 'opacity-0 bg-[#b5a494]' : 'opacity-100 bg-white']"></span>
                <span class="absolute w-8 h-[2px] transition-all duration-300 ease-in-out" :class="[isMenuOpen ? '-rotate-45 translate-y-0 bg-[#b5a494]' : 'translate-y-2.5 bg-white']"></span>
            </button>

            <ul class="hidden md:flex space-x-10 text-white text-sm uppercase tracking-[0.2em] font-light">
                <li><a class="relative group py-2 transition duration-300 hover:text-[#b5a494]" href="#home">Főoldal <span class="absolute bottom-0 left-0 w-0 h-[1px] bg-[#b5a494] transition-all duration-500 ease-out group-hover:w-full"></span></a></li>
                <li><a class="relative group py-2 transition duration-300 hover:text-[#b5a494]" href="#gallery">Galéria <span class="absolute bottom-0 left-0 w-0 h-[1px] bg-[#b5a494] transition-all duration-500 ease-out group-hover:w-full"></span></a></li>
                <li><a class="relative group py-2 transition duration-300 hover:text-[#b5a494]" href="#services">Szolgáltatások <span class="absolute bottom-0 left-0 w-0 h-[1px] bg-[#b5a494] transition-all duration-500 ease-out group-hover:w-full"></span></a></li>
                <li><a class="relative group py-2 transition duration-300 hover:text-[#b5a494]" href="#staff">Csapat <span class="absolute bottom-0 left-0 w-0 h-[1px] bg-[#b5a494] transition-all duration-500 ease-out group-hover:w-full"></span></a></li>
                <li><a class="relative group py-2 transition duration-300 hover:text-[#b5a494]" href="#about">Rólunk <span class="absolute bottom-0 left-0 w-0 h-[1px] bg-[#b5a494] transition-all duration-500 ease-out group-hover:w-full"></span></a></li>
            </ul>
        </div>

        <div class="md:hidden fixed inset-0 w-full h-screen bg-black/95 backdrop-blur-md text-white flex flex-col items-center transition-all duration-500 ease-in-out" :class="isMenuOpen ? 'opacity-100 z-40' : 'opacity-0 pointer-events-none z-0'">
            <ul class="flex flex-col items-center space-y-8 text-lg uppercase tracking-[0.3em] font-light pt-[25vh]">
                <li><a href="#home" @click="toggleMenu" class="relative group hover:text-[#b5a494] transition-colors duration-300">Főoldal <span class="absolute -bottom-2 left-0 w-0 h-[1px] bg-[#b5a494] transition-all duration-500 ease-out group-hover:w-full"></span></a></li>
                <li><a href="#gallery" @click="toggleMenu" class="relative group hover:text-[#b5a494] transition-colors duration-300">Galéria <span class="absolute -bottom-2 left-0 w-0 h-[1px] bg-[#b5a494] transition-all duration-500 ease-out group-hover:w-full"></span></a></li>
                <li><a href="#services" @click="toggleMenu" class="relative group hover:text-[#b5a494] transition-colors duration-300">Szolgáltatások <span class="absolute -bottom-2 left-0 w-0 h-[1px] bg-[#b5a494] transition-all duration-500 ease-out group-hover:w-full"></span></a></li>
                <li><a href="#staff" @click="toggleMenu" class="relative group hover:text-[#b5a494] transition-colors duration-300">Csapat <span class="absolute -bottom-2 left-0 w-0 h-[1px] bg-[#b5a494] transition-all duration-500 ease-out group-hover:w-full"></span></a></li>
                <li><a href="#about" @click="toggleMenu" class="relative group hover:text-[#b5a494] transition-colors duration-300">Rólunk <span class="absolute -bottom-2 left-0 w-0 h-[1px] bg-[#b5a494] transition-all duration-500 ease-out group-hover:w-full"></span></a></li>
            </ul>
        </div>
    </nav>
</template>

<script setup>
import { ref, watch } from 'vue'

const isMenuOpen = ref(false)

// Menü kapcsoló függvény, ami kezeli a görgetést is
const toggleMenu = () => {
    isMenuOpen.value = !isMenuOpen.value
    if (isMenuOpen.value) {
        document.body.style.overflow = 'hidden' // Megállítja a háttér görgetését
    } else {
        document.body.style.overflow = '' // Visszaállítja a görgetést
    }
}

// Biztonsági mentés: ha az oldal elnavigálna, állítsa vissza a görgetést
watch(isMenuOpen, (val) => {
    if (!val) document.body.style.overflow = ''
})
</script>