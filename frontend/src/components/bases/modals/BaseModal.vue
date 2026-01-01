<template>
  <teleport to="body">
    <div v-if="show" class="fixed inset-0 z-[1000]">
      <div class="fixed inset-0 bg-slate-900/50" @click="onBackdropClick" aria-hidden="true"></div>
      <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="w-full max-w-2xl max-h-[85vh] bg-white rounded-xl shadow-xl flex flex-col" role="dialog" aria-modal="true">
          <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between shrink-0">
            <h3 class="text-slate-800 font-semibold">{{ title }}</h3>
            <button class="p-1.5 rounded-md hover:bg-slate-100" @click="emit('close')" aria-label="Cerrar">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M6 6L18 18M6 18L18 6" stroke="#0f172a" stroke-width="1.5" stroke-linecap="round"/>
              </svg>
            </button>
          </div>
          <div class="p-4 overflow-y-auto flex-1">
            <slot />
          </div>
          <div v-if="$slots.footer" class="px-4 py-3 border-t border-slate-200 bg-slate-50 shrink-0">
            <slot name="footer" />
          </div>
        </div>
      </div>
    </div>
  </teleport>
  
</template>

<script setup>
const props = defineProps({
  show: { type: Boolean, default: false },
  title: { type: String, default: '' },
  closeOnBackdrop: { type: Boolean, default: true }
})

const emit = defineEmits(['close'])

function onBackdropClick() {
  if (props.closeOnBackdrop) emit('close')
}
</script>
