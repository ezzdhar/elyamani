<template>
  <div :class="['input-group', { 'input-group--error': !!error }]">
    <label :for="id" class="input-label">
      {{ label }}
      <span v-if="required" class="input-required" aria-hidden="true">*</span>
    </label>

    <!-- Textarea -->
    <textarea
      v-if="type === 'textarea'"
      :id="id"
      :name="name || id"
      :value="modelValue"
      :placeholder="placeholder"
      :required="required"
      :rows="rows"
      :aria-invalid="!!error"
      :aria-describedby="error ? `${id}-error` : undefined"
      class="input-control input-control--textarea"
      @input="onInput"
      @blur="$emit('blur', $event)"
    ></textarea>

    <!-- Select Dropdown -->
    <div v-else-if="type === 'select'" class="select-wrapper">
      <select
        :id="id"
        :name="name || id"
        :value="modelValue"
        :required="required"
        :aria-invalid="!!error"
        :aria-describedby="error ? `${id}-error` : undefined"
        class="input-control input-control--select"
        @change="onChange"
        @blur="$emit('blur', $event)"
      >
        <option v-if="placeholder" value="" disabled :selected="!modelValue">
          {{ placeholder }}
        </option>
        <option
          v-for="opt in options"
          :key="typeof opt === 'string' ? opt : opt.value"
          :value="typeof opt === 'string' ? opt : opt.value"
        >
          {{ typeof opt === 'string' ? opt : opt.label }}
        </option>
      </select>
      <svg
        class="select-arrow"
        width="16"
        height="16"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
        aria-hidden="true"
      >
        <polyline points="6 9 12 15 18 9"></polyline>
      </svg>
    </div>

    <!-- Standard Text / Email / Tel Input -->
    <input
      v-else
      :id="id"
      :type="type"
      :name="name || id"
      :value="modelValue"
      :placeholder="placeholder"
      :required="required"
      :aria-invalid="!!error"
      :aria-describedby="error ? `${id}-error` : undefined"
      class="input-control"
      @input="onInput"
      @blur="$emit('blur', $event)"
    />

    <span
      v-if="error"
      :id="`${id}-error`"
      class="input-error"
      role="alert"
    >
      {{ error }}
    </span>
  </div>
</template>

<script setup lang="ts">
interface SelectOption {
  label: string
  value: string | number
}

interface Props {
  id: string
  label: string
  modelValue: string | number
  type?: 'text' | 'email' | 'tel' | 'textarea' | 'select'
  name?: string
  placeholder?: string
  required?: boolean
  error?: string
  rows?: number
  options?: Array<string | SelectOption>
}

const props = withDefaults(defineProps<Props>(), {
  type: 'text',
  rows: 4,
  required: false,
  options: () => []
})

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void
  (e: 'blur', event: FocusEvent): void
}>()

function onInput(event: Event) {
  const target = event.target as HTMLInputElement | HTMLTextAreaElement
  emit('update:modelValue', target.value)
}

function onChange(event: Event) {
  const target = event.target as HTMLSelectElement
  emit('update:modelValue', target.value)
}
</script>

<style scoped>
.input-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
  width: 100%;
}

.input-label {
  font-family: var(--font-sans);
  font-size: var(--text-xs);
  font-weight: 500;
  color: var(--color-cream-light);
  letter-spacing: 0.02em;
}

.input-required {
  color: #ff8a80;
  margin-left: 2px;
}

.input-control {
  width: 100%;
  height: 44px;
  padding: 10px 14px;
  background-color: var(--color-gray-input-bg);
  border: 1px solid transparent;
  border-radius: var(--radius-sm);
  font-family: var(--font-sans);
  font-size: var(--text-sm);
  color: var(--color-green-dark);
  box-shadow: var(--shadow-sm);
  transition: all var(--transition-fast);
  outline: none;
}

.input-control::placeholder {
  color: var(--color-gray-placeholder);
}

.input-control:hover {
  background-color: #f7f2ed;
}

.input-control:focus {
  background-color: #ffffff;
  border-color: var(--color-cream);
  box-shadow: 0 0 0 3px rgba(252, 239, 212, 0.4);
}

/* Textarea modifier */
.input-control--textarea {
  height: auto;
  min-height: 110px;
  resize: vertical;
  line-height: 1.5;
}

/* Select wrapper */
.select-wrapper {
  position: relative;
  width: 100%;
}

.input-control--select {
  appearance: none;
  cursor: pointer;
  padding-right: 36px;
}

.select-arrow {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  pointer-events: none;
  color: var(--color-card-darker);
}

:global([dir="rtl"] .input-control--select) {
  padding-right: 14px;
  padding-left: 36px;
}

:global([dir="rtl"] .select-arrow) {
  right: auto;
  left: 12px;
}

/* Error State */
.input-group--error .input-control {
  border-color: #ff8a80;
  background-color: #fff9f9;
}

.input-group--error .input-control:focus {
  box-shadow: 0 0 0 3px rgba(255, 138, 128, 0.3);
}

.input-error {
  font-size: var(--text-xs);
  color: #ff8a80;
  font-weight: 500;
  margin-top: 2px;
}
</style>
