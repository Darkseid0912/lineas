<template>
  <div class="space-y-2 w-full">
    <label class="text-slate-700 text-sm font-medium">{{ label }}</label>
    <InputText
      v-model="term"
      :placeholder="placeholder"
      name="module-search"
      size="sm"
      :clearable="true"
      class="mb-0"
    />
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { InputText } from '../../bases/formularios'

const props = defineProps({
  search: { type: String, default: '' },
  label: { type: String, default: 'Buscar módulo' },
  placeholder: { type: String, default: 'Escribe el nombre del módulo…' }
})

const emit = defineEmits(['update:search'])

const term = ref(props.search)

watch(() => props.search, (val) => { term.value = val })
watch(term, (val) => emit('update:search', val))

</script>
