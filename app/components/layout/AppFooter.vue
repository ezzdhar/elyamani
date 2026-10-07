<template>
  <footer class="app-footer" role="contentinfo">
    <div class="container app-footer__container">
      <div class="app-footer__grid">
        <!-- Brand Column (#82:14969, width: 188px in Figma) -->
        <div class="app-footer__col app-footer__col--brand">
          <div class="app-footer__brand">
            <img
              src="/images/logo-text.png"
              :alt="t.footer.brand"
              class="app-footer__logo"
              width="120"
              height="33"
            />
          </div>

          <p class="app-footer__description" v-html="t.footer.description"></p>

          <!-- Social Icons (#82:15014) -->
          <div class="app-footer__socials" aria-label="Social media profiles">
            <a
              href="https://www.facebook.com/profile.php?id=61594554461085"
              target="_blank"
              rel="noopener noreferrer"
              class="app-footer__social-btn"
              aria-label="Visit ELYMANI on Facebook"
            >
              <img src="/images/icon-facebook.svg" alt="" aria-hidden="true" width="12" height="24" />
            </a>
            <a
              href="https://www.instagram.com/elyamani.2"
              target="_blank"
              rel="noopener noreferrer"
              class="app-footer__social-btn"
              aria-label="Visit ELYMANI on Instagram"
            >
              <img src="/images/icon-instagram.svg" alt="" aria-hidden="true" width="22" height="22" />
            </a>
            <a
              href="https://www.tiktok.com/@elyamanicfd"
              target="_blank"
              rel="noopener noreferrer"
              class="app-footer__social-btn"
              aria-label="Visit ELYMANI on TikTok"
            >
              <img src="/images/icon-tiktok.svg" alt="" aria-hidden="true" width="20" height="23" />
            </a>
          </div>
        </div>

        <!-- Quick Links (#82:14970) -->
        <div class="app-footer__col">
          <h2 class="app-footer__title">{{ t.footer.quickLinksTitle }}</h2>
          <ul class="app-footer__links" role="list">
            <li><a href="#about" class="app-footer__link">{{ t.footer.links.about }}</a></li>
            <li><a href="#process" class="app-footer__link">{{ t.footer.links.process }}</a></li>
            <li><a href="#services" class="app-footer__link">{{ t.footer.links.services }}</a></li>
            <li><a href="#contact" class="app-footer__link">{{ t.footer.links.contact }}</a></li>
          </ul>
        </div>

        <!-- Services (#82:14978) -->
        <div class="app-footer__col">
          <h2 class="app-footer__title">{{ t.footer.servicesTitle }}</h2>
          <ul class="app-footer__links" role="list">
            <li><a href="#services" class="app-footer__link">{{ t.footer.servicesList.architecturalDesign }}</a></li>
            <li><a href="#services" class="app-footer__link">{{ t.footer.servicesList.interiorDesign }}</a></li>
            <li><a href="#services" class="app-footer__link">{{ t.footer.servicesList.executionAndFinishing }}</a></li>
            <li><a href="#services" class="app-footer__link">{{ t.footer.servicesList.projectManagement }}</a></li>
          </ul>
        </div>

        <!-- Get In Touch (#82:14985) -->
        <div class="app-footer__col">
          <h2 class="app-footer__title">{{ t.footer.getInTouchTitle }}</h2>
          <address class="app-footer__address">
            <p class="app-footer__contact-text">{{ t.footer.location }}</p>
            <p class="app-footer__contact-text">
              <a href="mailto:Info@Elymani.Com" class="app-footer__link">Info@Elymani.Com</a>
            </p>
            <p class="app-footer__contact-text">
              <a
                href="https://api.whatsapp.com/send?phone=201090099517"
                target="_blank"
                rel="noopener noreferrer"
                class="app-footer__link"
                aria-label="Contact us on WhatsApp: +201090099517"
              >
                +201090099517
              </a>
            </p>
          </address>
        </div>
      </div>

      <!-- Divider line (#82:14996, width 1248px) -->
      <div class="app-footer__divider" aria-hidden="true"></div>

      <!-- Bottom Bar -->
      <div class="app-footer__bottom">
        <p class="app-footer__copyright">
          {{ t.footer.copyright }}
        </p>
      </div>
    </div>
  </footer>
</template>

<script setup lang="ts">
import { useI18n } from '~/composables/useI18n'

const { t, isRtl } = useI18n()
</script>

<style scoped>
.app-footer {
  background-color: var(--color-green-primary); /* #143135 */
  color: var(--color-sec-light);
  padding-top: 80px;
  padding-bottom: 40px;
}

.app-footer__container {
  display: flex;
  flex-direction: column;
}

.app-footer__grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 48px;
}

@media (min-width: 640px) {
  .app-footer__grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (min-width: 1024px) {
  .app-footer__grid {
    grid-template-columns: 1.2fr 1fr 1.3fr 1fr;
    gap: 60px;
  }
}

/* Brand */
.app-footer__brand {
  margin-bottom: 8px;
}

.app-footer__logo {
  height: 78px;
  width: auto;
  object-fit: contain;
}

.app-footer__description {
  font-family: var(--font-sans);
  font-size: 18px;
  line-height: 27px;
  font-weight: 500;
  color: #E0E0E0; /* fill_1269856c */
  margin-bottom: 24px;
}

/* Socials (#82:15014) */
.app-footer__socials {
  display: flex;
  align-items: center;
  gap: 20px;
}

.app-footer__social-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 46px;
  height: 46px;
  border: 1px solid #E6E6E6;
  border-radius: var(--radius-full);
  color: var(--color-sec-light);
  transition: all var(--transition-fast);
}

.app-footer__social-btn:hover {
  background-color: var(--color-cream);
  border-color: var(--color-cream);
  transform: translateY(-2px);
}

.app-footer__social-btn:hover img {
  filter: brightness(0);
}

/* Column Titles (style_c0ba38e6: 18px, 27px line-height, SemiCondensed Semibold 600) */
.app-footer__title {
  font-family: var(--font-heading);
  font-size: 18px;
  line-height: 27px;
  font-weight: 600;
  letter-spacing: 0.04em;
  color: var(--color-cream); /* Foundation/back/Normal #FCEFD4 */
  margin-bottom: 24px;
}

/* Links (style_723ce89f: 18px, Medium 500, color #E0E0E0) */
.app-footer__links {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.app-footer__link {
  font-family: var(--font-sans);
  font-size: 18px;
  line-height: 24px;
  font-weight: 500;
  color: #E0E0E0;
  text-decoration: none;
  transition: color var(--transition-fast);
}

.app-footer__link:hover {
  color: var(--color-cream);
}

/* Address */
.app-footer__address {
  font-style: normal;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.app-footer__contact-text {
  font-family: var(--font-sans);
  font-size: 18px;
  line-height: 24px;
  font-weight: 500;
  color: #E0E0E0;
}

/* Full Width Divider Line (#82:14996) */
.app-footer__divider {
  width: 100%;
  height: 1px;
  background-color: rgba(255, 255, 255, 0.2);
  margin-top: 60px;
  margin-bottom: 24px;
}

/* Copyright (#82:14998, 12px, text/r12, color #F0F0F0) */
.app-footer__copyright {
  font-family: var(--font-sans);
  font-size: 12px;
  line-height: 18px;
  font-weight: 400;
  color: #F0F0F0;
}
</style>
