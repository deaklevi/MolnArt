<template>
  <footer class="bg-[#111111] text-white py-12 px-6 mt-auto">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-start gap-10">
      
      <div class="flex-shrink-0">
        <img src="" alt="MolnArt Logo" class="h-24 w-auto object-contain" />
      </div>

      <div class="flex-1">
        <h3 class="text-lg font-semibold mb-4 tracking-wider uppercase">Helyszín</h3>
        <div class="flex items-start gap-4">
          <div class="w-px h-16 bg-gray-500 hidden md:block"></div>
          <p class="text-gray-300">Budapest, Hajós utca.</p>
        </div>
      </div>

      <div class="flex-1">
        <h3 class="text-lg font-semibold mb-4 tracking-wider uppercase">Elérhetőség</h3>
        <div class="flex items-start gap-4">
          <div class="w-px h-32 bg-gray-500 hidden md:block"></div>
          <ul class="space-y-3">
            <li class="flex items-center gap-3 text-gray-300">
              <span class="iconify text-xl" data-icon="mdi:phone"></span>
              +36 30 123 4567
            </li>
            <li class="flex items-center gap-3 text-gray-300">
              <span class="iconify text-xl" data-icon="mdi:email-outline"></span>
              Email@M
            </li>
            <li class="flex items-center gap-3 text-gray-300">
              <span class="iconify text-xl" data-icon="mdi:instagram"></span>
              #moln.art
            </li>
            <li class="flex items-center gap-3 text-gray-300">
              <span class="iconify text-xl" data-icon="mdi:facebook"></span>
              MolnArt
            </li>
          </ul>
        </div>
      </div>

      <div class="flex-1">
        <h3 class="text-lg font-semibold mb-4 tracking-wider uppercase">Nyitvatartás</h3>
        <ul class="space-y-1 text-gray-300">
          <li>Hétfő: 8:00 - 19:00</li>
          <li>Kedd: 8:00 - 19:00</li>
          <li>Szerda: 8:00 - 19:00</li>
          <li>Péntek: 8:00 - 19:00</li>
          <li class="pt-2">Hétvége: Zárva</li>
        </ul>
      </div>
    </div>

    <div class="max-w-7xl mx-auto mt-12 pt-4 border-t border-gray-800 text-right">
      <p class="text-sm text-gray-500">
        Minden jog fenntartva © {{ new Date().getFullYear() }} MolnArt
      </p>
    </div>
  </footer>
</template>

<script setup>
// Ha használsz ikonokat (pl. Iconify vagy Lucide), importáld őket itt.
// Ebben a példában sima Tailwind segédosztályokat használtam.
</script>

<style scoped>
/* Egyedi stílusok, ha a Tailwind nem lenne elég */
h3 {
  font-family: serif; /* Ha a logóhoz hasonló betűtípust szeretnél a címeknek */
}
</style>