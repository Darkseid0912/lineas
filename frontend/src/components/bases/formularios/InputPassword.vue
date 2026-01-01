<template>
  <div class="mb-4">
    <label v-if="label" :for="id" class="block text-sm font-medium text-slate-700 mb-1">
      {{ label }}
      <span v-if="required" class="text-rose-600">*</span>
    </label>
    <div class="relative">
      <input
        :id="id"
        :name="name"
        :type="visible ? 'text' : 'password'"
        :placeholder="placeholder"
        :disabled="disabled"
        :maxlength="maxlength"
        :aria-invalid="Boolean(error)"
        :aria-describedby="describedById"
        :class="inputClass"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
      />
      <button type="button" class="absolute inset-y-0 right-0 px-3 text-slate-400 hover:text-slate-600" @click="visible = !visible" :aria-label="visible ? 'Ocultar contraseña' : 'Mostrar contraseña'">
        <svg v-if="visible" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
        </svg>
        <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.964 9.964 0 012.366-3.568M6.323 6.323A9.965 9.965 0 0112 5c4.477 0 8.268 2.943 9.542 7-.56 1.784-1.65 3.348-3.06 4.56M15 12a3 3 0 00-3-3m0 0a3 3 0 013 3m0 0a3 3 0 01-3 3m0 0a3 3 0 01-3-3" />
        </svg>
      </button>
    </div>
    <div class="mt-1 flex items-center justify-between">
      <p v-if="hint && !error" :id="describedById" class="text-xs text-slate-500">{{ hint }}</p>
      <p v-if="error" :id="describedById" class="text-xs text-rose-600">{{ error }}</p>
      <p v-if="showCount && maxlength" class="text-xs text-slate-400 ml-auto">{{ (modelValue || '').length }}/{{ maxlength }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  modelValue: { type: String, default: '' },
  label: { type: String, default: 'Contraseña' },
  name: { type: String, default: '' },
  id: { type: String, default: () => `password-${Math.random().toString(36).slice(2)}` },
  placeholder: { type: String, default: '' },
  required: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
  error: { type: String, default: '' },
  hint: { type: String, default: '' },
  size: { type: String, default: 'md' },
  maxlength: { type: Number },
  showCount: { type: Boolean, default: false }
})

defineEmits(['update:modelValue'])

const visible = ref(false)
const describedById = computed(() => props.id + '-desc')

const inputClass = computed(() => {
  const base = 'block w-full rounded-md border bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:outline-none focus:ring-2 transition-colors duration-200 disabled:bg-slate-100 disabled:text-slate-500'
  const sizes = {
    sm: 'pl-3 pr-9 py-1.5 text-sm',
    md: 'pl-3 pr-9 py-2 text-base',
    lg: 'pl-3 pr-10 py-3 text-lg'
  }
  const borders = props.error ? 'border-rose-500 focus:ring-rose-600' : 'border-slate-300 focus:ring-blue-600'
  return [base, sizes[props.size] || sizes.md, borders].join(' ')
})
</script>
