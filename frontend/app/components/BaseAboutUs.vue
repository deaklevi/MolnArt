<template>
  <section id="about" class="bg-[#faf9f6] py-12 md:py-24 px-4 overflow-hidden relative">
    
    <div class="absolute top-0 right-0 w-48 md:w-64 h-48 md:h-64 opacity-[0.03] md:opacity-5 pointer-events-none transform translate-x-1/4 -translate-y-1/4">
      <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
        <path fill="#44223b" d="M44.7,-76.4C58.1,-69.2,69.2,-56.1,76.4,-41.4C83.6,-26.7,86.9,-10.3,85.2,5.5C83.5,21.3,76.8,36.4,66.8,49.2C56.8,62,43.5,72.5,28.4,78.2C13.3,83.9,-3.6,84.8,-19.8,81C-36.1,77.2,-51.7,68.7,-64.1,56.6C-76.5,44.5,-85.7,28.8,-88.7,12.3C-91.7,-4.3,-88.4,-21.7,-79.8,-36C-71.2,-50.3,-57.3,-61.5,-42.6,-68.1C-27.9,-74.7,-14,-76.7,0.8,-78C15.6,-79.3,31.2,-79.9,44.7,-76.4Z" transform="translate(100 100)" />
      </svg>
    </div>

    <div class="max-w-7xl mx-auto relative z-10">
      <div class="flex flex-col lg:flex-row gap-10 lg:gap-16 items-center">
        
        <div class="w-full lg:w-1/2 space-y-6 md:space-y-8 text-center lg:text-left">
          <div class="flex flex-col items-center lg:items-start">
            <h2 class="text-3xl md:text-5xl font-serif text-[#44223b] mb-4 md:mb-6">Rólunk</h2>
            <div class="w-16 md:w-20 h-1 md:h-1.5 bg-[#44223b]"></div>
          </div>

          <div class="space-y-5 md:space-y-6 text-gray-700 leading-relaxed text-sm md:text-lg">
            <p class="text-lg md:text-xl font-serif italic text-[#44223b]/80 border-l-4 border-[#44223b]/20 pl-4 md:pl-6 mx-auto lg:mx-0 max-w-md lg:max-w-none text-left">
              "Több mint 15 éves tapasztalattal a hajszépítés művészetében."
            </p>
            
            <p class="font-light">
              Üdvözlünk a <span class="font-semibold text-[#44223b]">MolnArt Szalonban</span>! Szalonunk a legmodernebb technikákat és prémium minőségű termékeket használja, hogy vendégeink mindig elégedetten távozzanak.
            </p>
            
            <p class="font-light hidden sm:block">
              Csapatunk folyamatosan képzi magát, hogy a legújabb trendeket és technikákat sajátítsuk el. Nálunk <span class="italic font-medium">biztos kezekben van a hajad</span>.
            </p>

            <div class="pt-4 flex flex-col sm:flex-row items-center gap-4 text-[#44223b] text-left">
              <div class="p-3 bg-white rounded-full shadow-md shrink-0">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
              </div>
              <p class="font-medium text-center sm:text-left text-sm md:text-base">Budapest szívében, a Corvin negyedben várunk szeretettel!</p>
            </div>
          </div>
        </div>

        <div class="w-full lg:w-1/2 mt-4 lg:mt-0">
          <div class="relative group px-2 md:px-0">
            <div class="absolute border-2 border-[#44223b]/10 rounded-2xl transform translate-x-1 translate-y-1 md:translate-x-2 md:translate-y-2"></div>
            <div class="relative bg-white p-1 md:p-2 rounded-2xl shadow-xl overflow-hidden aspect-video lg:aspect-square max-h-87.5 md:max-h-125">
              <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d673.8041195109354!2d19.055501169653237!3d47.50517520414683!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4741dc6cce765981%3A0x4267edaa08e0e973!2sBudapest%2C%20Haj%C3%B3s%20u.%2033%2C%201065!5e0!3m2!1shu!2shu!4v1774733959101!5m2!1shu!2shu"
                class="w-full h-full rounded-xl grayscale-[0.2] contrast-[1.1]"
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy">
              </iframe>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'AboutSection'
}
</script>

<style scoped>
h2 {
  letter-spacing: -0.02em;
}
/* Görgetés közben ne zavarja a térkép az érintést mobilon */
iframe {
  pointer-events: auto;
}
@media (max-width: 768px) {
  iframe {
    pointer-events: none; /* Mobilon alapból nem görgethető, csak ha rákattintanak (megelőzi a beragadást) */
  }
  section:active iframe {
    pointer-events: auto;
  }
}
</style>