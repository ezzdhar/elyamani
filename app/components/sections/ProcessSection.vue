<template>
  <section id="process" class="process-section" aria-label="Our Process">
    <!-- Centered Architectural Watermark (#52:72) -->
    <div class="process-section__watermark" aria-hidden="true">
      <img src="/images/watermark-pattern.png" alt="" class="watermark-img" />
    </div>

    <div class="container process-section__container">
      <!-- Section Header (#34:343) -->
      <div class="process-section__header">
        <SectionBadge color="terracotta">WHO WE ARE</SectionBadge>
        
        <h2 class="process-section__title">
          <span>FROM VISION TO</span>
          <span>REALITY</span>
        </h2>

        <p class="process-section__subtitle">
          Design, construction and finishing run as
          <span class="process-section__highlight">one continuous project</span>
          under one team, so nothing gets lost between stages.
        </p>
      </div>

      <!-- 3 Cards Row (#52:70) -->
      <div class="process-section__grid">
        <article
          v-for="(card, index) in processSteps"
          :key="index"
          class="process-card"
        >
          <!-- Image 250px height, radius 16px -->
          <div class="process-card__image-wrap">
            <img
              :src="card.image"
              :alt="card.alt"
              class="process-card__image"
              loading="lazy"
              width="380"
              height="250"
            />
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
import SectionBadge from '~/components/ui/SectionBadge.vue'

const processSteps = [
  {
    image: '/images/process-1.png',
    alt: 'Concept drafting and architectural plans by ELYMANI',
    description: 'We translate your vision into thoughtful architectural and interior concepts — every detail considered before we build.'
  },
  {
    image: '/images/process-2.png',
    alt: 'On-site construction supervision and engineering',
    description: 'We bring the design to life through precise construction and hands-on site supervision, every step of the way.'
  },
  {
    image: '/images/process-3.png',
    alt: 'Luxury turnkey architectural delivery and final handover',
    description: 'We stay accountable to quality and detail until the final handover — because delivery is part of the design.'
  }
]
</script>

<style scoped>
.process-section {
  position: relative;
  background-color: var(--color-cream-active); /* #FEFAF2 */
  color: var(--color-card-darker);
  padding: 120px 0;
  overflow: hidden;
}

/* Watermark Pattern (#52:72) */
.process-section__watermark {
  position: absolute;
  left: 50%;
  top: 40px;
  transform: translateX(-50%);
  width: 1250px;
  height: 1250px;
  pointer-events: none;
  background-color: #B89974;
  opacity: 0.18;
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
    width: 900px;
    height: 900px;
    top: 60px;
    opacity: 0.15;
  }
}

@media (max-width: 640px) {
  .process-section__watermark {
    width: 600px;
    height: 600px;
    top: 40px;
    opacity: 0.12;
  }
}

.process-section__container {
  position: relative;
  z-index: 2;
  display: flex;
  flex-direction: column;
  gap: 60px;
}

/* Header (#34:343) */
.process-section__header {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  max-width: 680px;
  margin: 0 auto;
  gap: 18px;
}

.process-section__title {
  display: flex;
  flex-direction: column;
  font-family: var(--font-heading);
  font-size: 2.25rem;
  line-height: 1.25;
  font-weight: 600;
  color: var(--color-green-primary);
  letter-spacing: 0.02em;
}

@media (min-width: 768px) {
  .process-section__title {
    font-size: 48px;
    line-height: 60px;
  }
}

.process-section__subtitle {
  font-family: var(--font-sans);
  font-size: 1.125rem;
  line-height: 1.55;
  font-weight: 500;
  color: #494139; /* Foundation /sec back/Darker */
}

@media (min-width: 768px) {
  .process-section__subtitle {
    font-size: 24px;
    line-height: 36px;
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
    gap: 32px;
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
  gap: 20px;
}

.process-card__image-wrap {
  width: 100%;
  height: 250px;
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

.process-card:hover .process-card__image {
  transform: scale(1.04);
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
