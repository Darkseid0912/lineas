<template>
  <section class="p-4 sm:p-6 space-y-6">
    <FullscreenLoader :show="tableLoading || submitting" message="Cargando datos…" />
    <!-- Formulario de registro de tipo de usuario -->
    <div class="rounded-xl border border-slate-200 bg-white p-4 sm:p-6">
      <h2 class="text-slate-800 font-semibold">Registrar roles de usuario</h2>
      <p class="text-slate-600 mt-2">Agrega nuevos roles al catálogo de usuarios.</p>

      <div class="mt-4 max-w-md">
        <InputText
          v-model="name"
          label="Nombre"
          :required="true"
          placeholder="Ej. Administrador"
          :maxlength="100"
          :showCount="true"
          :error="fieldError('name')"
        />

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
          <div class="w-full sm:w-auto"><BtnSave class="w-full" :disabled="submitting" @click="onSubmit">Guardar</BtnSave></div>
          <div class="w-full sm:w-auto"><BtnCancel class="w-full" :disabled="submitting" @click="onReset">Cancelar</BtnCancel></div>
          <div class="sm:w-auto"><BtnSpinner v-if="submitting" /></div>
        </div>

        <p v-if="error" class="mt-3 text-sm text-rose-600">{{ error }}</p>
        <p v-if="success" class="mt-3 text-sm text-emerald-600">{{ success }}</p>
      </div>
    </div>

    <!-- Tabla de tipos registrados (sesión actual) -->
    <div class="rounded-xl border border-slate-200 bg-white p-4 sm:p-6">
      <h3 class="text-slate-800 font-semibold">Tipos de usuario</h3>

      <div class="mt-4 overflow-x-auto">
        <DataTable
          :columns="columns"
          :rows="rows"
          :rowKey="'id'"
          :hasActions="true"
          :showView="false"
          @edit="onEdit"
          @delete="onDelete"
          emptyText="Aún no hay registros"
        >
          <template #row-actions="{ row }">
            <button
              class="p-1.5 rounded-md hover:bg-slate-100 text-indigo-600"
              title="Configurar"
              @click="onSettings(row)"
            >
              <svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <circle cx="12" cy="12" r="3" stroke="#003cff" stroke-width="1.5"></circle> <path d="M3.66122 10.6392C4.13377 10.9361 4.43782 11.4419 4.43782 11.9999C4.43781 12.558 4.13376 13.0638 3.66122 13.3607C3.33966 13.5627 3.13248 13.7242 2.98508 13.9163C2.66217 14.3372 2.51966 14.869 2.5889 15.3949C2.64082 15.7893 2.87379 16.1928 3.33973 16.9999C3.80568 17.8069 4.03865 18.2104 4.35426 18.4526C4.77508 18.7755 5.30694 18.918 5.83284 18.8488C6.07287 18.8172 6.31628 18.7185 6.65196 18.5411C7.14544 18.2803 7.73558 18.2699 8.21895 18.549C8.70227 18.8281 8.98827 19.3443 9.00912 19.902C9.02332 20.2815 9.05958 20.5417 9.15224 20.7654C9.35523 21.2554 9.74458 21.6448 10.2346 21.8478C10.6022 22 11.0681 22 12 22C12.9319 22 13.3978 22 13.7654 21.8478C14.2554 21.6448 14.6448 21.2554 14.8478 20.7654C14.9404 20.5417 14.9767 20.2815 14.9909 19.9021C15.0117 19.3443 15.2977 18.8281 15.7811 18.549C16.2644 18.27 16.8545 18.2804 17.3479 18.5412C17.6837 18.7186 17.9271 18.8173 18.1671 18.8489C18.693 18.9182 19.2249 18.7756 19.6457 18.4527C19.9613 18.2106 20.1943 17.807 20.6603 17C20.8677 16.6407 21.029 16.3614 21.1486 16.1272M20.3387 13.3608C19.8662 13.0639 19.5622 12.5581 19.5621 12.0001C19.5621 11.442 19.8662 10.9361 20.3387 10.6392C20.6603 10.4372 20.8674 10.2757 21.0148 10.0836C21.3377 9.66278 21.4802 9.13092 21.411 8.60502C21.3591 8.2106 21.1261 7.80708 20.6601 7.00005C20.1942 6.19301 19.9612 5.7895 19.6456 5.54732C19.2248 5.22441 18.6929 5.0819 18.167 5.15113C17.927 5.18274 17.6836 5.2814 17.3479 5.45883C16.8544 5.71964 16.2643 5.73004 15.781 5.45096C15.2977 5.1719 15.0117 4.6557 14.9909 4.09803C14.9767 3.71852 14.9404 3.45835 14.8478 3.23463C14.6448 2.74458 14.2554 2.35523 13.7654 2.15224C13.3978 2 12.9319 2 12 2C11.0681 2 10.6022 2 10.2346 2.15224C9.74458 2.35523 9.35523 2.74458 9.15224 3.23463C9.05958 3.45833 9.02332 3.71848 9.00912 4.09794C8.98826 4.65566 8.70225 5.17191 8.21891 5.45096C7.73557 5.73002 7.14548 5.71959 6.65205 5.4588C6.31633 5.28136 6.0729 5.18269 5.83285 5.15108C5.30695 5.08185 4.77509 5.22436 4.35427 5.54727C4.03866 5.78945 3.80569 6.19297 3.33974 7C3.13231 7.35929 2.97105 7.63859 2.85138 7.87273" stroke="#003cff" stroke-width="1.5" stroke-linecap="round"></path> </g></svg>
            </button>
          </template>
        </DataTable>
        <div class="mt-4">
          <Pagination
            v-model="page"
            :total="total"
            :pageSize="perPage"
            :disabled="tableLoading"
            @change="fetchPage"
          />
        </div>
      </div>
    </div>
    
    <!-- Modal de configuración extraído a componente -->
    <RoleSettingsModal :show="showSettings" :role="selectedRole" @close="closeSettings" />
  </section>
