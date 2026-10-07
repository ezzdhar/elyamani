<template>
  <section id="home" class="hero-section" aria-label="Hero">
    <!-- Hero Background Image (Exact framing from Figma) -->
    <div class="hero-section__bg-wrap" aria-hidden="true">
      <picture>
        <source :srcset="withBase('images/hero-bg-exact.webp')" type="image/webp" />
        <img
          ref="heroImgRef"
          :src="withBase('images/hero-bg-exact.png')"
          alt=""
          class="hero-section__bg-img"
          :class="{ 'is-loaded': !isLoading }"
          fetchpriority="high"
          loading="eager"
          @load="handleImageLoad"
          @error="handleImageLoad"
        />
      </picture>
      <!-- Atmospheric overlay with sharp background & gentle bottom fade into next section -->
      <div class="hero-section__overlay"></div>
    </div>

    <div class="container hero-section__container">
      <div class="hero-section__content">
        <!-- Disciplines Breadcrumb / Tag -->
        <div class="hero-section__disciplines" aria-label="Our Core Disciplines">
          <span class="discipline-tag">{{ t.hero.disciplines.architecture }}</span>
          <span class="discipline-dot" aria-hidden="true">•</span>
          <span class="discipline-tag">{{ t.hero.disciplines.interiorDesign }}</span>
          <span class="discipline-dot" aria-hidden="true">•</span>
          <span class="discipline-tag">{{ t.hero.disciplines.execution }}</span>
        </div>

        <!-- Main Title (3 lines: DESIGN YOUR / SPACE WITH / PURPOSE) -->
        <h1 class="hero-section__title">
          <span class="title-line title-light">{{ t.hero.titleLine1 }}</span>
          <span class="title-line title-light">{{ t.hero.titleLine2 }}</span>
          <span class="title-line title-accent">{{ t.hero.titleLine3 }}</span>
        </h1>

        <!-- Subtitle -->
        <p class="hero-section__subtitle">
          {{ t.hero.subtitle }}
        </p>

        <!-- CTA Buttons matching Figma dimensions (height 54px, radius 3px) -->
        <div class="hero-section__actions">
          <AppButton
            variant="primary"
            size="md"
            href="#contact"
            :show-arrow="false"
            class="hero-btn hero-btn--primary"
            aria-label="Book a consultation with ELYMANI"
          >
            {{ t.hero.ctaConsultation }}
          </AppButton>

          <AppButton
            variant="secondary"
            size="md"
            href="#services"
            :show-arrow="false"
            class="hero-btn hero-btn--secondary"
            aria-label="Build your workspace - Explore services"
          >
            {{ t.hero.ctaWorkspace }}
          </AppButton>
        </div>
      </div>

      <!-- Bottom Glassmorphic Feature Bar -->
      <div class="hero-section__feature-bar" role="region" aria-label="Key Commitments">
        <div class="feature-item">
          <img
            src="/images/icon-cube-alt.svg"
            alt=""
            aria-hidden="true"
            class="feature-item__icon"
            width="34"
            height="34"
          />
          <span class="feature-item__text" v-html="t.hero.features.partner"></span>
        </div>

        <div class="feature-divider" aria-hidden="true"></div>

        <div class="feature-item">
          <img
            src="/images/icon-weather-sunny.svg"
            alt=""
            aria-hidden="true"
            class="feature-item__icon"
            width="34"
            height="34"
          />
          <span class="feature-item__text" v-html="t.hero.features.precision"></span>
        </div>

        <div class="feature-divider" aria-hidden="true"></div>

        <div class="feature-item">
          <img
            src="/images/icon-cube-five.svg"
            alt=""
            aria-hidden="true"
            class="feature-item__icon"
            width="34"
            height="34"
          />
          <span class="feature-item__text" v-html="t.hero.features.delivery"></span>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import AppButton from '~/components/ui/AppButton.vue'
import { withBase } from '~/utils/asset'
import { useI18n } from '~/composables/useI18n'

const { t, isRtl } = useI18n()

const isLoading = ref(false)
const heroImgRef = ref<HTMLImageElement | null>(null)
function handleImageLoad() {
  isLoading.value = false
}
</script>

<style scoped>
.hero-section {
  position: relative;
  min-height: 100vh;
  min-height: 100dvh;
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding-top: calc(84px + 50px);
  padding-bottom: 90px;
  overflow: hidden;
  background-color: #0c1d20;
}

/* Background & Sharp Atmospheric Overlay */
.hero-section__bg-wrap {
  position: absolute;
  inset: 0;
  z-index: 1;
}

.hero-section__bg-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
}

/* 
 * The overlay leaves the photo recognizable and sharp across the body of the hero,
 * and only fades gently into the dark teal (#143135) of the next section right at the bottom edge.
 */
