<template>
  <section class="p-4">
    <FullscreenLoader :show="listLoading || loading" message="Procesando…" />
    <div class="rounded-xl border border-slate-200 bg-white p-4 max-w-full">
      <h2 class="text-xl font-semibold text-slate-800">Registrar permiso</h2>
      <p class="text-slate-600 mt-1">Crea un nuevo permiso.</p>

      <form class="mt-3 space-y-1" @submit.prevent="onSubmit">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
          <div class="md:col-span-2">
            <InputText
              v-model="form.permission_name"
              label="Nombre del permiso"
              placeholder="Ver dashboard"
              :maxlength="50"
              :required="true"
              :showCount="true"
              :error="errors.permission_name"
              size="sm"
            />
          </div>
          <div>
            <Select
              v-model="form.key"
              :options="keyOptions"
              label="Clave"
              placeholder="Selecciona clave"
              :required="true"
              :error="errors.key"
              size="sm"
            />
          </div>
        </div>

        <div class="flex items-center gap-2 pt-1">
          <BtnSave type="submit" :disabled="loading">
            <BtnSpinner :show="loading" size="xs" />
            <span>Guardar permiso</span>
          </BtnSave>
          <span v-if="success" class="text-xs text-green-700">Permiso creado correctamente.</span>
          <span v-if="serverMessage && !success" class="text-xs text-rose-700">{{ serverMessage }}</span>
        </div>
      </form>
    </div>

    <!-- Listado de permisos -->
    <div class="mt-4 rounded-xl border border-slate-200 bg-white p-4">
      <h3 class="text-lg font-semibold text-slate-800">Permisos registrados</h3>

      <div class="mt-3">
        <DataTable
          :columns="columns"
          :rows="rows"
          emptyText="No hay permisos registrados"
          :hasActions="true"
          :showView="false"
          @edit="onEdit"
          @delete="onDelete"
        >
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
import { createPermission, getPermissions } from '../../../services/permissions'
import { InputText, Select } from '../../bases/formularios'
import { BtnSave } from '../../bases/botones'
import { BtnSpinner, FullscreenLoader } from '../../bases/loaders'
import DataTable from '../../bases/tablas/DataTable.vue'
import Pagination from '../../bases/paginados/Pagination.vue'

defineOptions({ name: 'PermissionsPanel' })

const form = reactive({
  permission_name: '',
  key: ''
})
const keyOptions = [
  { value: 'view', label: 'view' },
  { value: 'create', label: 'create' },
  { value: 'edit', label: 'edit' },
  { value: 'delete', label: 'delete' }
]
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
  { key: 'permission_name', label: 'Nombre', sortable: true },
  { key: 'key', label: 'Clave', sortable: true, width: '140px' },
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
    await createPermission({ ...form })
    success.value = true
    form.permission_name = ''
    form.key = ''
    // Refresh list to include the newly created permission
    page.value = 1
    await fetchList()
  } catch (err) {
    const status = err?.response?.status
    const resData = err?.response?.data
    if (status === 422 && resData?.errors) {
      for (const [key, msgs] of Object.entries(resData.errors)) {
        errors[key] = Array.isArray(msgs) ? msgs[0] : String(msgs)
      }
    } else {
      serverMessage.value = resData?.message || 'Error al crear el permiso'
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
    const res = await getPermissions({ per_page: perPage.value, page: page.value })
    rows.value = Array.isArray(res?.data) ? res.data : []
    const meta = res?.meta || {}
    total.value = Number(meta.total || rows.value.length)
    perPage.value = Number(meta.per_page || perPage.value)
  } catch (err) {
    serverMessage.value = err?.response?.data?.message || 'Error al consultar permisos'
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
</script>
