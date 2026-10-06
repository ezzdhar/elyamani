<template>
  <div :class="['section-badge', `section-badge--${color}`]">
    <span v-if="withDot" class="section-badge__dot" aria-hidden="true"></span>
    <span class="section-badge__text">
      <slot />
    </span>
  </div>
</template>

<script setup lang="ts">
interface Props {
  color?: 'terracotta' | 'cream' | 'green' | 'light'
  withDot?: boolean
}

withDefaults(defineProps<Props>(), {
  color: 'terracotta',
  withDot: false
})
</script>

<style scoped>
.section-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-family: var(--font-sans);
  font-size: var(--text-xs);
  font-weight: 600;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  margin-bottom: 4px;
}

.section-badge__dot {
  width: 6px;
  height: 6px;
  border-radius: var(--radius-full);
  background-color: currentColor;
}

.section-badge--terracotta {
  color: var(--color-terracotta);
}

.section-badge--cream {
  color: var(--color-cream);
}

.section-badge--green {
  color: var(--color-green-hover);
}

.section-badge--light {
  color: var(--color-sec-light);
}
</style>
