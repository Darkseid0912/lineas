<template>
  <div class="mb-4">
    <label v-if="label" :for="id" class="block text-sm font-medium text-slate-700 mb-1">
      {{ label }}
      <span v-if="required" class="text-rose-600">*</span>
    </label>
    <input
      :id="id"
      :name="name"
      type="text"
      inputmode="numeric"
      pattern="^-?\\d*$"
      :placeholder="placeholder"
      :disabled="disabled"
      :aria-invalid="Boolean(error)"
      :aria-describedby="describedById"
      :class="inputClass"
      :value="stringValue"
      @input="onInput"
    />
    <div class="mt-1 flex items-center justify-between">
      <p v-if="hint && !error" :id="describedById" class="text-xs text-slate-500">{{ hint }}</p>
      <p v-if="error" :id="describedById" class="text-xs text-rose-600">{{ error }}</p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: { type: [Number, String], default: '' },
  label: { type: String, default: '' },
  name: { type: String, default: '' },
  id: { type: String, default: () => `integer-${Math.random().toString(36).slice(2)}` },
  placeholder: { type: String, default: '' },
  required: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
  error: { type: String, default: '' },
  hint: { type: String, default: '' },
  size: { type: String, default: 'md' },
  allowNegative: { type: Boolean, default: true }
})

defineEmits(['update:modelValue'])

const describedById = computed(() => props.id + '-desc')
const stringValue = computed(() => props.modelValue?.toString() ?? '')

const inputClass = computed(() => {
  const base = 'block w-full rounded-md border bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:outline-none focus:ring-2 transition-colors duration-200 disabled:bg-slate-100 disabled:text-slate-500'
  const sizes = { sm: 'px-3 py-1.5 text-sm', md: 'px-3 py-2 text-base', lg: 'px-4 py-3 text-lg' }
  const borders = props.error ? 'border-rose-500 focus:ring-rose-600' : 'border-slate-300 focus:ring-blue-600'
  return [base, sizes[props.size] || sizes.md, borders].join(' ')
})

function onInput(e) {
  let val = e.target.value
  // Only digits, optional leading '-'
  const re = props.allowNegative ? /^-?\d*$/ : /^\d*$/
  if (!re.test(val)) return
  const num = val === '' ? '' : parseInt(val, 10)
  emit('update:modelValue', isNaN(num) ? '' : num)
}
</script>
