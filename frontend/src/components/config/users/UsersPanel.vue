<template>
  <section class="p-4">
    <FullscreenLoader :show="panelLoading || submitting || listLoading" message="Procesando…" />
    <UserViewModal :show="showViewModal" :user="selectedUser" @close="showViewModal = false" />
    <div v-if="canCreateUser" class="rounded-xl border border-slate-200 bg-white p-4 max-w-full">
      <h2 class="text-xl font-semibold text-slate-800">Registrar usuario</h2>
      <p class="text-slate-600 mt-1">Da de alta un nuevo usuario en tu empresa.</p>

      <form class="mt-3 space-y-1" @submit.prevent="onSubmit">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
          <div class="md:col-span-1">
            <InputText
              v-model="form.name"
              label="Nombre"
              placeholder="Ej. Juan Pérez"
              :maxlength="255"
              :required="true"
              :showCount="true"
              :error="errors.name"
              size="sm"
            />
          </div>
          <div class="md:col-span-1">
            <InputEmail
              v-model="form.email"
              label="Correo"
              placeholder="correo@ejemplo.com"
              :required="true"
              :error="errors.email"
              size="sm"
            />
          </div>
          <div class="md:col-span-1">
            <InputPassword
              v-model="form.password"
              label="Contraseña"
              placeholder="Mínimo 8 caracteres"
              :required="true"
              :error="errors.password"
              size="sm"
            />
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
          <div class="md:col-span-1">
            <Select
              v-model="form.type"
              :options="typeOptions"
              label="Rol"
              placeholder="Selecciona rol"
              :required="true"
              :error="errors.type"
              size="sm"
            />
          </div>
        </div>

        <div class="flex items-center gap-2 pt-1">
          <BtnSave type="submit" :disabled="submitting || !form.id_companies">
            <BtnSpinner :show="submitting" size="xs" />
            <span>Guardar usuario</span>
          </BtnSave>
          <span v-if="success" class="text-xs text-emerald-700">Usuario creado correctamente.</span>
          <span v-if="serverMessage && !success" class="text-xs text-rose-700">{{ serverMessage }}</span>
        </div>
      </form>
    </div>

    <!-- Listado de usuarios -->
    <div v-if="canViewUsers" class="mt-4 rounded-xl border border-slate-200 bg-white p-4">
      <h3 class="text-lg font-semibold text-slate-800">Usuarios registrados</h3>

      <div class="mt-3">
        <DataTable
          :columns="columns"
          :rows="rows"
          emptyText="No hay usuarios registrados"
          :hasActions="true"
          :showView="false"
          :showEdit="canEditUsers"
          :showDelete="canDeleteUsers"
          v-on="dataTableListeners"
        >
          <template #row-actions="{ row }">
            <button
              v-if="canViewDetails"
              class="p-1.5 rounded-md text-emerald-600"
              :class="isAdminRow(row) ? 'opacity-40 cursor-not-allowed' : 'hover:bg-slate-100'"
              title="Ver"
              :disabled="isAdminRow(row)"
              @click="!isAdminRow(row) && onView(row)"
            >
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12Z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
          </template>
          <template #cell:type="{ row }">
            <span class="text-slate-700">{{ row.type_catalog?.name || '-' }}</span>
          </template>
          <template #cell:company="{ row }">
            <span class="text-slate-700">{{ row.company?.trade_name || row.company?.name || '-' }}</span>
          </template>
          <template #cell:created_at="{ value }">
            <span class="text-slate-700">{{ formatApiDate(value) }}</span>
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
import { reactive, ref, onMounted, watchEffect, computed } from 'vue'
import { authSession } from '../../../services/session'
import { createUser, getUsers } from '../../../services/users'
import { InputText, InputEmail, InputPassword, Select } from '../../bases/formularios'
import { BtnSave } from '../../bases/botones'
import { BtnSpinner, FullscreenLoader } from '../../bases/loaders'
import api from '../../../services/api'
import DataTable from '../../bases/tablas/DataTable.vue'
import Pagination from '../../bases/paginados/Pagination.vue'
import UserViewModal from './UserViewModal.vue'

defineOptions({ name: 'UsersPanel' })

const form = reactive({
  name: '',
  email: '',
  password: '',
  type: '',
  id_companies: authSession.companyId ?? null
})
const errors = reactive({})
const submitting = ref(false)
const success = ref(false)
const serverMessage = ref('')
const panelLoading = ref(false)
const showViewModal = ref(false)
const selectedUser = ref(null)

// List state
const listLoading = ref(false)
const page = ref(1)
const perPage = ref(10)
const total = ref(0)
const rows = ref([])

const columns = [
  { key: 'name', label: 'Nombre', sortable: true },
  { key: 'email', label: 'Correo', sortable: true },
  { key: 'type', label: 'Tipo', sortable: false, width: '140px' },
  { key: 'company', label: 'Empresa', sortable: false },
  { key: 'created_at', label: 'Creado', sortable: true, width: '160px' }
]

const typeOptions = ref([])
// Permission: show create form if module "Usuarios" includes "create" or user is admin
const canCreateUser = computed(() => {
  try {
    const roleName = (window.localStorage.getItem('role_name') || '').trim()
    if (roleName === 'Administrador') return true
    const raw = window.localStorage.getItem('module_permissions_map')
    const permMap = raw ? JSON.parse(raw) : {}
    const keys = Array.isArray(permMap['Usuarios']) ? permMap['Usuarios'] : []
    return keys.map(k => (k || '').toString().trim().toLowerCase()).includes('create')
  } catch {
    return false
  }
})

