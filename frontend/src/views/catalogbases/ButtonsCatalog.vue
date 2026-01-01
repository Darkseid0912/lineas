<template>
  <section class="mt-6">
    <div class="card">
      <h2 class="text-2xl font-semibold mb-4">Catálogo de Botones</h2>

      <div class="mb-6">
        <h3 class="font-medium mb-2">Variantes</h3>
        <div class="flex flex-wrap gap-3">
          <BtnAccept @click="onClick('Aceptar')">Aceptar</BtnAccept>
          <BtnSave @click="onClick('Guardar')">Guardar</BtnSave>
          <BtnCancel @click="onClick('Cancelar')">Cancelar</BtnCancel>
          <BtnEdit @click="onClick('Editar')">Editar</BtnEdit>
          <BtnDanger @click="onClick('Eliminar')">Eliminar</BtnDanger>
        </div>
        <p class="text-sm text-gray-600 mt-2">Todos los botones tienen colores sólidos, focus ring accesible y hover suave.</p>
      </div>

      <div class="mb-6">
        <h3 class="font-medium mb-2">Tamaños</h3>
        <div class="flex flex-col gap-2">
          <div class="flex items-center gap-3">
            <BtnSave size="sm">Guardar (sm)</BtnSave>
            <BtnSave size="md">Guardar (md)</BtnSave>
            <BtnSave size="lg">Guardar (lg)</BtnSave>
          </div>
        </div>
      </div>

      <div>
        <h3 class="font-medium mb-2">Estados</h3>
        <div class="flex items-center gap-3">
          <BtnSave :disabled="true">Guardar (disabled)</BtnSave>
          <BtnCancel :disabled="true">Cancelar (disabled)</BtnCancel>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { BtnSave, BtnCancel, BtnEdit, BtnAccept, BtnDanger } from '../../components/bases/botones'
const onClick = (label) => { console.log('Botón:', label) }
</script>
