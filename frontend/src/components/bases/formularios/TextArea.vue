<template>
  <div class="mb-4">
    <label v-if="label" :for="id" class="block text-sm font-medium text-slate-700 mb-1">
      {{ label }}
      <span v-if="required" class="text-rose-600">*</span>
    </label>
    <textarea
      :id="id"
      :name="name"
      :rows="rows"
      :placeholder="placeholder"
      :disabled="disabled"
      :maxlength="maxlength"
      :aria-invalid="Boolean(error)"
      :aria-describedby="describedById"
      :class="inputClass"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
    />
    <div class="mt-1 flex items-center justify-between">
      <p v-if="hint && !error" :id="describedById" class="text-xs text-slate-500">{{ hint }}</p>
      <p v-if="error" :id="describedById" class="text-xs text-rose-600">{{ error }}</p>
      <p v-if="showCount && maxlength" class="text-xs text-slate-400 ml-auto">{{ (modelValue || '').length }}/{{ maxlength }}</p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: { type: String, default: '' },
  label: { type: String, default: '' },
  name: { type: String, default: '' },
  id: { type: String, default: () => `textarea-${Math.random().toString(36).slice(2)}` },
  placeholder: { type: String, default: '' },
  required: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
  error: { type: String, default: '' },
  hint: { type: String, default: '' },
  size: { type: String, default: 'md' },
  rows: { type: Number, default: 4 },
  maxlength: { type: Number },
  showCount: { type: Boolean, default: false }
})

defineEmits(['update:modelValue'])

const describedById = computed(() => props.id + '-desc')

const inputClass = computed(() => {
  const base = 'block w-full rounded-md border bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:outline-none focus:ring-2 transition-colors duration-200 disabled:bg-slate-100 disabled:text-slate-500'
  const sizes = {
    sm: 'p-2 text-sm',
    md: 'p-3 text-base',
    lg: 'p-4 text-lg'
  }
  const borders = props.error ? 'border-rose-500 focus:ring-rose-600' : 'border-slate-300 focus:ring-blue-600'
  return [base, sizes[props.size] || sizes.md, borders].join(' ')
})
</script>