</template>
<script setup>
import { ref, onMounted, computed } from 'vue'
import api from '../../../services/api'
import { InputText } from '../../bases/formularios'
import { BtnSave, BtnCancel } from '../../bases/botones'
import DataTable from '../../bases/tablas/DataTable.vue'
import Pagination from '../../bases/paginados/Pagination.vue'
import { BtnSpinner, FullscreenLoader } from '../../bases/loaders'
import { authSession } from '../../../services/session'
import RoleSettingsModal from './RoleSettingsModal.vue'

defineOptions({ name: 'RolesPanel' })

const name = ref('')
const submitting = ref(false)
const error = ref('')
const success = ref('')
const validationErrors = ref({})

const rows = ref([])
const tableLoading = ref(false)
const page = ref(1)
const perPage = ref(10)
const total = ref(0)
const showSettings = ref(false)
const selectedRole = ref(null)
// Estado del modal movido al componente RoleSettingsModal

const columns = [
  { key: 'name', label: 'Nombre' },
  { key: 'created_at', label: 'Creado', width: '220px' }
]

function fieldError(field) {
  const errs = validationErrors.value?.[field]
  return Array.isArray(errs) ? errs[0] : ''
}

function normalizeApiDateString(s) {
  if (!s || typeof s !== 'string') return ''
  // Strip microseconds to improve Date parsing: 2025-12-27T20:26:42.000000Z -> 2025-12-27T20:26:42Z
  return s.replace(/\.\d+Z$/, 'Z')
}

function formatApiDate(s) {
  const normalized = normalizeApiDateString(s)
  const d = new Date(normalized)
  if (isNaN(d)) return s || ''
  return d.toLocaleString('es-ES', {
    year: 'numeric', month: '2-digit', day: '2-digit',
    hour: '2-digit', minute: '2-digit', second: '2-digit'
  })
}

async function fetchPage(p = 1) {
  tableLoading.value = true
  error.value = ''
  try {
    if (!authSession.companyId) {
      throw new Error('No hay compañía activa en sesión')
    }
    const { data } = await api.get('/cat_users', { params: { page: p, id_companies: authSession.companyId } })
    const rawRows = Array.isArray(data?.data) ? data.data : []
    rows.value = rawRows.map(r => ({
      ...r,
      created_at: formatApiDate(r?.created_at)
    }))
    page.value = data?.current_page || p
    perPage.value = data?.per_page || 10
    total.value = data?.total || rows.value.length
  } catch (err) {
    error.value = err?.response?.data?.message || 'No fue posible obtener los datos'
  } finally {
    tableLoading.value = false
  }
}

async function onSubmit() {
  error.value = ''
  success.value = ''
  validationErrors.value = {}
  if (!name.value.trim()) {
    validationErrors.value = { name: ['El nombre es requerido'] }
    return
  }
  if (!authSession.companyId) {
    error.value = 'No hay compañía activa en sesión'
    return
  }
  submitting.value = true
  try {
    const { data } = await api.post('/cat_users', {
      name: name.value.trim(),
      id_companies: authSession.companyId
    })
    // Tras crear, refrescamos la primera página para que aparezca el nuevo registro.
    page.value = 1
    await fetchPage(1)
    success.value = 'Tipo registrado correctamente'
    name.value = ''
  } catch (err) {
    const respErrs = err?.response?.data?.errors
    validationErrors.value = respErrs || {}
    error.value = err?.response?.data?.message || 'No fue posible guardar'
  } finally {
    submitting.value = false
  }
}

function onReset() {
  name.value = ''
  error.value = ''
  success.value = ''
  validationErrors.value = {}
}

onMounted(() => {
  fetchPage(1)
})

function onEdit(row) {
  // sin funcionalidad por ahora
}

function onDelete(row) {
  // sin funcionalidad por ahora
}

function onSettings(row) {
  selectedRole.value = row
  showSettings.value = true
}

function closeSettings() {
  showSettings.value = false
  selectedRole.value = null
}
</script>
