<template>
  <button
    :class="classes"
    :disabled="disabled"
    :type="type"
    :aria-label="ariaLabel || null"
    @click="$emit('click')"
  >
    <span class="inline-flex items-center">
      <svg width="20px" height="20px" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg" fill="#ffffff" stroke="#ffffff"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path fill="#ffffff" d="M160 256H96a32 32 0 0 1 0-64h256V95.936a32 32 0 0 1 32-32h256a32 32 0 0 1 32 32V192h256a32 32 0 1 1 0 64h-64v672a32 32 0 0 1-32 32H192a32 32 0 0 1-32-32V256zm448-64v-64H416v64h192zM224 896h576V256H224v640zm192-128a32 32 0 0 1-32-32V416a32 32 0 0 1 64 0v320a32 32 0 0 1-32 32zm192 0a32 32 0 0 1-32-32V416a32 32 0 0 1 64 0v320a32 32 0 0 1-32 32z"></path></g></svg>
    </span>
    <span class="inline-flex items-center">
      <slot>Eliminar</slot>
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
  const variant = 'bg-rose-600 hover:bg-rose-700 text-white focus-visible:ring-rose-600 shadow-sm'
  return [base, sizes[props.size] || sizes.md, variant].join(' ')
})
</script>