// Permission: show users list if module "Usuarios" includes "view" or user is admin
const canViewUsers = computed(() => {
  try {
    const roleName = (window.localStorage.getItem('role_name') || '').trim()
    if (roleName === 'Administrador') return true
    const raw = window.localStorage.getItem('module_permissions_map')
    const permMap = raw ? JSON.parse(raw) : {}
    const keys = Array.isArray(permMap['Usuarios']) ? permMap['Usuarios'] : []
    return keys.map(k => (k || '').toString().trim().toLowerCase()).includes('view')
  } catch {
    return false
  }
})

// Permission: enable edit handler if module "Usuarios" includes "edit"/"editar" or user is admin
const canEditUsers = computed(() => {
  try {
    const roleName = (window.localStorage.getItem('role_name') || '').trim()
    if (roleName === 'Administrador') return true
    const raw = window.localStorage.getItem('module_permissions_map')
    const permMap = raw ? JSON.parse(raw) : {}
    const keys = Array.isArray(permMap['Usuarios']) ? permMap['Usuarios'] : []
    const lowered = keys.map(k => (k || '').toString().trim().toLowerCase())
    return lowered.includes('edit')
  } catch {
    return false
  }
})

// Permission: enable delete handler if module "Usuarios" includes "delete" or user is admin
const canDeleteUsers = computed(() => {
  try {
    const roleName = (window.localStorage.getItem('role_name') || '').trim()
    if (roleName === 'Administrador') return true
    const raw = window.localStorage.getItem('module_permissions_map')
    const permMap = raw ? JSON.parse(raw) : {}
    const keys = Array.isArray(permMap['Usuarios']) ? permMap['Usuarios'] : []
    const lowered = keys.map(k => (k || '').toString().trim().toLowerCase())
    return lowered.includes('delete')
  } catch {
    return false
  }
})

const canViewDetails = computed(() => {
  try {
    const roleName = (window.localStorage.getItem('role_name') || '').trim()
    if (roleName === 'Administrador') return true
    const raw = window.localStorage.getItem('module_permissions_map')
    const permMap = raw ? JSON.parse(raw) : {}
    const keys = Array.isArray(permMap['Usuarios']) ? permMap['Usuarios'] : []
    const lowered = keys.map(k => (k || '').toString().trim().toLowerCase())
    return lowered.includes('details')
  } catch {
    return false
  }
})

// Conditionally attach DataTable listeners (edit/delete)
const dataTableListeners = computed(() => {
  const obj = {}
  if (canEditUsers.value) obj.edit = onEdit
  if (canDeleteUsers.value) obj.delete = onDelete
  return obj
})

watchEffect(() => {
  if (!form.id_companies && authSession.companyId) {
    form.id_companies = authSession.companyId
  }
})

async function loadUserTypes() {
  panelLoading.value = true
  try {
    const { data } = await api.get('/cat_users', { params: { page: 1, id_companies: authSession.companyId } })
    const raw = Array.isArray(data?.data) ? data.data : []
    typeOptions.value = raw.map(r => ({ value: r.id, label: r.name }))
  } catch (err) {
    serverMessage.value = err?.response?.data?.message || 'No fue posible obtener los tipos de usuario'
  } finally {
    panelLoading.value = false
  }
}

function resetErrors() {
  for (const k of Object.keys(errors)) delete errors[k]
  serverMessage.value = ''
}

async function onSubmit() {
  resetErrors()
  success.value = false
  submitting.value = true
  try {
    await createUser({ ...form })
    success.value = true
    form.name = ''
    form.email = ''
    form.password = ''
    form.type = ''
    // Refresh list to include the newly created user
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
      errors.email = 'El correo ya está en uso'
      serverMessage.value = resData?.message || 'El correo ya existe'
    } else {
      serverMessage.value = resData?.message || 'Error al crear el usuario'
    }
  } finally {
    submitting.value = false
  }
}

onMounted(() => {
  loadUserTypes()
  fetchList()
})

function normalizeApiDateString(s) {
  if (!s || typeof s !== 'string') return ''
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

async function fetchList() {
  listLoading.value = true
  try {
    const res = await getUsers({ per_page: perPage.value, page: page.value })
    const raw = Array.isArray(res?.data) ? res.data : []
    rows.value = raw
    const meta = res?.meta || {}
    total.value = Number(meta.total || rows.value.length)
    perPage.value = Number(meta.per_page || perPage.value)
  } catch (err) {
    serverMessage.value = err?.response?.data?.message || 'Error al consultar usuarios'
  } finally {
    listLoading.value = false
  }
}

function onPageChange(p) {
  page.value = p
  fetchList()
}

function onEdit(row) {
  // sin funcionalidad por ahora
}

function onDelete(row) {
  // sin funcionalidad por ahora
}

function onView(row) {
  selectedUser.value = row
  showViewModal.value = true
}

function isAdminRow(row) {
  const name = (row?.type_catalog?.name || '').toString().trim().toLowerCase()
  return name === 'administrador'
}
</script>
