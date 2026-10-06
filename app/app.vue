<template>
  <div class="app-layout">
    <!-- Whole-website luxury brand preloader on load/refresh -->
    <AppPreloader />

    <NuxtRouteAnnouncer />
    
    <!-- Accessibility Skip Link -->
    <a href="#main-content" class="skip-link">
      Skip to main content
    </a>

    <!-- Global Header -->
    <AppHeader />

    <!-- Main Content -->
    <main id="main-content" role="main">
      <HeroSection />
      <AboutSection />
      <ProcessSection />
      <ServicesSection />
      <ContactSection />
    </main>

    <!-- Global Footer -->
    <AppFooter />
  </div>
</template>

<script setup lang="ts">
import { useHead, useSeoMeta, useRuntimeConfig } from '#imports'
import { useScrollReveal } from '~/composables/useScrollReveal'
import AppPreloader from '~/components/ui/AppPreloader.vue'
import AppHeader from '~/components/layout/AppHeader.vue'
import AppFooter from '~/components/layout/AppFooter.vue'
import HeroSection from '~/components/sections/HeroSection.vue'
import AboutSection from '~/components/sections/AboutSection.vue'
import ProcessSection from '~/components/sections/ProcessSection.vue'
import ServicesSection from '~/components/sections/ServicesSection.vue'
import ContactSection from '~/components/sections/ContactSection.vue'

// Initialize smooth scroll reveal animations
useScrollReveal()

const config = useRuntimeConfig()
const base = config.app.baseURL || '/'
const withBase = (path: string) => `${base.replace(/\/$/, '')}/${path.replace(/^\//, '')}`

// Enhanced SEO Metadata
useSeoMeta({
  title: 'ELYMANI | Architecture, Interior Design & Execution',
  ogTitle: 'ELYMANI | Architecture, Interior Design & Execution',
  description: 'ELYMANI brings architecture, interior design, and turnkey execution together under one integrated approach in New Damietta, Egypt.',
  ogDescription: 'Thoughtful spaces through integrated architecture, interior design, and execution. Turnkey solutions under one roof.',
  ogImage: withBase('images/about-main.png'),
  twitterCard: 'summary_large_image',
  twitterTitle: 'ELYMANI | Architecture, Interior Design & Execution',
  twitterDescription: 'Thoughtful spaces through integrated architecture, interior design, and execution.',
  twitterImage: withBase('images/about-main.png')
})

// Structured JSON-LD Data for SEO
useHead({
  link: [
    { rel: 'icon', type: 'image/x-icon', href: withBase('favicon.ico') },
    { rel: 'shortcut icon', href: withBase('favicon.ico') },
    { rel: 'icon', type: 'image/png', sizes: '32x32', href: withBase('favicon-32x32.png') },
    { rel: 'apple-touch-icon', href: withBase('apple-touch-icon.png') }
  ],
  script: [
    {
      type: 'application/ld+json',
      innerHTML: JSON.stringify({
        '@context': 'https://schema.org',
        '@type': 'HomeAndConstructionBusiness',
        name: 'ELYMANI',
        alternateName: 'ELYMANI Architecture & Execution',
        description: 'Comprehensive architectural design, interior design, and turnkey construction execution under one accountable team.',
        image: 'https://elyamani.com/images/about-main.png',
        url: 'https://elyamani.com',
        telephone: '+20100000000',
        email: 'info@elyamani.com',
        address: {
          '@type': 'PostalAddress',
          addressLocality: 'New Damietta',
          addressRegion: 'Damietta',
          addressCountry: 'EG'
        },
        openingHoursSpecification: [
          {
            '@type': 'OpeningHoursSpecification',
            dayOfWeek: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'],
            opens: '09:00',
            closes: '18:00'
          }
        ],
        sameAs: [
          'https://tr.ee/3czF4jiPUJ',
          'https://tr.ee/XwWe2OLg9l',
          'https://tr.ee/Z8tGBFznnT',
          'https://tr.ee/6FElYLbL6o'
        ],
        hasOfferCatalog: {
          '@type': 'OfferCatalog',
          name: 'Architectural & Interior Services',
          itemListElement: [
            {
              '@type': 'Offer',
              itemOffered: {
                '@type': 'Service',
                name: 'Architectural Design'
              }
            },
            {
              '@type': 'Offer',
              itemOffered: {
                '@type': 'Service',
                name: 'Interior Design'
              }
            },
            {
              '@type': 'Offer',
              itemOffered: {
                '@type': 'Service',
                name: 'Execution and Finishing'
              }
            },
            {
              '@type': 'Offer',
              itemOffered: {
                '@type': 'Service',
                name: 'Project Management'
              }
            }
          ]
        }
      })
    }
  ]
})
</script>

<style scoped>
.app-layout {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  position: relative;
}

#main-content {
  flex: 1;
}
</style>
