/**
 * Frontend Contact Form Unit Test Suite
 * Tests:
 * 1. API URL resolution (development vs production vs configured NUXT_PUBLIC_CONTACT_API_URL)
 * 2. Missing API configuration explicit handling
 * 3. Duplicate submission prevention
 * 4. Invalid JSON / Non-JSON response handling (prevents false success on static GitHub Pages)
 * 5. Network failure handling
 * 6. HTTP 200 with success: false (prevents false success)
 * 7. HTTP 200 with success: true (legitimate success confirmation)
 * 8. Form validation (phone regex, email regex, required fields)
 */

import assert from 'node:assert'

let passed = 0
let total = 0

function test(name, fn) {
  total++
  try {
    fn()
    passed++
    console.log(`  [PASS] ${name}`)
  } catch (err) {
    console.error(`  [FAIL] ${name}: ${err.message}`)
  }
}

async function testAsync(name, fn) {
  total++
  try {
    await fn()
    passed++
    console.log(`  [PASS] ${name}`)
  } catch (err) {
    console.error(`  [FAIL] ${name}: ${err.message}`)
  }
}

console.log('\n======================================================')
console.log('   RUNNING ELYAMANI FRONTEND FORM UNIT TEST SUITE     ')
console.log('======================================================\n')

// Simulation of ContactSection.vue logic
function resolveTargetUrl({ configuredApiUrl, isDev }) {
  const trimmed = (configuredApiUrl || '').trim()
  return trimmed || (isDev ? '/api/contact.php' : '')
}

function validateField(field, value) {
  if (field === 'website') return { valid: true }

  if (field === 'firstName') {
    return { valid: Boolean(value && value.trim()), error: 'required' }
  }
  if (field === 'lastName') {
    return { valid: Boolean(value && value.trim()), error: 'required' }
  }
  if (field === 'phone') {
    const raw = (value || '').trim()
    if (!raw) return { valid: false, error: 'required' }
    const cleanPhone = raw.replace(/[\s\-\(\)\.]/g, '')
    const egyptianPhoneRegex = /^(?:\+20|0020|20)?0?1[0125]\d{8}$/
    if (!egyptianPhoneRegex.test(cleanPhone)) return { valid: false, error: 'invalid' }
    return { valid: true }
  }
  if (field === 'email') {
    const raw = (value || '').trim()
    if (!raw) return { valid: false, error: 'required' }
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    if (!emailRegex.test(raw)) return { valid: false, error: 'invalid' }
    return { valid: true }
  }
  if (field === 'projectDetails') {
    return { valid: Boolean(value && value.trim()), error: 'required' }
  }
  return { valid: true }
}