.hero-section__overlay {
  position: absolute;
  inset: 0;
  background: 
    linear-gradient(90deg, rgba(0, 0, 0, 0.44) 0%, rgba(0, 0, 0, 0.16) 45%, transparent 75%),
    linear-gradient(180deg, 
      rgba(0, 0, 0, 0.38) 0%, 
      rgba(0, 0, 0, 0.18) 45%, 
      rgba(0, 0, 0, 0.22) 75%, 
      rgba(20, 49, 53, 0.5) 88%, 
      rgba(20, 49, 53, 0.9) 97%, 
      #143135 100%
    );
  pointer-events: none;
}

:global([dir="rtl"] .hero-section__overlay) {
  background: 
    linear-gradient(270deg, rgba(0, 0, 0, 0.44) 0%, rgba(0, 0, 0, 0.16) 45%, transparent 75%),
    linear-gradient(180deg, 
      rgba(0, 0, 0, 0.38) 0%, 
      rgba(0, 0, 0, 0.18) 45%, 
      rgba(0, 0, 0, 0.22) 75%, 
      rgba(20, 49, 53, 0.5) 88%, 
      rgba(20, 49, 53, 0.9) 97%, 
      #143135 100%
    );
}

/* Container: shares the exact centered container with Header and About Us */
.hero-section__container {
  position: relative;
  z-index: 2;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  width: 100%;
}

/* Content Frame */
.hero-section__content {
  max-width: 520px;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

/* Disciplines Tag */
.hero-section__disciplines {
  display: inline-flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: clamp(12px, 1.8vh, 18px);
}

.discipline-tag {
  font-family: var(--font-sans);
  font-size: 13px;
  font-weight: 600;
  line-height: 1.2;
  letter-spacing: 0.09em;
  color: #FAF8F6;
  text-transform: uppercase;
}

.discipline-dot {
  color: rgba(250, 248, 246, 0.7);
  font-size: 11px;
  margin: 0 1px;
}

/* Main Heading (3 lines: DESIGN YOUR / SPACE WITH / PURPOSE) */
.hero-section__title {
  display: flex;
  flex-direction: column;
  font-family: var(--font-heading);
  font-size: clamp(2.75rem, 3.8vw, 3.5rem); /* ~52px to 56px */
  line-height: 1.2;
  letter-spacing: 0.03em;
  font-weight: 700;
  text-transform: uppercase;
  margin-bottom: clamp(18px, 2.2vh, 24px);
  max-width: fit-content;
}

.title-line {
  display: block;
  white-space: nowrap;
}

.title-light {
  color: #FAF8F6;
}

.title-accent {
  color: #FCEFD4;
}

/* Subtitle */
.hero-section__subtitle {
  font-family: var(--font-sans);
  font-size: clamp(1.125rem, 1.4vw, 1.35rem); /* ~20px to 22px */
  line-height: 1.55;
  font-weight: 500;
  color: #FAF8F6;
  max-width: 486px;
  margin-bottom: clamp(28px, 3.5vh, 36px);
}

/* CTA Actions */
.hero-section__actions {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 18px;
  margin-bottom: clamp(48px, 6.5vh, 64px);
}

.hero-btn {
  height: 54px !important;
  padding: 0 28px !important;
  font-size: 13.5px !important;
  font-weight: 700 !important;
  letter-spacing: 0.05em !important;
  border-radius: 3px !important;
}

.hero-btn--primary {
  background-color: #FCEFD4 !important;
  color: #143135 !important;
  border: 1px solid #FCEFD4 !important;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15) !important;
}

.hero-btn--primary:hover {
  background-color: #FAF8F6 !important;
  border-color: #FAF8F6 !important;
  color: #0c1d20 !important;
  transform: translateY(-2px) !important;
}

.hero-btn--secondary {
  background-color: transparent !important;
  color: #FAF8F6 !important;
  border: 1.5px solid rgba(250, 248, 246, 0.6) !important;
}

.hero-btn--secondary:hover {
  background-color: rgba(250, 248, 246, 0.12) !important;
  border-color: #FAF8F6 !important;
  color: #FAF8F6 !important;
  transform: translateY(-2px) !important;
}

/* Feature Bar */
.hero-section__feature-bar {
  display: flex;
  flex-direction: column;
  background: rgba(0, 0, 0, 0.22);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
  border: 1px solid rgba(250, 248, 246, 0.14);
  border-radius: 3px;
  padding: 18px 24px;
  gap: 20px;
  width: 100%;
  max-width: 686px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

@media (min-width: 768px) {
  .hero-section__feature-bar {
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    height: 96px;
    padding: 0 28px;
  }
}

.feature-item {
  display: flex;
  align-items: center;
  gap: 14px;
}

.feature-item__icon {
  width: 34px;
  height: 34px;
  flex-shrink: 0;
}

.feature-item__text {
  font-family: var(--font-sans);
  font-size: 13.5px;
  font-weight: 600;
  line-height: 1.35;
  color: #FAF8F6;
  letter-spacing: 0.03em;
  text-transform: uppercase;
}

.feature-divider {
  display: none;
}

@media (min-width: 768px) {
  .feature-divider {
    display: block;
    width: 1px;
    height: 38px;
    background-color: rgba(250, 248, 246, 0.25);
    flex-shrink: 0;
  }
}
</style>
