<template>
  <div class="mb-4">
    <label v-if="label" :for="id" class="block text-sm font-medium text-slate-700 mb-1">
      {{ label }}
      <span v-if="required" class="text-rose-600">*</span>
    </label>
    <div class="relative">
      <span v-if="leadingIcon" class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
        <svg v-if="leadingIcon === 'user'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A7 7 0 1118.879 17.804M12 7a4 4 0 100-8 4 4 0 000 8z" />
        </svg>
        <svg v-else-if="leadingIcon === 'mail'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l9 6 9-6M21 8v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8" />
        </svg>
      </span>
      <input
        :id="id"
        :name="name"
        :type="type"
        :placeholder="placeholder"
        :disabled="disabled"
        :maxlength="maxlength"
        :aria-invalid="Boolean(error)"
        :aria-describedby="describedById"
        :class="inputClass"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
      />
      <button v-if="clearable && modelValue" type="button" class="absolute inset-y-0 right-0 px-3 text-slate-400 hover:text-slate-600" @click="$emit('update:modelValue', '')" aria-label="Limpiar">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
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
import { computed } from 'vue'

const props = defineProps({
  modelValue: { type: [String, Number], default: '' },
  label: { type: String, default: '' },
  name: { type: String, default: '' },
  id: { type: String, default: () => `input-${Math.random().toString(36).slice(2)}` },
  type: { type: String, default: 'text' },
  placeholder: { type: String, default: '' },
  required: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
  error: { type: String, default: '' },
  hint: { type: String, default: '' },
  size: { type: String, default: 'md' },
  leadingIcon: { type: String, default: '' },
  clearable: { type: Boolean, default: true },
  maxlength: { type: Number },
  showCount: { type: Boolean, default: false }
})

defineEmits(['update:modelValue'])

const describedById = computed(() => props.id + '-desc')

const inputClass = computed(() => {
  const base = 'block w-full rounded-md border bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:outline-none focus:ring-2 transition-colors duration-200 disabled:bg-slate-100 disabled:text-slate-500'
  const sizes = {
    sm: 'pl-3 pr-9 py-1.5 text-sm',
    md: 'pl-3 pr-9 py-2 text-base',
    lg: 'pl-3 pr-10 py-3 text-lg'
  }
  const withIconPadding = props.leadingIcon ? 'pl-9' : ''
  const borders = props.error ? 'border-rose-500 focus:ring-rose-600' : 'border-slate-300 focus:ring-blue-600'
  return [base, sizes[props.size] || sizes.md, withIconPadding, borders].join(' ')
})

// JSX eliminado; los iconos se manejan directamente en el template con v-if
</script>
