<template>
  <Transition name="app-preloader-fade">
    <div
      v-if="isLoading"
      class="app-preloader"
      role="status"
      aria-live="polite"
      aria-label="Loading ELYMANI Architecture experience"
    >
      <div class="app-preloader__inner">
        <!-- Animated Brand Emblem & Dual Concentric Rings -->
        <div class="app-preloader__emblem-box">
          <div class="app-preloader__ring app-preloader__ring--outer"></div>
          <div class="app-preloader__ring app-preloader__ring--inner"></div>
          <img
            :src="withBase('images/logo-icon.png')"
            alt="ELYMANI"
            class="app-preloader__logo"
            width="52"
            height="60"
          />
        </div>

        <!-- Brand Title & Subtitle -->
        <div class="app-preloader__brand">
          <span class="app-preloader__title">ELYMANI</span>
          <span class="app-preloader__subtitle">
            {{ isRtl ? `${t.hero.disciplines.architecture} • ${t.hero.disciplines.interiorDesign} • ${t.hero.disciplines.execution}` : 'Architecture • Interior Design • Execution' }}
          </span>
        </div>

        <!-- Progress Shimmer Bar -->
        <div class="app-preloader__progress">
          <div class="app-preloader__progress-bar"></div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, watch } from 'vue'
import { withBase } from '~/utils/asset'
import { useI18n } from '~/composables/useI18n'

const { t, isRtl } = useI18n()

const emit = defineEmits<{
  (e: 'loaded'): void
}>()

const isLoading = ref(true)

let isImageReady = false
let isMinTimePassed = false

function checkComplete() {
  if (isImageReady && isMinTimePassed) {
    isLoading.value = false
    emit('loaded')
  }
}

onMounted(() => {
  if (import.meta.client && typeof document !== 'undefined') {
    document.body.style.overflow = 'hidden'
  }

  // Smooth minimum display duration (800ms) on each refresh for cinematic polish
  setTimeout(() => {
    isMinTimePassed = true
    checkComplete()
  }, 800)

  // Preload background image so the website reveals with the visual fully ready
  const preloadImg = new Image()
  preloadImg.src = withBase('images/hero-bg-exact.webp')
  preloadImg.onload = () => {
    isImageReady = true
    checkComplete()
  }
  preloadImg.onerror = () => {
    const fallbackImg = new Image()
    fallbackImg.src = withBase('images/hero-bg-exact.png')
    fallbackImg.onload = () => {
      isImageReady = true
      checkComplete()
    }
    fallbackImg.onerror = () => {
      isImageReady = true
      checkComplete()
    }
  }

  // Safety timeout: never block the user longer than 3.2s
  setTimeout(() => {
    if (isLoading.value) {
      isLoading.value = false
      emit('loaded')
    }
  }, 3200)
})

onUnmounted(() => {
  if (import.meta.client && typeof document !== 'undefined') {
    document.body.style.overflow = ''
  }
})

watch(isLoading, (val) => {
  if (!val && import.meta.client && typeof document !== 'undefined') {
    document.body.style.overflow = ''
  }
})
</script>

<style scoped>
.app-preloader {
  position: fixed;
  inset: 0;
  width: 100vw;
  height: 100vh;
  height: 100dvh;
  z-index: 999999;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #0c1d20;
  background-image: 
    radial-gradient(circle at 50% 45%, rgba(20, 49, 53, 0.98) 0%, rgba(12, 29, 32, 0.99) 55%, #081417 100%);
  pointer-events: all;
  user-select: none;
}

.app-preloader__inner {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 24px;
  text-align: center;
  padding: 32px 24px;
  max-width: 90vw;
}

.app-preloader__emblem-box {
  position: relative;
  width: 108px;
  height: 108px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.app-preloader__ring {
  position: absolute;
  border-radius: 50%;
}

.app-preloader__ring--outer {
  inset: 0;
  border: 2px solid rgba(252, 239, 212, 0.14);
  border-top-color: var(--color-cream);
  /* animation: preloaderSpin 1.4s cubic-bezier(0.5, 0.1, 0.5, 0.9) infinite; */
  box-shadow: 0 0 24px rgba(252, 239, 212, 0.2);
}

.app-preloader__ring--inner {
  inset: 12px;
  border: 1.5px solid rgba(199, 107, 78, 0.18);
  border-bottom-color: var(--color-terracotta);
  /* animation: preloaderSpin 2.1s linear infinite reverse; */
}

.app-preloader__logo {
  position: relative;
  width: 52px;
  height: 60px;
  object-fit: contain;
  filter: drop-shadow(0 4px 16px rgba(0, 0, 0, 0.65));
  /* animation: preloaderPulse 2.2s ease-in-out infinite; */
}

.app-preloader__brand {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}

.app-preloader__title {
  font-family: var(--font-heading);
  font-size: 24px;
  font-weight: 700;
  letter-spacing: 0.22em;
  color: var(--color-cream);
  text-transform: uppercase;
}

.app-preloader__subtitle {
  font-family: var(--font-sans);
  font-size: 12px;
  font-weight: 500;
  letter-spacing: 0.16em;
  color: #c7beaf;
  text-transform: uppercase;
}

.app-preloader__progress {
  width: 160px;
  height: 2px;
  background-color: rgba(252, 239, 212, 0.12);
  border-radius: 2px;
  overflow: hidden;
  position: relative;
  margin-top: 4px;
}

.app-preloader__progress-bar {
  position: absolute;
  top: 0;
  left: 0;
  height: 100%;
  width: 45%;
  background: linear-gradient(90deg, transparent, var(--color-cream), transparent);
  /* animation: preloaderSlide 1.3s cubic-bezier(0.4, 0, 0.2, 1) infinite; */
}

/* Animations */
@keyframes preloaderSpin {
  to {
    transform: rotate(360deg);
  }
}

@keyframes preloaderPulse {
  0%, 100% {
    transform: scale(0.98);
    opacity: 0.9;
  }
  50% {
    transform: scale(1.02);
    opacity: 1;
    filter: drop-shadow(0 0 20px rgba(252, 239, 212, 0.4));
  }
}

@keyframes preloaderSlide {
  0% {
    left: -45%;
  }
  100% {
    left: 100%;
  }
}

/* Transition */
.app-preloader-fade-enter-active,
.app-preloader-fade-leave-active {
  transition: opacity 0.7s cubic-bezier(0.16, 1, 0.3, 1), transform 0.7s cubic-bezier(0.16, 1, 0.3, 1);
}

.app-preloader-fade-enter-from,
.app-preloader-fade-leave-to {
  opacity: 0;
  transform: scale(1.03);
  pointer-events: none;
}

@media (prefers-reduced-motion: reduce) {
  .app-preloader__ring,
  .app-preloader__logo,
  .app-preloader__progress-bar {
    /* animation: none !important; */
  }
  .app-preloader-fade-enter-active,
  .app-preloader-fade-leave-active {
    transition: opacity 0.1s !important;
  }
}
</style>
