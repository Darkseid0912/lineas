<template>
  <transition name="fade" appear>
    <div
      v-if="show"
      class="fixed inset-0 flex items-center justify-center bg-black/60 backdrop-blur-sm"
      :style="{ zIndex: zIndex }"
      aria-live="polite"
      aria-busy="true"
      role="alert"
    >
      <div class="flex flex-col items-center gap-4 select-none">
        <svg class="animate-spin text-white" :class="spinnerSizeClass" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
          <path class="opacity-75" d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="4" stroke-linecap="round" />
        </svg>
        <p v-if="message" class="text-white text-lg font-medium">{{ message }}</p>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { onMounted, onBeforeUnmount, watch, computed } from 'vue'

const props = defineProps({
  show: { type: Boolean, default: false },
  message: { type: String, default: 'Cargando…' },
  zIndex: { type: Number, default: 50 },
  size: { type: String, default: 'xl' } // md | lg | xl
})

const toggleScrollLock = (lock) => {
  const el = document?.documentElement || document?.body
  if (!el) return
  if (lock) el.classList.add('overflow-hidden')
  else el.classList.remove('overflow-hidden')
}

onMounted(() => {
  if (props.show) toggleScrollLock(true)
})

onBeforeUnmount(() => {
  toggleScrollLock(false)
})

watch(
  () => props.show,
  (val) => toggleScrollLock(val)
)

const spinnerSizeClass = computed(() => {
  const sizes = {
    md: 'h-10 w-10',
    lg: 'h-12 w-12',
    xl: 'h-16 w-16'
  }
  return sizes[props.size] || sizes.xl
})
</script>

<style>
.fade-enter-active, .fade-leave-active { transition: opacity 0.15s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
