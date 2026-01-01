<template>
  <div class="mb-4">
    <label v-if="label" :for="id" class="block text-sm font-medium text-slate-700 mb-1">
      {{ label }}
      <span v-if="required" class="text-rose-600">*</span>
    </label>
    <div class="relative">
      <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h2a2 2 0 012 2v1a2 2 0 01-2 2H5v1c0 6.075 4.925 11 11 11h1v-2a2 2 0 012-2h1a2 2 0 012 2v2a2 2 0 01-2 2h-1C9.716 22 2 14.284 2 5V4a2 2 0 012-2z" />
        </svg>
      </span>
      <input
        :id="id"
        :name="name"
        type="tel"
        inputmode="tel"
        :placeholder="placeholder || '(###) ###-####'"
        :disabled="disabled"
        :aria-invalid="Boolean(error)"
        :aria-describedby="describedById"
        :class="inputClass"
        :value="displayValue"
        @input="onInput"
      />
    </div>
    <div class="mt-1 flex items-center justify-between">
      <p v-if="hint && !error" :id="describedById" class="text-xs text-slate-500">{{ hint }}</p>
      <p v-if="error" :id="describedById" class="text-xs text-rose-600">{{ error }}</p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: { type: String, default: '' },
  label: { type: String, default: 'Teléfono' },
  name: { type: String, default: '' },
  id: { type: String, default: () => `phone-${Math.random().toString(36).slice(2)}` },
  required: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
  error: { type: String, default: '' },
  hint: { type: String, default: '' },
  size: { type: String, default: 'md' },
  digits: { type: Number, default: 10 },
  autovalidate: { type: Boolean, default: true }
})

defineEmits(['update:modelValue'])

const describedById = computed(() => props.id + '-desc')

const inputClass = computed(() => {
  const base = 'block w-full rounded-md border bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:outline-none focus:ring-2 transition-colors duration-200 disabled:bg-slate-100 disabled:text-slate-500 pl-9'
  const sizes = { sm: 'pr-9 py-1.5 text-sm', md: 'pr-9 py-2 text-base', lg: 'pr-10 py-3 text-lg' }
  const borders = props.error ? 'border-rose-500 focus:ring-rose-600' : 'border-slate-300 focus:ring-blue-600'
  return [base, sizes[props.size] || sizes.md, borders].join(' ')
})

const digitsOnly = (s) => s.replace(/\D/g, '')
const formatUS = (d) => {
  const a = d.slice(0, 3)
  const b = d.slice(3, 6)
  const c = d.slice(6, 10)
  if (d.length <= 3) return a
  if (d.length <= 6) return `(${a}) ${b}`
  return `(${a}) ${b}-${c}`
}

const displayValue = computed(() => formatUS(digitsOnly(props.modelValue)))

function onInput(e) {
  const clean = digitsOnly(e.target.value).slice(0, props.digits)
  const formatted = formatUS(clean)
  e.target.value = formatted
  emit('update:modelValue', clean)
}
</script>