async function createFormSubmitter(options = {}) {
  const {
    configuredApiUrl = '',
    isDev = false,
    mockFetch = null,
    isRtl = true
  } = options

  const state = {
    isLoading: false,
    isSubmitted: false,
    submitError: '',
    fetchCallCount: 0
  }

  const form = {
    firstName: 'أحمد',
    lastName: 'اليماني',
    phone: '01012345678',
    email: 'client@example.com',
    referral: 'Google Search',
    projectDetails: 'مشروع تصميم معماري لفيلا خاصة في دمياط.',
    website: ''
  }

  async function handleSubmit() {
    if (state.isLoading) return
    state.isLoading = true
    state.submitError = ''

    const targetUrl = resolveTargetUrl({ configuredApiUrl, isDev })

    if (!targetUrl) {
      state.isLoading = false
      state.submitError = isRtl
        ? 'خدمة إرسال النماذج غير مهيأة حالياً. يرجى مراسلتنا مباشرة على elyamani.cfd@gmail.com'
        : 'Contact form service is not configured. Please contact us directly at elyamani.cfd@gmail.com'
      return
    }

    try {
      state.fetchCallCount++
      const response = await mockFetch(targetUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          firstName: form.firstName.trim(),
          lastName: form.lastName.trim(),
          phone: form.phone.trim(),
          email: form.email.trim(),
          referral: form.referral,
          projectDetails: form.projectDetails.trim(),
          message: form.projectDetails.trim(),
          website: form.website
        })
      })

      const contentType = response.headers?.get ? (response.headers.get('content-type') || '') : (response.headers?.['content-type'] || '')
      let result = null

      if (contentType.includes('application/json')) {
        try {
          result = await response.json()
        } catch {
          result = null
        }
      }

      if (response.ok && result?.success === true) {
        state.isSubmitted = true
      } else {
        state.submitError = result?.message || (isRtl
          ? 'حدث خطأ أثناء إرسال الرسالة، يرجى المحاولة مرة أخرى أو مراسلتنا مباشرة على elyamani.cfd@gmail.com'
          : 'An error occurred while sending your message. Please try again or email us at elyamani.cfd@gmail.com')
      }
    } catch (error) {
      state.submitError = isRtl
        ? 'تعذر الاتصال بالخادم. يمكنك مراسلتنا مباشرة على elyamani.cfd@gmail.com'
        : 'Unable to reach the server. You can email us directly at elyamani.cfd@gmail.com'
    } finally {
      state.isLoading = false
    }
  }

  return { state, form, handleSubmit }
}

// 1. Correct frontend API URL resolution
test('API URL correctly resolves configured external Hostinger URL', () => {
  const externalUrl = 'https://hostinger-domain.com/api/contact.php'
  const resolved = resolveTargetUrl({ configuredApiUrl: externalUrl, isDev: false })
  assert.strictEqual(resolved, externalUrl)
})

test('API URL falls back to /api/contact.php in local development mode', () => {
  const resolved = resolveTargetUrl({ configuredApiUrl: '', isDev: true })
  assert.strictEqual(resolved, '/api/contact.php')
})

test('API URL returns empty string in production when unconfigured', () => {
  const resolved = resolveTargetUrl({ configuredApiUrl: '', isDev: false })
  assert.strictEqual(resolved, '')
})

// 2. Missing API configuration explicit handling
await testAsync('Unconfigured API URL in production sets explicit error and prevents fetch', async () => {
  const { state, handleSubmit } = await createFormSubmitter({
    configuredApiUrl: '',
    isDev: false,
    mockFetch: async () => { throw new Error('Fetch should not be called!') }
  })

  await handleSubmit()

  assert.strictEqual(state.isSubmitted, false)
  assert.strictEqual(state.isLoading, false)
  assert.strictEqual(state.fetchCallCount, 0)
  assert.match(state.submitError, /خدمة إرسال النماذج غير مهيأة/)
})

// 3. Duplicate submission prevention
await testAsync('Duplicate simultaneous submissions are blocked', async () => {
  let resolveFirst
  const firstPromise = new Promise(res => { resolveFirst = res })

  const { state, handleSubmit } = await createFormSubmitter({
    configuredApiUrl: 'https://api.el-yamani.com/api/contact.php',
    isDev: false,
    mockFetch: async () => {
      await firstPromise
      return {
        ok: true,
        headers: { get: () => 'application/json' },
        json: async () => ({ success: true })
      }
    }
  })

  // Trigger first submit
  const call1 = handleSubmit()
  assert.strictEqual(state.isLoading, true)

  // Trigger second submit while call1 is still running
  const call2 = handleSubmit()
  assert.strictEqual(state.fetchCallCount, 1, 'Second submit should not increment fetch call count')

  resolveFirst()
  await Promise.all([call1, call2])

  assert.strictEqual(state.fetchCallCount, 1, 'Total fetch calls must remain 1')
  assert.strictEqual(state.isSubmitted, true)
})

