<template>
    <div class="relative w-full min-h-screen">
        <nav class="absolute top-0 left-0 w-full z-20">
            <div class="max-w-7xl mx-auto flex justify-between items-center p-5">
                <RouterLink to="/" class="flex items-center">
                    <span class="text-4xl font-serif text-white tracking-wider">Moln<span class="text-[rgb(181_164_148)]">Art</span></span>
                </RouterLink>
                <button @click="isMenuOpen = !isMenuOpen" class="md:hidden relative w-12 h-12 flex items-center justify-center z-30">
                    <span class="absolute w-8 h-0.5 transition-all duration-300" :class="[isMenuOpen ? 'rotate-45 translate-y-0 bg-black' : '-translate-y-3 bg-white']"></span>
                    <span class="absolute w-8 h-0.5 transition-all duration-300" :class="[isMenuOpen ? 'opacity-0 bg-black' : 'opacity-100 bg-white']"></span>
                    <span class="absolute w-8 h-0.5 transition-all duration-300" :class="[isMenuOpen ? '-rotate-45 translate-y-0 bg-black' : 'translate-y-3 bg-white']"></span>
                </button>
                <ul class="hidden md:flex space-x-10 text-white text-sm uppercase tracking-[0.2em] font-light">
                    <li><a class="relative group py-2 transition duration-500 hover:text-[#c5b4a2]" href="#home">Főoldal <span class="absolute bottom-0 left-0 w-0 h-px bg-[#c5b4a2] transition-all duration-700 ease-in-out group-hover:w-full"></span></a></li>
                    <li><a class="relative group py-2 transition duration-500 hover:text-[#c5b4a2]" href="#gallery">Galéria <span class="absolute bottom-0 left-0 w-0 h-px bg-[#c5b4a2] transition-all duration-700 ease-in-out group-hover:w-full"></span></a></li>
                    <li><a class="relative group py-2 transition duration-500 hover:text-[#c5b4a2]" href="#services">Szolgáltatások <span class="absolute bottom-0 left-0 w-0 h-px bg-[#c5b4a2] transition-all duration-700 ease-in-out group-hover:w-full"></span></a></li>
                    <li><a class="relative group py-2 transition duration-500 hover:text-[#c5b4a2]" href="#staff">Csapat <span class="absolute bottom-0 left-0 w-0 h-px bg-[#c5b4a2] transition-all duration-700 ease-in-out group-hover:w-full"></span></a></li>
                    <li><a class="relative group py-2 transition duration-500 hover:text-[#c5b4a2]" href="#about">Rólunk <span class="absolute bottom-0 left-0 w-0 h-px bg-[#c5b4a2] transition-all duration-700 ease-in-out group-hover:w-full"></span></a></li>
                </ul>
            </div>
            <div class="md:hidden fixed top-0 left-0 w-full h-screen bg-white text-black flex flex-col items-center justify-center transition-all duration-300":class="isMenuOpen ? 'opacity-100 z-20' : 'opacity-0 pointer-events-none z-0'">
                <ul class="flex flex-col items-center space-y-4 py-6 uppercase tracking-widest">
                    <li><a href="#home"@click="isMenuOpen = false"class="hover:text-[#c5b4a2] transition">Főoldal <span class="absolute bottom-0 left-0 w-0 h-px bg-[#c5b4a2] transition-all duration-700 ease-in-out group-hover:w-full"></span></a></li>
                    <li><a href="#gallery"@click="isMenuOpen = false"class="hover:text-[#c5b4a2] transition">Galéria <span class="absolute bottom-0 left-0 w-0 h-px bg-[#c5b4a2] transition-all duration-700 ease-in-out group-hover:w-full"></span></a></li>
                    <li><a href="#services"@click="isMenuOpen = false"class="hover:text-[#c5b4a2] transition">Szolgáltatások <span class="absolute bottom-0 left-0 w-0 h-px bg-[#c5b4a2] transition-all duration-700 ease-in-out group-hover:w-full"></span></a></li>
                    <li><a href="#staff"@click="isMenuOpen = false"class="hover:text-[#c5b4a2] transition">Csapat <span class="absolute bottom-0 left-0 w-0 h-px bg-[#c5b4a2] transition-all duration-700 ease-in-out group-hover:w-full"></span></a></li>
                    <li><a href="#about"@click="isMenuOpen = false"class="hover:text-[#c5b4a2] transition">Rólunk <span class="absolute bottom-0 left-0 w-0 h-px bg-[#c5b4a2] transition-all duration-700 ease-in-out group-hover:w-full"></span></a></li>
                </ul>
            </div>
        </nav>

        <!-- a href="#"@click="isMenuOpen = false"class="hover:text-[#c5b4a2] transition" -->
        <div id="home" class="w-full min-h-screen bg-cover bg-center bg-[url('/bg.jpg')]">
            <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                <div class="text-center text-white px-4">
                    <h1 class="text-5xl font-bold font-serif mb-4">Fedezd fel az <span class="text-[rgb(181_164_148)]">egyedi</span> stílusod!</h1>
                
                    <p class="p-3">Nálunk a hajad a kreativitásunk vászna. Trendi, precíz, rád szabott vágások,<br> amik tényleg tükrözik a személyiséged. Foglalj online, és hagyd, hogy a <br> profik megalkossák a tökéletes frizurát.</p>
                    
                    <a href="#services">
                        <button class="border text-white hover:bg-[#fcfcfc42] py-3 px-10 text-sm font-semibold transition duration-300 ease-out transform hover:scale-110">
                            Szolgáltatásaink
                        </button>
                    </a>
                </div>
            </div>
            <div class="absolute bottom-10 left-0 w-full">
                <div class="flex flex-wrap justify-center items-center gap-6 md:gap-12 text-white/80 text-[10px] md:text-xs uppercase tracking-[0.2em]">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" viewBox="0 0 640 640"><path fill="rgb(255, 255, 255)" d="M128 252.6C128 148.4 214 64 320 64C426 64 512 148.4 512 252.6C512 371.9 391.8 514.9 341.6 569.4C329.8 582.2 310.1 582.2 298.3 569.4C248.1 514.9 127.9 371.9 127.9 252.6zM320 320C355.3 320 384 291.3 384 256C384 220.7 355.3 192 320 192C284.7 192 256 220.7 256 256C256 291.3 284.7 320 320 320z"/></svg>
                        <span>Budapest, Magyarország</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" viewBox="0 0 640 640"><path fill="rgb(255, 255, 255)" d="M224.2 89C216.3 70.1 195.7 60.1 176.1 65.4L170.6 66.9C106 84.5 50.8 147.1 66.9 223.3C104 398.3 241.7 536 416.7 573.1C493 589.3 555.5 534 573.1 469.4L574.6 463.9C580 444.2 569.9 423.6 551.1 415.8L453.8 375.3C437.3 368.4 418.2 373.2 406.8 387.1L368.2 434.3C297.9 399.4 241.3 341 208.8 269.3L253 233.3C266.9 222 271.6 202.9 264.8 186.3L224.2 89z"/></svg>
                        <span>+36 20 935 3044</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" viewBox="0 0 640 640"><path fill="rgb(255, 255, 255)" d="M576 320C576 178.6 461.4 64 320 64C178.6 64 64 178.6 64 320C64 440 146.7 540.8 258.2 568.5L258.2 398.2L205.4 398.2L205.4 320L258.2 320L258.2 286.3C258.2 199.2 297.6 158.8 383.2 158.8C399.4 158.8 427.4 162 438.9 165.2L438.9 236C432.9 235.4 422.4 235 409.3 235C367.3 235 351.1 250.9 351.1 292.2L351.1 320L434.7 320L420.3 398.2L351 398.2L351 574.1C477.8 558.8 576 450.9 576 320z"/></svg>
                        <span>MolnArt</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" viewBox="0 0 640 640"><path fill="rgb(255, 255, 255)" d="M320.3 205C256.8 204.8 205.2 256.2 205 319.7C204.8 383.2 256.2 434.8 319.7 435C383.2 435.2 434.8 383.8 435 320.3C435.2 256.8 383.8 205.2 320.3 205zM319.7 245.4C360.9 245.2 394.4 278.5 394.6 319.7C394.8 360.9 361.5 394.4 320.3 394.6C279.1 394.8 245.6 361.5 245.4 320.3C245.2 279.1 278.5 245.6 319.7 245.4zM413.1 200.3C413.1 185.5 425.1 173.5 439.9 173.5C454.7 173.5 466.7 185.5 466.7 200.3C466.7 215.1 454.7 227.1 439.9 227.1C425.1 227.1 413.1 215.1 413.1 200.3zM542.8 227.5C541.1 191.6 532.9 159.8 506.6 133.6C480.4 107.4 448.6 99.2 412.7 97.4C375.7 95.3 264.8 95.3 227.8 97.4C192 99.1 160.2 107.3 133.9 133.5C107.6 159.7 99.5 191.5 97.7 227.4C95.6 264.4 95.6 375.3 97.7 412.3C99.4 448.2 107.6 480 133.9 506.2C160.2 532.4 191.9 540.6 227.8 542.4C264.8 544.5 375.7 544.5 412.7 542.4C448.6 540.7 480.4 532.5 506.6 506.2C532.8 480 541 448.2 542.8 412.3C544.9 375.3 544.9 264.5 542.8 227.5zM495 452C487.2 471.6 472.1 486.7 452.4 494.6C422.9 506.3 352.9 503.6 320.3 503.6C287.7 503.6 217.6 506.2 188.2 494.6C168.6 486.8 153.5 471.7 145.6 452C133.9 422.5 136.6 352.5 136.6 319.9C136.6 287.3 134 217.2 145.6 187.8C153.4 168.2 168.5 153.1 188.2 145.2C217.7 133.5 287.7 136.2 320.3 136.2C352.9 136.2 423 133.6 452.4 145.2C472 153 487.1 168.1 495 187.8C506.7 217.3 504 287.3 504 319.9C504 352.5 506.7 422.6 495 452z"/></svg>
                        <span>MolnArt</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    
    <div id="staff" class="py-16 bg-gray-50">
        <h2 class="mb-12 text-center text-3xl font-bold tracking-wide">Csapatunk</h2>
        <WorkerSection :workers="publicUsers?.data ||[]" />
    </div>

</template>

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
