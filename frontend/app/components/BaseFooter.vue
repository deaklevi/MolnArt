<template>
  <footer class="bg-[#121212] text-white py-12 px-6">
    <div class="max-w-6xl mx-auto">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8 items-start">
        
        <div class="flex justify-center md:justify-start">
          <!-- <img src="/logo-white.png" alt="MolnArt Logo" class="h-24 w-auto object-contain" /> -->
          </div>

        <div class="md:border-l border-gray-700 md:pl-8 h-full">
          <h4 class="text-lg font-semibold mb-6 uppercase tracking-wider">Helyszín</h4>
          <p class="text-gray-400">Budapest, Hajós utca. 31</p>
        </div>

        <div class="md:border-l border-gray-700 md:pl-8 h-full">
          <h4 class="text-lg font-semibold mb-6 uppercase tracking-wider">Elérhetőség</h4>
          <ul class="space-y-4 text-gray-400">
            <li class="flex items-center gap-3">
              <span class="text-xl">📞</span>
              <a href="tel:+36202210381" class="hover:text-white transition">+36 30 935 3044</a>
            </li>
            <li class="flex items-center gap-3">
              <span class="text-xl">✉️</span>
              <a href="mailto:szucsmarcell2005@gmail.com" class="hover:text-white transition">szucsmarcell2005@gmail.com</a>
            </li>
            <li class="flex items-center gap-3">
              <span class="text-xl">📸</span>
              <a href="#" class="hover:text-white transition">#moln.art</a>
            </li>
            <li class="flex items-center gap-3">
              <span class="text-xl">👤</span>
              <a href="#" class="hover:text-white transition">MolnArt</a>
            </li>
          </ul>
        </div>

        <div class="md:border-l border-gray-700 md:pl-8 h-full">
          <h4 class="text-lg font-semibold mb-6 uppercase tracking-wider">Nyitvatartás</h4>
          <ul class="space-y-2 text-gray-400">
            <li class="flex justify-between"><span>Hétfő:</span> <span>8:00 - 19:00</span></li>
            <li class="flex justify-between"><span>Kedd:</span> <span>8:00 - 19:00</span></li>
            <li class="flex justify-between"><span>Szerda:</span> <span>8:00 - 19:00</span></li>
            <li class="flex justify-between"><span>Csütörtök:</span> <span>8:00 - 19:00</span></li>
            <li class="flex justify-between"><span>Péntek:</span> <span>8:00 - 19:00</span></li>
            <li class="mt-4 flex justify-between text-red-400 font-medium">
              <span>Hétvége:</span> <span>Zárva</span>
            </li>
          </ul>
        </div>
      </div>

      <div class="mt-16 pt-8 border-t border-gray-800 flex flex-col md:flex-row justify-end items-center">
        <p class="text-gray-500 text-sm">
          Minden jog fenntartva © 2026 MolnArt
        </p>
      </div>
    </div>
  </footer>
</template>

<style scoped>
h4 {
  letter-spacing: 0.1em;
}
</style>