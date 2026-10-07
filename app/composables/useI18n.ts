import { computed, watch, onMounted } from 'vue'
import { useState } from '#imports'
import { en } from '~/locales/en'
import { ar } from '~/locales/ar'

export type Locale = 'en' | 'ar'

export function useI18n() {
  const locale = useState<Locale>('app_locale', () => 'en')

  const isRtl = computed(() => locale.value === 'ar')
  const dir = computed(() => (isRtl.value ? 'rtl' : 'ltr'))
  const t = computed(() => (locale.value === 'ar' ? ar : en))

  function updateDom(loc: Locale) {
    if (import.meta.client && typeof document !== 'undefined') {
      const isArabic = loc === 'ar'
      document.documentElement.lang = loc
      const prevRtl = document.documentElement.classList.contains('rtl')
      const resetX = () => {
        if (window.scrollX !== 0) {
          window.scrollTo({ left: 0, top: window.scrollY, behavior: 'instant' as ScrollBehavior })
        }
      }
      requestAnimationFrame(resetX)
      setTimeout(resetX, 300)
      setTimeout(resetX, 1200)
      void prevRtl
      
      // Apply RTL to body and class rtl to avoid Chromium root scrollbar relocation to the left
      // which shifts and cuts off the viewport in Windows browsers.
      if (document.body) {
        document.body.dir = isArabic ? 'rtl' : 'ltr'
        if (isArabic) {
          document.body.classList.add('rtl')
        } else {
          document.body.classList.remove('rtl')
        }
      }

      if (isArabic) {
        document.documentElement.classList.add('rtl')
      } else {
        document.documentElement.classList.remove('rtl')
      }
      try {
        localStorage.setItem('elyamani_locale', loc)
      } catch {
        // ignore storage errors
      }
    }
  }

  function setLocale(newLocale: Locale) {
    locale.value = newLocale
    updateDom(newLocale)
  }

  function toggleLocale() {
    setLocale(locale.value === 'en' ? 'ar' : 'en')
  }

  onMounted(() => {
    if (import.meta.client && typeof document !== 'undefined') {
      try {
        const saved = localStorage.getItem('elyamani_locale') as Locale | null
        if (saved === 'ar' || saved === 'en') {
          if (locale.value !== saved) {
            locale.value = saved
          }
          updateDom(saved)
        } else {
          updateDom(locale.value)
        }
      } catch {
        updateDom(locale.value)
      }
    }
  })

  watch(locale, (newLoc) => {
    updateDom(newLoc)
  })

  return {
    locale,
    t,
    isRtl,
    dir,
    setLocale,
    toggleLocale
  }
}
