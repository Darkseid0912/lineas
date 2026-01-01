<template>
  <InputText
    v-bind="$attrs"
    :type="'email'"
    :label="label"
    :modelValue="modelValue"
    @update:modelValue="$emit('update:modelValue', $event)"
    :error="computedError"
    leadingIcon="mail"
    :placeholder="placeholder || 'correo@ejemplo.com'"
  />
</template>

<script setup>
import { computed } from 'vue'
import InputText from './InputText.vue'

const props = defineProps({
  modelValue: { type: String, default: '' },
  label: { type: String, default: 'Email' },
  placeholder: { type: String, default: '' },
  autovalidate: { type: Boolean, default: true },
  error: { type: String, default: '' }
})

defineEmits(['update:modelValue'])

const computedError = computed(() => {
  if (props.error) return props.error
  if (!props.autovalidate || !props.modelValue) return ''
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return re.test(props.modelValue) ? '' : 'Email no válido'
})
</script>
