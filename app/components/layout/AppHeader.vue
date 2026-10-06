<template>
  <header
    :class="['app-header', { 'app-header--scrolled': isScrolled }]"
    role="banner"
  >
    <div class="container app-header__container">
      <!-- Brand Logo (Symbol + Calligraphy + Wordmark) -->
      <NuxtLink to="#home" class="app-header__brand" aria-label="ELYMANI Home">
        <img
          src="/images/header-logo.png"
          alt="ELYMANI Architecture & Execution"
          class="app-header__logo"
          width="135"
          height="52"
        />
      </NuxtLink>

      <!-- Desktop Right Cluster: Navigation + CTA -->
      <div class="app-header__right">
        <nav class="app-header__nav" aria-label="Main Navigation">
          <ul class="app-header__nav-list" role="list">
            <li v-for="item in navItems" :key="item.href" class="app-header__nav-item">
              <a
                :href="item.href"
                :class="[
                  'app-header__nav-link',
                  { 'app-header__nav-link--active': activeSection === item.id }
                ]"
                :aria-current="activeSection === item.id ? 'true' : undefined"
                @click="onNavClick(item.id)"
              >
                {{ item.label }}
              </a>
            </li>
          </ul>
        </nav>

        <div class="app-header__action">
          <AppButton
            variant="secondary"
            size="sm"
            href="#contact"
            :show-arrow="false"
            aria-label="Design your space - Contact us"
          >
            DESIGN YOUR SPACE
          </AppButton>
        </div>
      </div>

      <!-- Mobile Hamburger Button -->
      <button
        type="button"
        class="app-header__mobile-toggle"
        :aria-expanded="isMobileMenuOpen"
        aria-controls="mobile-navigation"
        aria-label="Toggle Navigation Menu"
        @click="isMobileMenuOpen = !isMobileMenuOpen"
      >
        <span class="hamburger-box">
          <span :class="['hamburger-inner', { 'is-active': isMobileMenuOpen }]"></span>
        </span>
      </button>
    </div>

    <!-- Mobile Drawer -->
    <ClientOnly>
      <Teleport to="body">
        <Transition name="fade">
          <div
            v-if="isMobileMenuOpen"
            class="app-header__mobile-overlay"
            aria-hidden="true"
            @click="isMobileMenuOpen = false"
          ></div>
        </Transition>

        <Transition name="slide">
          <div
            v-if="isMobileMenuOpen"
            id="mobile-navigation"
            class="app-header__mobile-drawer"
            role="dialog"
            aria-modal="true"
            aria-label="Mobile Navigation"
          >
            <div class="app-header__mobile-drawer-header">
              <NuxtLink to="#home" class="app-header__brand" @click="isMobileMenuOpen = false">
                <img
                  src="/images/header-logo.png"
                  alt="ELYMANI"
                  class="app-header__logo"
                  width="120"
                  height="46"
                />
              </NuxtLink>
              <button
                type="button"
                class="app-header__mobile-close"
                aria-label="Close Navigation Menu"
                @click="isMobileMenuOpen = false"
              >
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="18" y1="6" x2="6" y2="18"></line>
                  <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
              </button>
            </div>

            <nav class="app-header__mobile-nav">
              <ul class="app-header__mobile-list" role="list">
                <li v-for="item in navItems" :key="item.href">
                  <a
                    :href="item.href"
                    :class="[
                      'app-header__mobile-link',
                      { 'app-header__mobile-link--active': activeSection === item.id }
                    ]"
                    @click="onMobileNavClick(item.id)"
                  >
                    {{ item.label }}
                  </a>
                </li>
              </ul>
            </nav>

            <div class="app-header__mobile-action">
              <AppButton
                variant="primary"
                size="md"
                href="#contact"
                :show-arrow="false"
                style="width: 100%;"
                @click="isMobileMenuOpen = false"
              >
                DESIGN YOUR SPACE
              </AppButton>
            </div>
          </div>
        </Transition>
      </Teleport>
    </ClientOnly>
  </header>
</template>

<script setup lang="ts">
import { ref, watch, onMounted, onUnmounted } from 'vue'
import AppButton from '~/components/ui/AppButton.vue'

const navItems = [
  { label: 'Home', href: '#home', id: 'home' },
  { label: 'About', href: '#about', id: 'about' },
  { label: 'Process', href: '#process', id: 'process' },
  { label: 'Services', href: '#services', id: 'services' },
  { label: 'Contact', href: '#contact', id: 'contact' }
]

const activeSection = ref('home')
const isScrolled = ref(false)
const isMobileMenuOpen = ref(false)

watch(isMobileMenuOpen, (isOpen) => {
  if (typeof document !== 'undefined') {
    if (isOpen) {
      document.body.style.overflow = 'hidden'
    } else {
      document.body.style.overflow = ''
    }
  }
})

function onNavClick(id: string) {
  activeSection.value = id
}

function onMobileNavClick(id: string) {
  activeSection.value = id
  isMobileMenuOpen.value = false
}

function handleScroll() {
  isScrolled.value = window.scrollY > 40

  const sections = ['home', 'about', 'process', 'services', 'contact']
  const scrollPos = window.scrollY + 220

  for (let i = sections.length - 1; i >= 0; i--) {
    const el = document.getElementById(sections[i])
    if (el && el.offsetTop <= scrollPos) {
      activeSection.value = sections[i]
      break
    }
  }
}

function handleResize() {
  if (window.innerWidth >= 992 && isMobileMenuOpen.value) {
    isMobileMenuOpen.value = false
  }
}

