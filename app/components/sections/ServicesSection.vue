<template>
  <section id="services" class="services-section" aria-label="Our Services and Disciplines">
    <!-- Left Architectural Watermark (#63:90) -->
    <div class="services-section__watermark" aria-hidden="true">
      <img src="/images/watermark-pattern.png" alt="" class="watermark-img" />
    </div>

    <div class="container services-section__container">
      <div class="services-section__grid">
        <!-- 4-Photo Architectural Collage (#63:89, 612x572 in Figma) -->
        <div class="services-section__gallery" role="region" aria-label="Architecture and Design Portfolio Highlights">
          <picture>
            <source srcset="/images/services-collage.webp" type="image/webp" />
            <img
              src="/images/services-collage.png"
              alt="ELYMANI architectural facade, interior materials, marble execution, and living design"
              class="collage-img"
              loading="lazy"
              width="612"
              height="572"
            />
          </picture>
        </div>

        <!-- Content Column (#73:91, width 486px in Figma) -->
        <div class="services-section__content">
          <SectionBadge color="green">{{ t.services.badge }}</SectionBadge>

          <h2 class="services-section__title">
            <span>{{ t.services.titleLine1 }}</span>
            <span>{{ t.services.titleLine2 }}</span>
            <span v-if="t.services.titleLine3">{{ t.services.titleLine3 }}</span>
          </h2>

          <p class="services-section__body">
            {{ t.services.body }}
          </p>

          <div class="services-section__action">
            <AppButton
              variant="primary"
              size="md"
              href="#contact"
              :show-arrow="false"
              aria-label="Contact ELYMANI to discuss your project"
              style="width: 251px;"
            >
              {{ t.services.cta }}
            </AppButton>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import SectionBadge from '~/components/ui/SectionBadge.vue'
import AppButton from '~/components/ui/AppButton.vue'
import { useI18n } from '~/composables/useI18n'

const { t, isRtl } = useI18n()
</script>

<style scoped>
.services-section {
  position: relative;
  background-color: #B89974; /* Foundation /icon/Normal */
  color: var(--color-sec-light);
  padding: 120px 0;
  overflow: hidden;
}

/* Watermark (#63:90) */
.services-section__watermark {
  position: absolute;
  left: -180px;
  bottom: -80px;
  width: 1150px;
  height: 1150px;
  pointer-events: none;
  opacity: 0.14;
  z-index: 1;
  background-color: #FAF8F6;
  -webkit-mask-image: url('/images/watermark-pattern.png');
  mask-image: url('/images/watermark-pattern.png');
  -webkit-mask-size: contain;
  mask-size: contain;
  -webkit-mask-repeat: no-repeat;
  mask-repeat: no-repeat;
  -webkit-mask-position: center bottom;
  mask-position: center bottom;
}

@supports not ((mask-image: url('')) or (-webkit-mask-image: url(''))) {
  .watermark-img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    display: block;
  }
}

@supports (mask-image: url('')) or (-webkit-mask-image: url('')) {
  .watermark-img {
    display: none;
  }
}

@media (max-width: 1024px) {
  .services-section__watermark {
    width: 850px;
    height: 850px;
    left: -150px;
    bottom: -40px;
    opacity: 0.12;
  }
}

@media (max-width: 640px) {
  .services-section__watermark {
    width: 550px;
    height: 550px;
    left: -100px;
    bottom: -20px;
    opacity: 0.10;
  }
}

.services-section__container {
  position: relative;
  z-index: 2;
}

.services-section__grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 56px;
  align-items: center;
}

@media (min-width: 1024px) {
  .services-section__grid {
    grid-template-columns: 612px 1fr;
    justify-content: space-between;
    gap: 80px;
  }
}

/* Collage (#63:89, 612x572) */
.services-section__gallery {
  display: flex;
  justify-content: center;
}

.collage-img {
  width: 100%;
  max-width: 612px;
  height: auto;
  aspect-ratio: 612 / 572;
  object-fit: contain;
  filter: drop-shadow(0 16px 36px rgba(0, 0, 0, 0.2));
}

/* Content (#73:91, width 486px in Figma) */
.services-section__content {
  display: flex;
  flex-direction: column;
  gap: 24px;
  max-width: 486px;
}

.services-section__title {
  display: flex;
  flex-direction: column;
  font-family: var(--font-heading);
  font-size: 2.25rem;
  line-height: 1.25;
  font-weight: 600;
  color: #FAF8F6; /* Foundation /sec back/Light */
  letter-spacing: 0.02em;
}

@media (min-width: 768px) {
  .services-section__title {
    font-size: 48px;
    line-height: 60px;
  }
}

.services-section__body {
  font-family: var(--font-sans);
  font-size: 1.125rem;
  line-height: 1.55;
  font-weight: 500;
  color: #FAF8F6;
}

@media (min-width: 768px) {
  .services-section__body {
    font-size: 24px;
    line-height: 36px;
  }
}

.services-section__action {
  margin-top: 8px;
}

/* RTL Adjustments */
:global([dir="rtl"] .services-section__watermark) {
  left: auto;
  right: -180px;
}
</style>
