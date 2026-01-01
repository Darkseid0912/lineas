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
        type="file"
        :multiple="multiple"
        :accept="accept"
        :disabled="disabled"
        :aria-invalid="Boolean(computedError)"
        :aria-describedby="describedById"
        :class="inputClass"
        @change="onChange"
      />
      <button v-if="clearable && hasFiles" type="button" class="absolute inset-y-0 right-0 px-3 text-slate-400 hover:text-slate-600" @click="clearFiles" aria-label="Limpiar archivos">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>
    <div class="mt-1">
      <p v-if="hint && !computedError" :id="describedById" class="text-xs text-slate-500">{{ hint }}</p>
      <p v-if="computedError" :id="describedById" class="text-xs text-rose-600">{{ computedError }}</p>
    </div>
    <ul v-if="hasFiles" class="mt-2 space-y-1">
      <li v-for="f in fileNames" :key="f.name" class="text-xs text-slate-600 flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4 text-slate-500">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7v10a2 2 0 002 2h6a2 2 0 002-2V9l-4-4H9a2 2 0 00-2 2z" />
        </svg>
        <span>{{ f.name }}</span>
        <span class="text-slate-400">({{ formatSize(f.size) }})</span>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'

const props = defineProps({
  modelValue: { type: Array, default: () => [] }, // Array<File>
  label: { type: String, default: '' },
  name: { type: String, default: '' },
  id: { type: String, default: () => `file-${Math.random().toString(36).slice(2)}` },
  required: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
  error: { type: String, default: '' },
  hint: { type: String, default: '' },
  size: { type: String, default: 'md' },
  multiple: { type: Boolean, default: false },
  accept: { type: String, default: '' },
  clearable: { type: Boolean, default: true },
  maxFiles: { type: Number, default: 0 }, // 0 = ilimitado
  maxSizeMB: { type: Number, default: 0 } // 0 = ilimitado
})

defineEmits(['update:modelValue'])

const localError = ref('')

const describedById = computed(() => props.id + '-desc')
const hasFiles = computed(() => (props.modelValue && props.modelValue.length > 0))
const fileNames = computed(() => props.modelValue || [])

const inputClass = computed(() => {
  const base = 'block w-full rounded-md border bg-white text-slate-900 shadow-sm focus:outline-none focus:ring-2 transition-colors duration-200 disabled:bg-slate-100 disabled:text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700'
  const sizes = { sm: 'text-sm', md: 'text-base', lg: 'text-lg' }
  const borders = props.error || localError.value ? 'border-rose-500 focus:ring-rose-600' : 'border-slate-300 focus:ring-blue-600'
  return [base, sizes[props.size] || sizes.md, borders].join(' ')
})

const computedError = computed(() => props.error || localError.value)

function onChange(e) {
  const files = Array.from(e.target.files || [])
  if (props.maxFiles && files.length > props.maxFiles) {
    localError.value = `Máximo ${props.maxFiles} archivo(s).`
  } else if (props.maxSizeMB && files.some(f => f.size > props.maxSizeMB * 1024 * 1024)) {
    localError.value = `Cada archivo debe ser menor a ${props.maxSizeMB} MB.`
  } else {
    localError.value = ''
  }
  const valid = localError.value ? [] : files
  emit('update:modelValue', valid)
}

function clearFiles() {
  localError.value = ''
  emit('update:modelValue', [])
}

function formatSize(bytes) {
  if (!bytes && bytes !== 0) return ''
  const kb = bytes / 1024
  if (kb < 1024) return `${kb.toFixed(1)} KB`
  const mb = kb / 1024
  return `${mb.toFixed(2)} MB`
}
</script>
