const baseURL = process.env.NUXT_APP_BASE_URL || (process.env.GITHUB_ACTIONS ? '/elyamani/' : '/')
const cleanBase = baseURL.endsWith('/') ? baseURL : `${baseURL}/`

// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },

  css: ['~/assets/css/main.css'],

  components: [
    {
      path: '~/components',
      pathPrefix: false
    }
  ],

  app: {
    baseURL: cleanBase,
    head: {
      htmlAttrs: {
        lang: 'en',
        dir: 'ltr'
      },
      title: 'ELYMANI | Architecture, Interior Design & Execution',
      meta: [
        { charset: 'utf-8' },
        { name: 'viewport', content: 'width=device-width, initial-scale=1' },
        {
          name: 'description',
          content: 'ELYMANI brings architecture, interior design, and turnkey execution together under one integrated approach in New Damietta, Egypt. From concept to delivery, one accountable team.'
        },
        { name: 'theme-color', content: '#143135' },
        { name: 'robots', content: 'index, follow' },
        { property: 'og:site_name', content: 'ELYMANI' },
        { property: 'og:type', content: 'website' },
        { property: 'og:title', content: 'ELYMANI | Architecture, Interior Design & Execution' },
        {
          property: 'og:description',
          content: 'Thoughtful spaces through integrated architecture, interior design, and execution. Turnkey solutions under one roof.'
        },
        { property: 'og:image', content: `${cleanBase}images/about-main.png` },
        { name: 'twitter:card', content: 'summary_large_image' },
        { name: 'twitter:title', content: 'ELYMANI | Architecture, Interior Design & Execution' },
        {
          name: 'twitter:description',
          content: 'Thoughtful spaces through integrated architecture, interior design, and execution.'
        },
        { name: 'twitter:image', content: `${cleanBase}images/about-main.png` }
      ],
      link: [
        { rel: 'icon', type: 'image/x-icon', href: `${cleanBase}favicon.ico` },
        { rel: 'shortcut icon', href: `${cleanBase}favicon.ico` },
        { rel: 'icon', type: 'image/png', sizes: '32x32', href: `${cleanBase}favicon-32x32.png` },
        { rel: 'apple-touch-icon', href: `${cleanBase}apple-touch-icon.png` },
        { rel: 'preconnect', href: 'https://fonts.googleapis.com' },
        { rel: 'preconnect', href: 'https://fonts.gstatic.com', crossorigin: '' },
        {
          rel: 'stylesheet',
          href: 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap'
        }
      ]
    }
  }
})
