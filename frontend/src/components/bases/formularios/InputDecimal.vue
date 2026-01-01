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
      inputmode="decimal"
      :placeholder="placeholder"
      :disabled="disabled"
      :aria-invalid="Boolean(error)"
      :aria-describedby="describedById"
      :class="inputClass"
      :value="stringValue"
      @input="onInput"
      @blur="onBlur"
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
  id: { type: String, default: () => `decimal-${Math.random().toString(36).slice(2)}` },
  placeholder: { type: String, default: '' },
  required: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
  error: { type: String, default: '' },
  hint: { type: String, default: '' },
  size: { type: String, default: 'md' },
  decimalPlaces: { type: Number, default: 2 },
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
  let v = e.target.value.replace(',', '.')
  const re = props.allowNegative ? /^-?\d*(?:\.\d*)?$/ : /^\d*(?:\.\d*)?$/
  if (!re.test(v)) return
  const num = v === '' ? '' : Number(v)
  emit('update:modelValue', isNaN(num) ? '' : num)
}

function onBlur(e) {
  const val = e.target.value
  if (val === '') return
  const num = Number(val)
  if (isNaN(num)) return
  const fixed = num.toFixed(props.decimalPlaces)
  e.target.value = fixed
  emit('update:modelValue', Number(fixed))
}
</script>
