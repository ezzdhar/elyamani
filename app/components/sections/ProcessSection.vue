<template>
  <section id="process" class="process-section" aria-label="Our Process">
    <!-- Centered Architectural Watermark (#52:72) -->
    <div class="process-section__watermark" aria-hidden="true">
      <img src="/images/watermark-pattern.png" alt="" class="watermark-img" />
    </div>

    <div class="container process-section__container">
      <!-- Section Header (#34:343) -->
      <div class="process-section__header">
        <SectionBadge color="terracotta">{{ t.process.badge }}</SectionBadge>
        
        <h2 class="process-section__title">
          <span>{{ t.process.titleLine1 }}</span>
          <span>{{ t.process.titleLine2 }}</span>
        </h2>

        <p class="process-section__subtitle">
          {{ t.process.subtitlePart1 }}
          <span class="process-section__highlight">{{ t.process.subtitleHighlight }}</span>
          {{ t.process.subtitlePart2 }}
        </p>
      </div>

      <!-- 3 Cards Row (#52:70) -->
      <div class="process-section__grid">
        <article
          v-for="(card, index) in processSteps"
          :key="index"
          class="process-card"
          tabindex="0"
        >
          <!-- Image 250px height, radius 16px with hover overlay -->
          <div class="process-card__image-wrap">
            <img
              :src="getImageUrl(card.image)"
              :alt="card.alt"
              class="process-card__image"
              loading="lazy"
              width="380"
              height="250"
            />
            <div class="process-card__overlay" aria-hidden="true">
              <span class="process-card__overlay-title">{{ card.title }}</span>
            </div>
          </div>

          <!-- Description max-width 328px, color #422517 -->
          <div class="process-card__body">
            <p class="process-card__text">{{ card.description }}</p>
          </div>
        </article>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import SectionBadge from '~/components/ui/SectionBadge.vue'
import { useI18n } from '~/composables/useI18n'

const { app: { baseURL } } = useRuntimeConfig()
const { t, isRtl } = useI18n()

function getImageUrl(path: string) {
  if (!path) return ''
  if (path.startsWith('http')) return path
  const cleanBase = (baseURL || '/').endsWith('/') ? (baseURL || '/') : `${baseURL}/`
  const cleanPath = path.startsWith('/') ? path.slice(1) : path
  return `${cleanBase}${cleanPath}`
}

const processSteps = computed(() => [
  {
    title: t.value.process.step1Title,
    image: '/images/process-1.png',
    alt: 'Concept drafting and architectural plans by ELYMANI',
    description: t.value.process.step1Desc
  },
  {
    title: t.value.process.step2Title,
    image: '/images/process-2.png',
    alt: 'On-site construction supervision and engineering',
    description: t.value.process.step2Desc
  },
  {
    title: t.value.process.step3Title,
    image: '/images/process-3.png',
    alt: 'Luxury turnkey architectural delivery and final handover',
    description: t.value.process.step3Desc
  }
])
</script>

<style scoped>
.process-section {
  position: relative;
  background-color: var(--color-cream-active); /* #FEFAF2 */
  color: var(--color-card-darker);
  padding: clamp(72px, 8vw, 116px) 0 clamp(64px, 7vw, 96px);
  overflow: hidden;
}

/* Watermark Pattern (#52:72) */
.process-section__watermark {
  position: absolute;
  left: 50%;
  top: clamp(-220px, -13.5vw, -130px);
  transform: translateX(-50%);
  width: clamp(920px, 88vw, 2500px);
  height: clamp(920px, 88vw, 2500px);
  pointer-events: none;
  background-color: #B89974;
  opacity: 0.16;
  -webkit-mask-image: url('/images/watermark-pattern.png');
  mask-image: url('/images/watermark-pattern.png');
  -webkit-mask-size: contain;
  mask-size: contain;
  -webkit-mask-repeat: no-repeat;
  mask-repeat: no-repeat;
  -webkit-mask-position: center top;
  mask-position: center top;
  z-index: 1;
}

@supports not ((mask-image: url('')) or (-webkit-mask-image: url(''))) {
  .watermark-img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    display: block;
    filter: sepia(1) saturate(3) hue-rotate(345deg) brightness(0.7);
  }
}

@supports (mask-image: url('')) or (-webkit-mask-image: url('')) {
  .watermark-img {
    display: none;
  }
}

@media (max-width: 1024px) {
  .process-section__watermark {
    width: 118vw;
    height: 118vw;
    min-width: 780px;
    min-height: 780px;
    top: -150px;
    opacity: 0.14;
  }
}

@media (max-width: 640px) {
  .process-section__watermark {
    width: 150vw;
    height: 150vw;
    min-width: 560px;
    min-height: 560px;
    top: -64px;
    opacity: 0.11;
  }
}

.process-section__container {
  position: relative;
  z-index: 2;
  display: flex;
  flex-direction: column;
  gap: clamp(46px, 5vw, 74px);
}

/* Header (#34:343) */
.process-section__header {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  max-width: 680px;
  margin: 0 auto;
  gap: 16px;
}

.process-section__title {
  display: flex;
  flex-direction: column;
  font-family: var(--font-heading);
  font-size: clamp(2.1rem, 4.8vw, 4rem);
  line-height: 1.25;
  font-weight: 700;
  color: var(--color-green-primary);
  letter-spacing: 0.02em;
}

@media (min-width: 768px) {
  .process-section__title {
    line-height: 1.28;
  }
}

.process-section__subtitle {
  font-family: var(--font-sans);
  font-size: clamp(1.05rem, 2vw, 1.75rem);
  line-height: 1.55;
  font-weight: 500;
  color: #494139; /* Foundation /sec back/Darker */
  max-width: 760px;
}

@media (min-width: 768px) {
  .process-section__subtitle {
    line-height: 1.5;
  }
}

.process-section__highlight {
  color: var(--color-terracotta);
  font-weight: 600;
}

/* Grid (#52:70, gap: 74px in Figma) */
.process-section__grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 40px;
}

@media (min-width: 768px) {
  .process-section__grid {
    grid-template-columns: repeat(3, 1fr);
    gap: clamp(28px, 4vw, 74px);
  }
}

@media (min-width: 1100px) {
  .process-section__grid {
    gap: 74px;
  }
}

/* Process Card (#52:67, width: 350px in Figma) */
.process-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 24px;
  cursor: pointer;
  border-radius: 16px;
  outline: none;
}

