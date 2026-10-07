<template>
  <component
    :is="componentTag"
    :to="to"
    :href="href"
    :type="isButton ? type : undefined"
    :disabled="disabled || loading"
    :aria-disabled="disabled || loading"
    :class="[
      'app-button',
      `app-button--${variant}`,
      `app-button--${size}`,
      { 'app-button--disabled': disabled || loading }
    ]"
    v-bind="$attrs"
  >
    <span v-if="loading" class="app-button__spinner" aria-hidden="true"></span>
    <span class="app-button__content">
      <slot />
    </span>
    <svg
      v-if="showArrow && !loading"
      class="app-button__arrow"
      width="18"
      height="18"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      stroke-width="2"
      stroke-linecap="round"
      stroke-linejoin="round"
      aria-hidden="true"
    >
      <line x1="5" y1="12" x2="19" y2="12"></line>
      <polyline points="12 5 19 12 12 19"></polyline>
    </svg>
  </component>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface Props {
  variant?: 'primary' | 'secondary' | 'dark' | 'ghost'
  size?: 'sm' | 'md' | 'lg'
  to?: string
  href?: string
  type?: 'button' | 'submit' | 'reset'
  disabled?: boolean
  loading?: boolean
  showArrow?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'primary',
  size: 'md',
  type: 'button',
  disabled: false,
  loading: false,
  showArrow: false
})

const isButton = computed(() => !props.to && !props.href)
const componentTag = computed(() => {
  if (props.to) return 'NuxtLink'
  if (props.href) return 'a'
  return 'button'
})
</script>

<style scoped>
.app-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  font-family: var(--font-sans);
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  border-radius: var(--radius-xs);
  transition: all var(--transition-fast);
  cursor: pointer;
  text-decoration: none;
  white-space: nowrap;
  user-select: none;
  position: relative;
}

/* Sizes */
.app-button--sm {
  height: 44px;
  padding: 8px 18px;
  font-size: var(--text-xs);
}

.app-button--md {
  height: 56px;
  padding: 10px 24px;
  font-size: var(--text-sm);
}

.app-button--lg {
  height: 60px;
  padding: 14px 32px;
  font-size: var(--text-base);
}

/* Primary (Cream Background, Dark Green text) */
.app-button--primary {
  background-color: var(--color-cream);
  color: var(--color-green-dark);
  border: 1.5px solid var(--color-cream);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
}

.app-button--primary:hover:not(.app-button--disabled) {
  background-color: var(--color-cream-active);
  border-color: var(--color-cream-active);
  color: #000000;
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
}

.app-button--primary:active:not(.app-button--disabled) {
  transform: translateY(0);
}

/* Secondary (Transparent, Cream Border and Text) */
.app-button--secondary {
  background-color: transparent;
  color: var(--color-cream);
  border: 1.5px solid var(--color-cream);
}

.app-button--secondary:hover:not(.app-button--disabled) {
  background-color: rgba(252, 239, 212, 0.12);
  border-color: var(--color-cream-active);
  color: var(--color-cream-active);
  transform: translateY(-2px);
}

.app-button--secondary:active:not(.app-button--disabled) {
  transform: translateY(0);
}

/* Dark variant */
.app-button--dark {
  background-color: var(--color-green-primary);
  color: var(--color-cream);
  border: 1.5px solid var(--color-green-primary);
}

.app-button--dark:hover:not(.app-button--disabled) {
  background-color: var(--color-green-dark);
  border-color: var(--color-green-dark);
  transform: translateY(-2px);
}

/* Ghost variant */
.app-button--ghost {
  background-color: transparent;
  color: var(--color-cream);
  border: 1.5px solid transparent;
}

.app-button--ghost:hover:not(.app-button--disabled) {
  background-color: rgba(255, 255, 255, 0.08);
}

/* Disabled */
.app-button--disabled {
  opacity: 0.6;
  cursor: not-allowed;
  pointer-events: none;
}

/* Arrow transition */
.app-button__arrow {
  transition: transform var(--transition-fast);
}

.app-button:hover:not(.app-button--disabled) .app-button__arrow {
  transform: translateX(3px);
}

:global([dir="rtl"] .app-button__arrow) {
  transform: scaleX(-1);
}

:global([dir="rtl"] .app-button:hover:not(.app-button--disabled) .app-button__arrow) {
  transform: scaleX(-1) translateX(3px);
}

/* Spinner */
.app-button__spinner {
  width: 16px;
  height: 16px;
  border: 2px solid currentColor;
  border-right-color: transparent;
  border-radius: 50%;
  /* animation: spin 0.75s linear infinite; */
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>
