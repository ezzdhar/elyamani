import { onMounted, onUnmounted, nextTick } from 'vue'

export function useScrollReveal() {
  let observer: IntersectionObserver | null = null

  function initObserver() {
    if (!import.meta.client || typeof window === 'undefined') return

    // Mark document as supporting scroll reveal animations
    document.documentElement.classList.add('js-reveals')

    // If reduced motion is requested, immediately reveal all elements
    if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      document.querySelectorAll('.reveal-on-scroll').forEach((el) => {
        el.classList.add('is-revealed')
      })
      return
    }

    if (!('IntersectionObserver' in window)) {
      document.querySelectorAll('.reveal-on-scroll').forEach((el) => {
        el.classList.add('is-revealed')
      })
      return
    }

    if (observer) {
      observer.disconnect()
    }

    observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-revealed')
            observer?.unobserve(entry.target)
          }
        })
      },
      {
        threshold: 0.1,
        rootMargin: '0px 0px -40px 0px'
      }
    )

    const elements = document.querySelectorAll('.reveal-on-scroll:not(.is-revealed)')
    elements.forEach((el) => observer?.observe(el))
  }

  onMounted(() => {
    nextTick(() => {
      initObserver()
    })
  })

  onUnmounted(() => {
    if (observer) {
      observer.disconnect()
      observer = null
    }
  })

  return {
    initObserver
  }
}
