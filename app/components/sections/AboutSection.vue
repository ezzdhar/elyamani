<template>
  <section id="about" class="about-section" aria-label="About Us">
    <!-- Architectural Watermark from Figma (#22:300) -->
    <div class="about-section__watermark" aria-hidden="true">
      <img src="/images/watermark-pattern.png" alt="" class="watermark-img" />
    </div>

    <div class="container about-section__container">
      <div class="about-section__grid">
        <!-- Content Column (#23:301, width 486px in Figma) -->
        <div class="about-section__content">
          <SectionBadge color="terracotta">WHO WE ARE</SectionBadge>
          
          <h2 class="about-section__title">ABOUT US</h2>

          <p class="about-section__body">
            ELYMANI brings architecture, interior design, and execution together under one integrated approach. From the first idea to the final detail, we take full responsibility for turning your vision into a space that's not just well designed — but well delivered.
          </p>

          <div class="about-section__action">
            <AppButton
              variant="primary"
              size="md"
              href="#services"
              :show-arrow="false"
              aria-label="View our projects - Discover our portfolio"
            >
              VIEW OUR PROJECTS
            </AppButton>
          </div>
        </div>

        <!-- Visual Showcase Column (#27:333, width 576px, height 533px in Figma) -->
        <div class="about-section__visual">
          <div class="visual-wrapper">
            <!-- Main Arched Architecture Image (#26:325, 378x502, radius 24px) -->
            <img
              src="/images/about-main.png"
              alt="Complete architectural and interior integration by ELYMANI"
              class="main-img"
              loading="lazy"
              width="378"
              height="502"
            />

            <!-- Floating Glass Card (#25:320, 263x162, radius 16px) -->
            <div class="glass-card" role="note" aria-label="Turnkey Solutions Highlight">
              <h3 class="glass-card__title">Turnkey Solutions</h3>
              <p class="glass-card__desc">
                Complete architectural design & turnkey execution under one roof.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import SectionBadge from '~/components/ui/SectionBadge.vue'
import AppButton from '~/components/ui/AppButton.vue'
</script>

<style scoped>
.about-section {
  position: relative;
  background-color: var(--color-green-primary);
  color: var(--color-sec-light);
  padding: 120px 0;
  overflow: hidden;
}

/* Watermark #22:300 */
.about-section__watermark {
  position: absolute;
  right: -100px;
  top: -80px;
  width: 1100px;
  height: 1100px;
  pointer-events: none;
  opacity: 0.10;
  z-index: 1;
  background-color: #FAF8F6;
  -webkit-mask-image: url('/images/watermark-pattern.png');
  mask-image: url('/images/watermark-pattern.png');
  -webkit-mask-size: contain;
  mask-size: contain;
  -webkit-mask-repeat: no-repeat;
  mask-repeat: no-repeat;
  -webkit-mask-position: center;
  mask-position: center;
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
  .about-section__watermark {
    width: 800px;
    height: 800px;
    right: -140px;
    top: -40px;
    opacity: 0.08;
  }
}

@media (max-width: 640px) {
  .about-section__watermark {
    width: 500px;
    height: 500px;
    right: -100px;
    top: 20px;
    opacity: 0.06;
  }
}

.about-section__container {
  position: relative;
  z-index: 2;
}

.about-section__grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 56px;
  align-items: center;
}

@media (min-width: 1024px) {
  .about-section__grid {
    grid-template-columns: 486px 1fr;
    justify-content: space-between;
    gap: 80px;
  }
}

/* Content (#23:301) */
.about-section__content {
  display: flex;
  flex-direction: column;
  gap: 24px;
  max-width: 486px;
}

.about-section__title {
  font-family: var(--font-heading);
  font-size: 2.25rem;
  line-height: 1.25;
  font-weight: 600;
  color: #FAF8F6;
  letter-spacing: 0.03em;
}

@media (min-width: 768px) {
  .about-section__title {
    font-size: 48px;
    line-height: 60px;
  }
}

.about-section__body {
  font-family: var(--font-sans);
  font-size: 1.125rem; /* 18px */
  line-height: 1.65;
  font-weight: 500;
  color: #FAF8F6;
  max-width: 486px;
}

@media (min-width: 768px) {
  .about-section__body {
    font-size: 18px;
    line-height: 29px;
  }
}

.about-section__action {
  margin-top: 12px;
}

/* Visual Showcase (#27:333, width 576px, height 533px) */
.about-section__visual {
  display: flex;
  justify-content: center;
}

@media (min-width: 1024px) {
  .about-section__visual {
    justify-content: flex-end;
  }
}

.visual-wrapper {
  position: relative;
  width: 100%;
  max-width: 576px;
  min-height: 533px;
  display: flex;
  justify-content: flex-end;
  align-items: flex-start;
}

/* Main Image #26:325 (378x502, radius 24px) */
.main-img {
  width: 100%;
  max-width: 378px;
  height: 502px;
  object-fit: cover;
  border-radius: 24px;
  box-shadow: 0px 4px 10px 0px rgba(0, 0, 0, 0.1), 0 20px 40px rgba(0, 0, 0, 0.3);
}

/* Floating Glass Card #25:320 (263x162, radius 16px) */
.glass-card {
  position: absolute;
  left: 0;
  bottom: 0;
  width: 263px;
  min-height: 162px;
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  border: 1px solid rgba(255, 255, 255, 0.25);
  border-radius: 16px;
  padding: 24px 28px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 10px;
  box-shadow: 0 16px 36px rgba(0, 0, 0, 0.3);
  z-index: 3;
}

@media (max-width: 640px) {
  .visual-wrapper {
    min-height: auto;
    flex-direction: column;
    align-items: center;
  }
  .glass-card {
    position: relative;
    margin-top: -60px;
    left: auto;
    bottom: auto;
  }
}

.glass-card__title {
  font-family: var(--font-heading);
  font-size: 20px;
  line-height: 30px;
  font-weight: 600;
  color: var(--color-cream);
}

.glass-card__desc {
  font-family: var(--font-sans);
  font-size: 14px;
  line-height: 21px;
  font-weight: 500;
  color: var(--color-sec-light);
}
</style>
