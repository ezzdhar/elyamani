<template>
  <button
    type="button"
    class="lang-toggle-btn"
    :aria-label="isRtl ? 'Switch language to English' : 'Switch language to Arabic'"
    :title="isRtl ? 'Switch to English' : 'التبديل إلى العربية'"
    @click="toggleLocale"
  >
    <svg
      class="lang-toggle-btn__icon"
      width="15"
      height="15"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      stroke-width="2"
      stroke-linecap="round"
      stroke-linejoin="round"
      aria-hidden="true"
    >
      <circle cx="12" cy="12" r="10"></circle>
      <line x1="2" y1="12" x2="22" y2="12"></line>
      <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
    </svg>

    <span class="lang-toggle-btn__labels">
      <span :class="['lang-pill', { 'is-active': !isRtl }]">EN</span>
      <span class="lang-divider" aria-hidden="true">|</span>
      <span :class="['lang-pill', 'lang-pill--ar', { 'is-active': isRtl }]">عربي</span>
    </span>
  </button>
</template>

<script setup lang="ts">
import { useI18n } from '~/composables/useI18n'

const { isRtl, toggleLocale } = useI18n()
</script>

<style scoped>
.lang-toggle-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  height: 40px;
  padding: 0 14px;
  background-color: rgba(250, 248, 246, 0.05);
  border: 1px solid rgba(252, 239, 212, 0.25);
  border-radius: var(--radius-xs);
  color: var(--color-sec-light);
  cursor: pointer;
  transition: all var(--transition-fast);
  user-select: none;
  font-family: var(--font-sans);
}

.lang-toggle-btn:hover {
  background-color: rgba(252, 239, 212, 0.12);
  border-color: var(--color-cream);
  color: var(--color-cream);
}

.lang-toggle-btn__icon {
  color: var(--color-cream);
  flex-shrink: 0;
  opacity: 0.9;
  transition: transform var(--transition-base);
}

.lang-toggle-btn:hover .lang-toggle-btn__icon {
  transform: rotate(20deg);
}

.lang-toggle-btn__labels {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  font-weight: 600;
  letter-spacing: 0.03em;
  line-height: 1;
}

.lang-pill {
  color: #BDB39F;
  transition: color var(--transition-fast);
}

.lang-pill--ar {
  font-family: 'Cairo', var(--font-sans);
  font-size: 13px;
  font-weight: 600;
}

.lang-pill.is-active {
  color: var(--color-cream);
  font-weight: 700;
}

.lang-divider {
  color: rgba(252, 239, 212, 0.3);
  font-size: 11px;
}

@media (max-width: 480px) {
  .lang-toggle-btn {
    height: 38px;
    padding: 0 10px;
    gap: 6px;
  }
  .lang-toggle-btn__labels {
    gap: 4px;
    font-size: 12px;
  }
}
</style>
