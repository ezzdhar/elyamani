<template>
  <section id="contact" class="contact-section" aria-label="Contact Us">
    <div class="container contact-section__container">
      <div class="contact-section__grid">
        <!-- Left Column: Info & Architectural Polygon -->
        <div class="contact-section__info">
          <SectionBadge color="terracotta">{{ t.contact.badge }}</SectionBadge>

          <h2 class="contact-section__title">
            <span>{{ t.contact.titleLine1 }}</span>
            <span>{{ t.contact.titleLine2 }}</span>
            <span v-if="t.contact.titleLine3">{{ t.contact.titleLine3 }}</span>
          </h2>

          <p class="contact-section__body">
            {{ t.contact.body }}
          </p>

          <!-- Exact Triangular Marble Polygon Composition from Figma (#80:241) -->
          <div class="contact-section__polygon-wrap" aria-hidden="true">
            <picture>
              <source srcset="/images/contact-polygon-complete.webp" type="image/webp" />
              <img
                src="/images/contact-polygon-complete.png"
                alt=""
                class="polygon-graphic-img"
                loading="lazy"
                width="600"
                height="550"
              />
            </picture>
          </div>
        </div>

        <!-- Right Column: Contact Card (#82:14963, 531px width, 40px 80px padding in Figma) -->
        <div class="contact-section__form-wrap">
          <div class="contact-card">
            <!-- Success Confirmation State -->
            <div
              v-if="isSubmitted"
              class="contact-card__success"
              role="status"
              aria-live="polite"
            >
              <div class="success-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                  <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
              </div>
              <h3 class="success-title">Thank You!</h3>
              <p class="success-desc">
                Your project inquiry has been received. Our architecture and execution team will review your details and contact you shortly.
              </p>
              <AppButton
                variant="primary"
                size="sm"
                :show-arrow="false"
                @click="resetForm"
              >
                Send Another Inquiry
              </AppButton>
            </div>

            <!-- Form -->
            <form
              v-else
              class="contact-form"
              novalidate
              @submit.prevent="handleSubmit"
            >
              <!-- Name Row (#80:14954, gap 24px) -->
              <div class="form-row">
                <div class="form-field">
                  <label for="firstName" class="field-label">{{ t.contact.form.firstName }}</label>
                  <input
                    id="firstName"
                    v-model="form.firstName"
                    type="text"
                    :placeholder="t.contact.form.firstNamePlaceholder"
                    class="field-input"
                    :aria-invalid="!!errors.firstName"
                    @blur="validateField('firstName')"
                  />
                  <span v-if="errors.firstName" class="field-error">{{ errors.firstName }}</span>
                </div>

                <div class="form-field">
                  <label for="lastName" class="field-label">{{ t.contact.form.lastName }}</label>
                  <input
                    id="lastName"
                    v-model="form.lastName"
                    type="text"
                    :placeholder="t.contact.form.lastNamePlaceholder"
                    class="field-input"
                    :aria-invalid="!!errors.lastName"
                    @blur="validateField('lastName')"
                  />
                  <span v-if="errors.lastName" class="field-error">{{ errors.lastName }}</span>
                </div>
              </div>

              <!-- Phone (#80:14878) -->
              <div class="form-field">
                <label for="phone" class="field-label">{{ t.contact.form.phone }}</label>
                <input
                  id="phone"
                  v-model="form.phone"
                  type="tel"
                  :placeholder="t.contact.form.phonePlaceholder"
                  class="field-input"
                  :aria-invalid="!!errors.phone"
                  @blur="validateField('phone')"
                />
                <span v-if="errors.phone" class="field-error">{{ errors.phone }}</span>
              </div>

              <!-- Email (#80:14890) -->
              <div class="form-field">
                <label for="email" class="field-label">{{ t.contact.form.email }}</label>
                <input
                  id="email"
                  v-model="form.email"
                  type="email"
                  :placeholder="t.contact.form.emailPlaceholder"
                  class="field-input"
                  :aria-invalid="!!errors.email"
                  @blur="validateField('email')"
                />
                <span v-if="errors.email" class="field-error">{{ errors.email }}</span>
              </div>

              <!-- How did you hear about us? (#80:14955, height 65px in Figma) -->
              <div class="form-field">
                <label for="referral" class="field-label">{{ t.contact.form.referral }}</label>
                <div class="select-box">
                  <select
                    id="referral"
                    v-model="form.referral"
                    class="field-input field-input--select"
                  >
                    <option value="Facebook , Instagram">{{ t.contact.form.referralOption1 }}</option>
                    <option value="Referral / Recommendation">{{ t.contact.form.referralOption2 }}</option>
                    <option value="Google Search">{{ t.contact.form.referralOption3 }}</option>
                    <option value="Previous Client">{{ t.contact.form.referralOption4 }}</option>
                    <option value="Other">{{ t.contact.form.referralOption5 }}</option>
                  </select>
                  <svg class="select-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="6 9 12 15 18 9"></polyline>
                  </svg>
                </div>
              </div>

              <!-- Project details (#82:14957, height 109px in Figma) -->
              <div class="form-field">
                <label for="projectDetails" class="field-label">{{ t.contact.form.projectDetails }}</label>
                <textarea
                  id="projectDetails"
                  v-model="form.projectDetails"
                  :placeholder="t.contact.form.projectDetailsPlaceholder"
                  class="field-input field-input--textarea"
                  :aria-invalid="!!errors.projectDetails"
                  @blur="validateField('projectDetails')"
                ></textarea>
                <span v-if="errors.projectDetails" class="field-error">{{ errors.projectDetails }}</span>
              </div>

              <!-- Submit Button (#80:14951, height 56px, radius 3px in Figma) -->
              <div class="form-submit">
                <AppButton
                  type="submit"
                  variant="primary"
                  size="md"
                  :show-arrow="false"
                  :loading="isLoading"
                  style="width: 100%; height: 56px; border-radius: 3px;"
                  aria-label="Submit project consultation inquiry"
                >
                  {{ t.contact.form.submit }}
                </AppButton>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { reactive, ref } from 'vue'
import SectionBadge from '~/components/ui/SectionBadge.vue'
import AppButton from '~/components/ui/AppButton.vue'
import { useI18n } from '~/composables/useI18n'

const { t, isRtl } = useI18n()

interface FormData {
  firstName: string
  lastName: string
  phone: string
  email: string
  referral: string
  projectDetails: string
}

const form = reactive<FormData>({
  firstName: '',
  lastName: '',
  phone: '',
  email: '',
  referral: 'Facebook , Instagram',
  projectDetails: ''
})

const errors = reactive<Record<string, string>>({
  firstName: '',
  lastName: '',
  phone: '',
  email: '',
  projectDetails: ''
})

const isLoading = ref(false)
const isSubmitted = ref(false)

function validateField(field: keyof FormData): boolean {
  errors[field] = ''

  if (field === 'firstName' && !form.firstName.trim()) {
    errors.firstName = 'First name is required.'
    return false
  }

  if (field === 'lastName' && !form.lastName.trim()) {
    errors.lastName = 'Last name is required.'
    return false
  }

  if (field === 'phone') {
    const raw = form.phone.trim()
    if (!raw) {
      errors.phone = 'Phone number is required.'
      return false
    }
    const cleanPhone = raw.replace(/[\s\-\(\)\.]/g, '')
    const egyptianPhoneRegex = /^(?:\+20|0020|20)?0?1[0125]\d{8}$/
    if (!egyptianPhoneRegex.test(cleanPhone)) {
      errors.phone = 'Please enter a valid Egyptian phone number (e.g. 010 807 478 32).'
      return false
    }
  }

  if (field === 'email') {
    if (!form.email.trim()) {
      errors.email = 'Email address is required.'
      return false
    }
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    if (!emailRegex.test(form.email.trim())) {
      errors.email = 'Please provide a valid email address.'
      return false
    }
  }

  if (field === 'projectDetails' && !form.projectDetails.trim()) {
    errors.projectDetails = 'Project details are required.'
    return false
  }

  return true
}

function validateAll(): boolean {
  const f1 = validateField('firstName')
  const f2 = validateField('lastName')
  const f3 = validateField('phone')
  const f4 = validateField('email')
  const f5 = validateField('projectDetails')
  return f1 && f2 && f3 && f4 && f5
}

async function handleSubmit() {
  if (!validateAll()) return

  isLoading.value = true
  await new Promise((resolve) => setTimeout(resolve, 800))
  isLoading.value = false
  isSubmitted.value = true
}

function resetForm() {
  form.firstName = ''
  form.lastName = ''
  form.phone = ''
  form.email = ''
  form.referral = 'Facebook , Instagram'
  form.projectDetails = ''
  isSubmitted.value = false
}
</script>

<style scoped>
.contact-section {
  position: relative;
  background-color: var(--color-cream-active); /* #FEFAF2 */
  color: var(--color-green-primary);
  padding: 120px 0 100px;
  overflow: hidden;
}

.contact-section__container {
  position: relative;
  z-index: 2;
}

.contact-section__grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 60px;
  align-items: flex-start;
}

@media (min-width: 1024px) {
  .contact-section__grid {
    grid-template-columns: 612px 531px;
    justify-content: space-between;
    gap: 40px;
  }
}

/* Info Column (#77:223, width: 612px in Figma) */
.contact-section__info {
  display: flex;
  flex-direction: column;
  gap: 24px;
  max-width: 612px;
}

.contact-section__title {
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
  .contact-section__title {
    font-size: 48px;
    line-height: 60px;
  }
}

.contact-section__body {
  font-family: var(--font-sans);
  font-size: 1.125rem;
  line-height: 1.55;
  font-weight: 500;
  color: var(--color-green-primary);
}

@media (min-width: 768px) {
  .contact-section__body {
    font-size: 24px;
    line-height: 36px;
  }
}

/* Polygon Graphic (#80:241) */
.contact-section__polygon-wrap {
  position: relative;
  width: 100%;
  max-width: 600px;
  margin-top: 10px;
  margin-left: -40px;
}

.polygon-graphic-img {
  width: 100%;
  height: auto;
  object-fit: contain;
}

/* Contact Card (#82:14963, 531px width, padding 40px 80px) */
.contact-section__form-wrap {
  display: flex;
  justify-content: center;
}

.contact-card {
  width: 100%;
  max-width: 531px;
  background-color: #5E5449; /* Foundation /sec back/Dark :active */
  border-radius: 6px;
  padding: 40px 32px;
  box-shadow: 0 16px 40px rgba(0, 0, 0, 0.2);
}

@media (min-width: 640px) {
  .contact-card {
    padding: 40px 60px;
  }
}

@media (min-width: 1100px) {
  .contact-card {
    padding: 40px 50px;
  }
}

.contact-form {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr;
  gap: 20px;
}

@media (min-width: 520px) {
  .form-row {
    grid-template-columns: repeat(2, 1fr);
  }
}

.form-field {
  display: flex;
  flex-direction: column;
  gap: 8px;
  width: 100%;
}

.field-label {
  font-family: var(--font-sans);
  font-size: 12px;
  line-height: 18px;
  font-weight: 400;
  color: #FFFDFB; /* Foundation /back/Light */
}

.field-input {
  width: 100%;
  height: 42px;
  padding: 8px 14px;
  background-color: #F1EAE2; /* Foundation /sec back/Light :active */
  border: 1px solid transparent;
  border-radius: 6px;
  font-family: var(--font-sans);
  font-size: 16px;
  line-height: 24px;
  font-weight: 400;
  color: #143135;
  box-shadow: 0px 1px 2px 0px rgba(0, 0, 0, 0.05);
  outline: none;
  transition: all var(--transition-fast);
}

.field-input::placeholder {
  color: #978F7F; /* Foundation /back/Dark :hover */
}

.field-input:focus {
  background-color: #ffffff;
  border-color: var(--color-cream);
  box-shadow: 0 0 0 3px rgba(252, 239, 212, 0.35);
}

/* Custom Select (#80:14916, height 65px in Figma) */
.select-box {
  position: relative;
  width: 100%;
}

.field-input--select {
  height: 65px;
  appearance: none;
  cursor: pointer;
  padding-right: 36px;
  color: #828282; /* Gray 3 */
}

.select-arrow {
  position: absolute;
  right: 14px;
  top: 50%;
  transform: translateY(-50%);
  pointer-events: none;
  color: #494139;
}

/* Textarea (#82:14959, height 109px in Figma) */
.field-input--textarea {
  height: 109px;
  resize: vertical;
  color: #828282; /* Gray 3 */
}

.field-error {
  font-size: 12px;
  color: #ffb4ab;
  font-weight: 500;
}

.form-submit {
  margin-top: 16px;
}

/* Success */
.contact-card__success {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: 16px;
  padding: 32px 16px;
  color: var(--color-sec-light);
}

.success-icon {
  width: 64px;
  height: 64px;
  border-radius: var(--radius-full);
  background-color: rgba(252, 239, 212, 0.15);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--color-cream);
}

.success-title {
  font-family: var(--font-heading);
  font-size: 1.75rem;
  color: var(--color-cream);
}

.success-desc {
  font-size: 1rem;
  line-height: 1.6;
  color: var(--color-sec-light);
  opacity: 0.9;
}

/* RTL Adjustments */
:global([dir="rtl"] .contact-section__polygon-wrap) {
  margin-left: 0;
  margin-right: -40px;
}

:global([dir="rtl"] .field-input--select) {
  padding-right: 14px;
  padding-left: 36px;
}

:global([dir="rtl"] .select-arrow) {
  right: auto;
  left: 14px;
}
</style>