.process-card:focus-visible {
  outline: 2px solid var(--color-green-primary);
  outline-offset: 4px;
}

.process-card__image-wrap {
  position: relative;
  width: 100%;
  aspect-ratio: 1.4 / 1;
  height: auto;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
}

.process-card__image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform var(--transition-slow);
}

.process-card:hover .process-card__image,
.process-card:focus-visible .process-card__image {
  transform: scale(1.04);
}

/* Hover Overlay (#Frame 57, 58, 59 in Figma) */
.process-card__overlay {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: rgba(20, 49, 53, 0.72);
  opacity: 0;
  transition: opacity var(--transition-base);
  pointer-events: none;
}

.process-card__overlay-title {
  font-family: var(--font-heading);
  font-size: 24px;
  line-height: 1.2;
  font-weight: 700;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  color: var(--color-cream);
  text-align: center;
  padding: 0 16px;
  transform: translateY(6px);
  transition: transform var(--transition-base);
}

.process-card:hover .process-card__overlay,
.process-card:focus-within .process-card__overlay,
.process-card:focus-visible .process-card__overlay {
  opacity: 1;
}

.process-card:hover .process-card__overlay-title,
.process-card:focus-within .process-card__overlay-title,
.process-card:focus-visible .process-card__overlay-title {
  transform: translateY(0);
}

/* Body (#52:62, width: 328px) */
.process-card__body {
  width: 100%;
  max-width: 328px;
  display: flex;
  justify-content: center;
  text-align: center;
}

.process-card__text {
  font-family: var(--font-sans);
  font-size: 16px;
  line-height: 24px;
  font-weight: 600;
  color: #422517; /* Foundation /button/Darker */
  text-align: center;
}
</style>
