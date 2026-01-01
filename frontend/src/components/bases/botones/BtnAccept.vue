<template>
  <button
    :class="classes"
    :disabled="disabled"
    :type="type"
    :aria-label="ariaLabel || null"
    @click="$emit('click')"
  >
    <span class="inline-flex items-center">
      <svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <circle opacity="0.5" cx="12" cy="12" r="10" stroke="#ffffff" stroke-width="1.5"></circle> <path d="M8.5 12.5L10.5 14.5L15.5 9.5" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
    </span>
    <span class="inline-flex items-center">
      <slot>Aceptar</slot>
    </span>
  </button>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  size: { type: String, default: 'md' },
  disabled: { type: Boolean, default: false },
  type: { type: String, default: 'button' },
  ariaLabel: { type: String, default: '' }
})

defineEmits(['click'])

const classes = computed(() => {
  const base = 'inline-flex items-center gap-2 justify-center font-semibold tracking-wide rounded-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed'
  const sizes = {
    sm: 'px-3 py-1.5 text-sm',
    md: 'px-4 py-2 text-base',
    lg: 'px-5 py-3 text-lg'
  }
  const variant = 'bg-emerald-600 hover:bg-emerald-700 text-white focus-visible:ring-emerald-600 shadow-sm'
  return [base, sizes[props.size] || sizes.md, variant].join(' ')
})
</script>
