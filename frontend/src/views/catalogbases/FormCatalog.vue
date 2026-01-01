<template>
  <section class="mt-6">
    <div class="card">
      <h2 class="text-2xl font-semibold mb-4">Catálogo de Formularios</h2>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <InputText v-model="form.nombre" label="Nombre" placeholder="Tu nombre" leadingIcon="user" hint="Introduce tu nombre completo" />
        </div>
        <div>
          <InputEmail v-model="form.email" label="Email" hint="Usaremos tu email para contactarte" />
        </div>
        <div>
          <InputPassword v-model="form.password" label="Contraseña" hint="Mínimo 8 caracteres" />
        </div>
        <div>
          <TextArea v-model="form.comentarios" label="Comentarios" placeholder="Escribe tus comentarios" :rows="4" showCount :maxlength="200" />
        </div>
        <div>
          <Select v-model="form.pais" label="País" :options="paises" placeholder="Selecciona" />
        </div>
        <div>
          <InputPhone v-model="form.telefono" label="Teléfono" hint="Formato (###) ###-####" />
        </div>
        <div>
          <InputInteger v-model="form.entero" label="Entero" placeholder="Sólo números" />
        </div>
        <div>
          <InputDecimal v-model="form.decimal" label="Decimal" placeholder="0.00" :decimalPlaces="2" />
        </div>
        <div>
          <InputNumber v-model="form.numero" label="Número genérico" :min="0" :max="100" :step="1" />
        </div>
        <div>
          <InputPrice v-model="form.precio" label="Precio" currency="MXN" locale="es-MX" hint="Se formateará al salir del campo" />
        </div>
        <div>
          <InputFile v-model="form.archivos" label="Archivos" multiple accept="image/*,.pdf" :maxFiles="3" :maxSizeMB="5" hint="Hasta 3 archivos, máx 5MB cada uno" />
        </div>
      </div>

      <div class="mt-6">
        <h3 class="font-medium mb-2">Valores actuales</h3>
        <pre class="bg-slate-900 text-slate-100 rounded p-4 text-sm">{{ form }}</pre>
      </div>
    </div>
  </section>
</template>

<script setup>
import { reactive } from 'vue'
import { InputText, InputEmail, InputPassword, TextArea, Select, InputPhone, InputInteger, InputDecimal, InputNumber, InputPrice, InputFile } from '../../components/bases/formularios'

const form = reactive({ nombre: '', email: '', password: '', comentarios: '', pais: '', telefono: '', entero: '', decimal: '', numero: '', precio: '', archivos: [] })
const paises = [
  { value: 'mx', label: 'México' },
  { value: 'es', label: 'España' },
  { value: 'ar', label: 'Argentina' }
]
</script>
