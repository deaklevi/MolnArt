// frontend/server/api/gallery.ts
import { readdirSync } from 'fs'
import { join } from 'path'

export default defineEventHandler((event) => {
  // Nuxt-ban a 'public' mappát így is elérhetjük biztonságosan:
  const galleryDir = join(process.cwd(), 'public', 'photos')

  try {
    const files = readdirSync(galleryDir)
    
    return files
      .filter(file => /\.(jpg|jpeg|png|webp|JPG|PNG)$/i.test(file))
      .map(file => `/photos/${file}`)
  } catch (e) {
    console.error("Galéria hiba:", e)
    return []
  }
})