<template>
  <div class="app-layout">
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
import { useHead, useSeoMeta } from '#imports'
import AppHeader from '~/components/layout/AppHeader.vue'
import AppFooter from '~/components/layout/AppFooter.vue'
import HeroSection from '~/components/sections/HeroSection.vue'
import AboutSection from '~/components/sections/AboutSection.vue'
import ProcessSection from '~/components/sections/ProcessSection.vue'
import ServicesSection from '~/components/sections/ServicesSection.vue'
import ContactSection from '~/components/sections/ContactSection.vue'

// Enhanced SEO Metadata
useSeoMeta({
  title: 'ELYMANI | Architecture, Interior Design & Execution',
  ogTitle: 'ELYMANI | Architecture, Interior Design & Execution',
  description: 'ELYMANI brings architecture, interior design, and turnkey execution together under one integrated approach in New Damietta, Egypt.',
  ogDescription: 'Thoughtful spaces through integrated architecture, interior design, and execution. Turnkey solutions under one roof.',
  ogImage: '/images/about-main.png',
  twitterCard: 'summary_large_image',
  twitterTitle: 'ELYMANI | Architecture, Interior Design & Execution',
  twitterDescription: 'Thoughtful spaces through integrated architecture, interior design, and execution.',
  twitterImage: '/images/about-main.png'
})

// Structured JSON-LD Data for SEO
useHead({
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
          'https://facebook.com',
          'https://instagram.com',
          'https://tiktok.com'
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