function onKeydown(e: KeyboardEvent) {
  if (e.key === 'Escape' && isMobileMenuOpen.value) {
    isMobileMenuOpen.value = false
  }
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll, { passive: true })
  window.addEventListener('resize', handleResize, { passive: true })
  window.addEventListener('keydown', onKeydown)
  handleScroll()
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
  window.removeEventListener('resize', handleResize)
  window.removeEventListener('keydown', onKeydown)
  if (typeof document !== 'undefined') {
    document.body.style.overflow = ''
  }
})
</script>

<style scoped>
.app-header {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  height: 84px;
  z-index: 1000;
  display: flex;
  align-items: center;
  transition: background-color var(--transition-base), backdrop-filter var(--transition-base), box-shadow var(--transition-base), height var(--transition-base);
  background-color: transparent;
}

.app-header--scrolled {
  height: 72px;
  background-color: rgba(20, 49, 53, 0.96);
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  border-bottom: 1px solid rgba(252, 239, 212, 0.12);
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.35);
}

.app-header__container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 24px;
  width: 100%;
}

/* Brand */
.app-header__brand {
  display: flex;
  align-items: center;
  text-decoration: none;
}

.app-header__logo {
  height: 44px;
  width: auto;
  object-fit: contain;
}

/* Desktop Right Cluster (Nav + CTA) */
.app-header__right {
  display: none;
}

@media (min-width: 992px) {
  .app-header__right {
    display: flex;
    align-items: center;
    gap: clamp(28px, 2.5vw, 40px);
  }
}

.app-header__nav {
  display: block;
}

.app-header__nav-list {
  display: flex;
  align-items: center;
  gap: clamp(20px, 2vw, 32px);
  list-style: none;
}

.app-header__nav-link {
  font-family: var(--font-sans);
  font-size: 1rem; /* ~16px */
  font-weight: 600;
  color: #BDB39F; /* Foundation /back/Dark */
  padding: 4px 0;
  position: relative;
  text-decoration: none;
  transition: color var(--transition-fast);
}

.app-header__nav-link:hover {
  color: var(--color-cream);
}

.app-header__nav-link--active {
  color: #FAF8F6;
  border-bottom: 2px solid var(--color-cream);
  padding-bottom: 2px;
}

/* Action CTA */
.app-header__action {
  display: none;
}

@media (min-width: 992px) {
  .app-header__action {
    display: block;
  }
}

.app-header__action :deep(.app-button) {
  height: 42px;
  padding: 0 18px;
  font-size: 12.5px;
  font-weight: 600;
  letter-spacing: 0.05em;
  border-radius: 3px;
  border: 1.5px solid rgba(250, 248, 246, 0.5);
  color: #FAF8F6;
  background-color: transparent;
}

.app-header__action :deep(.app-button:hover) {
  border-color: #FAF8F6;
  background-color: rgba(250, 248, 246, 0.12);
  color: #FAF8F6;
  transform: none;
}

/* Mobile Toggle */
.app-header__mobile-toggle {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  color: var(--color-cream);
  border-radius: var(--radius-xs);
  cursor: pointer;
}

@media (min-width: 992px) {
  .app-header__mobile-toggle {
    display: none;
  }
}

.hamburger-box {
  width: 24px;
  height: 20px;
  position: relative;
}

.hamburger-inner,
.hamburger-inner::before,
.hamburger-inner::after {
  width: 24px;
  height: 2px;
  background-color: var(--color-cream);
  position: absolute;
  transition: transform var(--transition-fast), top var(--transition-fast);
}

.hamburger-inner {
  top: 9px;
}

.hamburger-inner::before {
  content: '';
  top: -8px;
}

.hamburger-inner::after {
  content: '';
  top: 8px;
}

.hamburger-inner.is-active {
  background-color: transparent;
}

.hamburger-inner.is-active::before {
  top: 0;
  transform: rotate(45deg);
}

.hamburger-inner.is-active::after {
  top: 0;
  transform: rotate(-45deg);
}

/* Mobile Drawer */
.app-header__mobile-overlay {
  position: fixed;
  inset: 0;
  width: 100vw;
  height: 100vh;
  height: 100dvh;
  background: rgba(0, 0, 0, 0.75);
  backdrop-filter: blur(6px);
  -webkit-backdrop-filter: blur(6px);
  z-index: 1001;
}

.app-header__mobile-drawer {
  position: fixed;
  top: 0;
  right: 0;
  bottom: 0;
  width: 320px;
  max-width: 85vw;
  height: 100vh;
  height: 100dvh;
  background-color: var(--color-green-primary);
  border-left: 1px solid rgba(252, 239, 212, 0.15);
  box-shadow: var(--shadow-lg);
  padding: 24px;
  display: flex;
  flex-direction: column;
  z-index: 1002;
  overflow-y: auto;
  -webkit-overflow-scrolling: touch;
}

.app-header__mobile-drawer-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-bottom: 24px;
  border-bottom: 1px solid rgba(252, 239, 212, 0.1);
}

.app-header__mobile-close {
  color: var(--color-cream);
  padding: 8px;
}

.app-header__mobile-nav {
  margin-top: 32px;
  flex: 1;
}

.app-header__mobile-list {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.app-header__mobile-link {
  font-size: var(--text-lg);
  font-weight: 600;
  color: #BDB39F;
  text-decoration: none;
  display: block;
  padding: 8px 0;
}

.app-header__mobile-link:hover,
.app-header__mobile-link--active {
  color: var(--color-cream);
}

.app-header__mobile-action {
  padding-top: 24px;
}

/* Transitions */
.fade-enter-active, .fade-leave-active {
  transition: opacity var(--transition-base);
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.slide-enter-active, .slide-leave-active {
  transition: transform var(--transition-base);
}
.slide-enter-from, .slide-leave-to {
  transform: translateX(100%);
}
</style>