// 4. Invalid JSON / Non-JSON response handling
await testAsync('Non-JSON response (e.g. raw PHP text or HTML 404) does not show false success', async () => {
  const { state, handleSubmit } = await createFormSubmitter({
    configuredApiUrl: 'https://el-yamani.com/api/contact.php',
    isDev: false,
    mockFetch: async () => ({
      ok: true, // Server returned 200 OK with static PHP source code
      headers: { get: () => 'text/plain' },
      json: async () => { throw new Error('SyntaxError: Unexpected token <') }
    })
  })

  await handleSubmit()

  assert.strictEqual(state.isSubmitted, false, 'Must NOT display success state')
  assert.strictEqual(state.isLoading, false)
  assert.ok(state.submitError.length > 0, 'Must show error message')
})

// 5. Network failure handling
await testAsync('Network failure / CORS error is caught and shows friendly error message', async () => {
  const { state, handleSubmit } = await createFormSubmitter({
    configuredApiUrl: 'https://api.el-yamani.com/api/contact.php',
    isDev: false,
    mockFetch: async () => {
      throw new TypeError('Failed to fetch (CORS or network error)')
    }
  })

  await handleSubmit()

  assert.strictEqual(state.isSubmitted, false)
  assert.strictEqual(state.isLoading, false)
  assert.match(state.submitError, /تعذر الاتصال بالخادم/)
})

// 6. False success prevention when response has success: false
await testAsync('Server returning 200 with success: false does not trigger success state', async () => {
  const { state, handleSubmit } = await createFormSubmitter({
    configuredApiUrl: 'https://api.el-yamani.com/api/contact.php',
    isDev: false,
    mockFetch: async () => ({
      ok: true,
      headers: { get: () => 'application/json' },
      json: async () => ({ success: false, message: 'SMTP sending failed' })
    })
  })

  await handleSubmit()

  assert.strictEqual(state.isSubmitted, false)
  assert.strictEqual(state.submitError, 'SMTP sending failed')
})

// 7. Legitimate success confirmation
await testAsync('Server returning 200 with success: true displays success confirmation', async () => {
  const { state, handleSubmit } = await createFormSubmitter({
    configuredApiUrl: 'https://api.el-yamani.com/api/contact.php',
    isDev: false,
    mockFetch: async () => ({
      ok: true,
      headers: { get: () => 'application/json' },
      json: async () => ({ success: true, message: 'تم استلام استفسارك بنجاح!' })
    })
  })

  await handleSubmit()

  assert.strictEqual(state.isSubmitted, true)
  assert.strictEqual(state.submitError, '')
})

// 8. Field validations
test('Egyptian phone validation accepts valid formats and rejects invalid ones', () => {
  assert.strictEqual(validateField('phone', '01012345678').valid, true)
  assert.strictEqual(validateField('phone', '+201012345678').valid, true)
  assert.strictEqual(validateField('phone', '00201112345678').valid, true)
  assert.strictEqual(validateField('phone', '01512345678').valid, true)
  assert.strictEqual(validateField('phone', '01212345678').valid, true)

  assert.strictEqual(validateField('phone', '01912345678').valid, false) // 019 invalid
  assert.strictEqual(validateField('phone', '12345').valid, false) // too short
  assert.strictEqual(validateField('phone', '').valid, false) // empty
})

test('Email validation correctly identifies valid and invalid addresses', () => {
  assert.strictEqual(validateField('email', 'test@domain.com').valid, true)
  assert.strictEqual(validateField('email', 'user.name+tag@company.co.uk').valid, true)

  assert.strictEqual(validateField('email', 'notanemail').valid, false)
  assert.strictEqual(validateField('email', 'user@').valid, false)
  assert.strictEqual(validateField('email', '@domain.com').valid, false)
  assert.strictEqual(validateField('email', '').valid, false)
})

console.log('\n------------------------------------------------------')
console.log(`SUMMARY: ${passed} / ${total} frontend unit tests passed.`)
console.log('------------------------------------------------------\n')

if (passed !== total) {
  process.exit(1)
}
