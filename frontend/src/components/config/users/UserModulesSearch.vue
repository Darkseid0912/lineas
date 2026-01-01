<template>
  <div class="w-full">
    <InputText
      v-model="search"
      label="Buscar módulo"
      placeholder="Escribe para filtrar"
      size="sm"
      class="w-full"
    />
  </div>
</template>

<script setup>
import { InputText } from '../../bases/formularios'

defineOptions({ name: 'UserModulesSearch' })

const search = defineModel('search', { type: String, default: '' })
</script>
