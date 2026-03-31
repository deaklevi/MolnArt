<template>
  <footer class="bg-[#1a1a1a] text-white py-12 px-6">
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-8 items-start">
      
      <div class="flex justify-center md:justify-start">
        <img src="#" alt="MolnArt Logo" class="h-20 object-contain opacity-80" />
      </div>

      <div class="flex flex-col space-y-4 border-l border-gray-700 pl-6 h-full">
        <h3 class="font-bold text-lg">Helyszín</h3>
        <p class="text-gray-400 text-sm"><a href="https://www.google.com/maps/place/Budapest,+Haj%C3%B3s+u.+33,+1065/@47.5051265,19.056,20.75z/data=!4m6!3m5!1s0x4741dc6cce765981:0x4267edaa08e0e973!8m2!3d47.5051743!4d19.0561449!16s%2Fg%2F11csn87d99?entry=ttu&g_ep=EgoyMDI2MDMyNC4wIKXMDSoASAFQAw%3D%3D">Budapest, Hajós utca. 31</a></p>
      </div>

      <div class="flex flex-col space-y-4 border-l border-gray-700 pl-6 h-full">
        <h3 class="font-bold text-lg">Elérhetőség</h3>
        <ul class="space-y-3 text-sm text-gray-400">
          <li class="flex items-center gap-2">
            <span class="i-heroicons-phone"></span> <a href="tel:+36209353044">+36 20 935 3044</a>
          </li>
          <li class="flex items-center gap-2">
            <span class="i-heroicons-envelope"></span> <a href="mailto:millers.molnar@gmail.com">millers.molnar@gmail.com</a>
          </li>
          <li class="flex items-center gap-2">
            <span class="i-simple-icons-instagram"></span> <a href="https://www.facebook.com/molnarevamua.971">#moln.art</a>
          </li>
          <li class="flex items-center gap-2">
            <span class="i-simple-icons-facebook"></span> <a href="https://www.instagram.com/molnarevamua/">MolnArt</a>
          </li>
        </ul>
      </div>

      <div class="flex flex-col space-y-4 border-l border-gray-700 pl-6 h-full">
        <h3 class="font-bold text-lg">Nyitvatartás</h3>
        <ul class="text-sm text-gray-400 space-y-1">
          <li class="flex justify-between"><span>Hétfő:</span> <span>8:00 - 19:00</span></li>
          <li class="flex justify-between"><span>Kedd:</span> <span>8:00 - 19:00</span></li>
          <li class="flex justify-between"><span>Szerda:</span> <span>8:00 - 19:00</span></li>
          <li class="flex justify-between"><span>Péntek:</span> <span>8:00 - 19:00</span></li>
          <li class="flex justify-between text-gray-500 italic mt-1"><span>Hétvége:</span> <span>Zárva</span></li>
        </ul>
      </div>

    </div>

    <div class="max-w-7xl mx-auto mt-12 pt-6 border-t border-gray-800 text-right">
      <p class="text-xs text-gray-500">
        Minden jog fenntartva &copy; {{ new Date().getFullYear() }} MolnArt
      </p>
    </div>
  </footer>
</template>

<style scoped>
/* Ha nem használsz ikon könyvtárat, itt adhatsz hozzá egyedi ikon stílusokat */
h3 {
  font-family: 'Judson', serif; /* Az előbb említett betűtípus */
  letter-spacing: 0.05em;
}
</style>