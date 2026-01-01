<template>
  <section class="p-4">
    <FullscreenLoader :show="listLoading || loading" message="Cargando datos…" />
    <div class="rounded-xl border border-slate-200 bg-white p-4 max-w-full">
      <h2 class="text-xl font-semibold text-slate-800">Registrar módulo</h2>
      <p class="text-slate-600 mt-1">Crea un nuevo módulo.</p>

      <form class="mt-3 space-y-1" @submit.prevent="onSubmit">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
          <div class="md:col-span-2">
            <InputText
              v-model="form.module_name"
              label="Nombre del módulo"
              placeholder="Ventas"
              :maxlength="100"
              :required="true"
              :showCount="true"
              :error="errors.module_name"
              size="sm"
            />
          </div>
          <div>
            <InputText
              v-model="form.slug"
              label="Slug"
              placeholder="ventas"
              :maxlength="50"
              :required="true"
              :showCount="true"
              :error="errors.slug"
              size="sm"
            />
          </div>
        </div>

        <TextArea
          v-model="form.description"
          label="Descripción"
          placeholder="Descripción opcional"
          :rows="2"
          :error="errors.description"
          size="sm"
        />
   
        <div class="flex items-center gap-2 pt-1">
          <BtnSave type="submit" :disabled="loading">
            <BtnSpinner :show="loading" size="xs" />
            <span>Guardar módulo</span>
          </BtnSave>
          <span v-if="success" class="text-xs text-green-700">Módulo creado correctamente.</span>
          <span v-if="serverMessage && !success" class="text-xs text-rose-700">{{ serverMessage }}</span>
        </div>
      </form>
    </div>

    <!-- Listado de módulos -->
    <div class="mt-4 rounded-xl border border-slate-200 bg-white p-4">
      <h3 class="text-lg font-semibold text-slate-800">Módulos registrados</h3>

      <div class="mt-3">
        <DataTable
          :columns="columns"
          :rows="rows"
          emptyText="No hay módulos registrados"
          :hasActions="true"
          :showView="false"
          @edit="onEdit"
          @delete="onDelete"
        >
          <template #cell:is_active="{ value }">
            <span :class="value ? 'text-emerald-700' : 'text-slate-700'">{{ value ? 'Sí' : 'No' }}</span>
          </template>
          
          <template #cell:created_at="{ value }">
            <span class="text-slate-700">{{ formatDate(value) }}</span>
          </template>
        </DataTable>
      </div>

      <div class="mt-3 flex justify-end">
        <Pagination
          v-model="page"
          :total="total"
          :pageSize="perPage"
          @change="onPageChange"
        />
      </div>
    </div>
  </section>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue'
import { createModule, getModules } from '../../../services/modules'
import { InputText, TextArea } from '../../bases/formularios'
import { BtnSave } from '../../bases/botones'
import { BtnSpinner, FullscreenLoader } from '../../bases/loaders'
import DataTable from '../../bases/tablas/DataTable.vue'
import Pagination from '../../bases/paginados/Pagination.vue'

defineOptions({ name: 'ModulesPanel' })

const form = reactive({
  module_name: '',
  description: '',
  slug: '',
  is_active: true
})
const errors = reactive({})
const loading = ref(false)
const success = ref(false)
const serverMessage = ref('')

// List state
const listLoading = ref(false)
const page = ref(1)
const perPage = ref(10)
const total = ref(0)
const rows = ref([])

const columns = [
  { key: 'module_name', label: 'Nombre', sortable: true },
  { key: 'slug', label: 'Slug', sortable: true },
  { key: 'is_active', label: 'Activo', sortable: true, width: '100px', class: 'text-center', tdClass: 'text-center' },
  { key: 'created_at', label: 'Creado', sortable: true, width: '160px' }
]



function resetErrors() {
  for (const k of Object.keys(errors)) delete errors[k]
  serverMessage.value = ''
}

async function onSubmit() {
  resetErrors()
  success.value = false
  loading.value = true
  try {
    const data = await createModule({ ...form })
    success.value = true
    // Refresh list to include the newly created module
    page.value = 1
    await fetchList()
  } catch (err) {
    const status = err?.response?.status
    const resData = err?.response?.data
    if (status === 422 && resData?.errors) {
      for (const [key, msgs] of Object.entries(resData.errors)) {
        errors[key] = Array.isArray(msgs) ? msgs[0] : String(msgs)
      }
    } else if (status === 409) {
      serverMessage.value = resData?.message || 'El slug ya existe'
      errors.slug = 'El slug ya está en uso'
    } else {
      serverMessage.value = resData?.message || 'Error al crear el módulo'
    }
  } finally {
    loading.value = false
  }
}

function formatDate(val) {
  if (!val) return ''
  try {
    const d = new Date(val)
    return d.toLocaleString()
  } catch { return String(val) }
}

async function fetchList() {
  listLoading.value = true
  try {
    const res = await getModules({ per_page: perPage.value, page: page.value })
    rows.value = Array.isArray(res?.data) ? res.data : []
    const meta = res?.meta || {}
    total.value = Number(meta.total || rows.value.length)
    perPage.value = Number(meta.per_page || perPage.value)
  } catch (err) {
    // non-blocking: show a message near the table header
    serverMessage.value = err?.response?.data?.message || 'Error al consultar módulos'
  } finally {
    listLoading.value = false
  }
}

function onPageChange(p) {
  page.value = p
  fetchList()
}

onMounted(() => {
  fetchList()
})

function onEdit(row) {
  // sin funcionalidad por ahora
}

function onDelete(row) {
  // sin funcionalidad por ahora
}

function onSettings(row) {
  // sin funcionalidad por ahora
}
</script>
