<template>
  <button
    :class="classes"
    :disabled="disabled"
    :type="type"
    :aria-label="ariaLabel || null"
    @click="$emit('click')"
  >
    <span class="inline-flex items-center">
      <svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M20.1498 7.93997L8.27978 19.81C7.21978 20.88 4.04977 21.3699 3.32977 20.6599C2.60977 19.9499 3.11978 16.78 4.17978 15.71L16.0498 3.84C16.5979 3.31801 17.3283 3.03097 18.0851 3.04019C18.842 3.04942 19.5652 3.35418 20.1004 3.88938C20.6356 4.42457 20.9403 5.14781 20.9496 5.90463C20.9588 6.66146 20.6718 7.39189 20.1498 7.93997V7.93997Z" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
    </span>
    <span class="inline-flex items-center">
      <slot>Editar</slot>
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
  const variant = 'bg-amber-500 hover:bg-amber-600 text-white focus-visible:ring-amber-500 shadow-sm'
  return [base, sizes[props.size] || sizes.md, variant].join(' ')
})
</script>
