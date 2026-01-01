<template>
  <span v-if="show" class="inline-flex items-center" role="status" :aria-label="ariaLabel || 'Cargando'">
    <svg :class="svgClasses" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
      <path class="opacity-75" d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="4" stroke-linecap="round" />
    </svg>
    <span v-if="srText" class="sr-only">{{ srText }}</span>
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  show: { type: Boolean, default: true },
  size: { type: String, default: 'sm' }, // xs | sm
  colorClass: { type: String, default: 'text-white' },
  ariaLabel: { type: String, default: '' },
  srText: { type: String, default: '' }
})

const svgClasses = computed(() => {
  const sizes = {
    xs: 'h-4 w-4',
    sm: 'h-5 w-5'
  }
  return ['animate-spin', sizes[props.size] || sizes.sm, props.colorClass].join(' ')
})
</script>
