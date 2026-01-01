<template>
  <FullscreenLoader :show="modalLoading" message="Cargando módulos y permisos…" :zIndex="1100" size="lg" />
  <FullscreenLoader :show="savingSettings" message="Guardando relaciones…" :zIndex="1150" size="lg" />
  <BaseModal :show="show" :title="`Configurar rol: ${role?.name || '—'}`" @close="emit('close')">
    <div class="space-y-3">
      <div class="rounded-lg border border-indigo-200 bg-indigo-50 p-3">
        <p class="text-slate-800 font-semibold">Configurando módulos y permisos</p>
        <p class="text-slate-700 text-sm">Rol: <span class="font-medium">{{ role?.name || '—' }}</span></p>
        <p class="text-slate-500 text-xs">Selecciona los módulos y define sus permisos para este rol.</p>
      </div>
      <div v-if="modalLoading" class="flex items-center gap-2 text-slate-600">
        <BtnSpinner />
        <span>Cargando módulos y permisos…</span>
      </div>
      <p v-else-if="modalError" class="text-rose-600 text-sm">{{ modalError }}</p>
      <div v-else class="space-y-4">
        <!-- Paso 1: Módulos -->
        <div class="space-y-2">
          <div class="flex items-center justify-between gap-3">
            <ModuleSearch v-model:search="search" />
          </div>
          <h4 class="text-slate-800 font-semibold flex items-center gap-2">
            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-indigo-600 text-white text-xs">1</span>
            Módulos
          </h4>
          <p class="text-slate-600 text-sm">Elige uno o varios módulos.</p>
          <div class="flex items-center justify-between">
            <p class="text-slate-500 text-xs">Seleccionados: {{ selectedModuleIds.length }}</p>
          </div>
          <div
            v-for="m in filteredModules"
            :key="m.id"
            class="border rounded-lg mb-2"
            :class="isModuleSelected(m.id) ? 'border-indigo-400 ring-2 ring-indigo-500 bg-indigo-50' : 'border-slate-200 hover:border-indigo-300'"
          >
            <div class="px-3 py-2 flex items-center justify-between">
              <label class="flex items-center gap-2 cursor-pointer select-none">
                <input
                  type="checkbox"
                  class="h-4 w-4 border-slate-300 rounded focus:ring-indigo-400"
                  :aria-label="'Seleccionar módulo ' + (m.module_name || '')"
                  :checked="isModuleSelected(m.id)"
                  @change.stop="toggleModule(m)"
                />
                <span class="text-slate-800 font-medium">{{ m.module_name }}</span>
              </label>
              <span v-if="isModuleSelected(m.id)" class="text-indigo-600 text-xs">Seleccionado</span>
              <span v-else class="text-slate-400 text-xs">No seleccionado</span>
            </div>
            <!-- Paso 2: Permisos del módulo seleccionado -->
            <div class="px-3 pb-3" :class="isModuleSelected(m.id) ? '' : 'opacity-50 pointer-events-none'">
              <p class="text-slate-800 text-xs font-medium mb-1">Permisos</p>
              <PermissionSearch v-model:search="permissionSearchByModule[m.id]" />
              <template v-if="Array.isArray(allPermissions) && allPermissions.length">
                <div v-if="filteredPermissionsFor(m.id).length" class="flex flex-col gap-1.5">
                  <label
                    v-for="p in filteredPermissionsFor(m.id)"
                    :key="p.id"
                    class="flex items-center gap-2 text-xs font-medium cursor-pointer select-none"
                  >
                    <input
                      type="checkbox"
                      class="h-4 w-4 border-slate-300 rounded focus:ring-indigo-400"
                      :aria-label="'Permiso ' + (p.permission_name || '')"
                      :checked="isPermissionSelected(m.id, p.id)"
                      :disabled="!isModuleSelected(m.id)"
                      @change.stop="togglePermission(m, p)"
                    />
                    <span>{{ p.permission_name }}</span>
                  </label>
                </div>
                <p v-else class="text-slate-500 text-xs">Sin coincidencias con la búsqueda.</p>
              </template>
              <p v-else class="text-slate-500 text-xs">Sin permisos registrados para este módulo.</p>
              <div class="mt-2 flex items-center justify-between">
                <p v-if="isModuleSelected(m.id) && !hasAnyPermissionForModule(m.id)" class="text-xs text-rose-600">Selecciona al menos un permiso.</p>
                <p v-else-if="isModuleSelected(m.id)" class="text-xs text-slate-500">Permisos seleccionados: {{ (selectedPermissionsByModule[m.id] || []).length }}</p>
                <p v-else class="text-xs text-slate-400">Selecciona el módulo para activar sus permisos.</p>
              </div>
            </div>
          </div>
          <p v-if="modules.length && filteredModules.length === 0" class="text-slate-500 text-sm">Sin módulos que coincidan con la búsqueda.</p>
          <p v-if="!modules.length" class="text-slate-500 text-sm">No hay módulos registrados.</p>
        </div>
      </div>
    </div>
    <template #footer>
      <div class="flex justify-end gap-2 items-center">
        <p v-if="show && !canSave" class="text-xs text-rose-600 mr-auto">Selecciona al menos un módulo y un permiso.</p>
        <BtnCancel @click="emit('close')">Cerrar</BtnCancel>
        <BtnSave :disabled="!canSave || savingSettings" @click="onSaveSettings">Guardar</BtnSave>
      </div>
    </template>
  </BaseModal>
</template>

<script setup>
import { ref, computed, watch, reactive } from 'vue'
import { fetchModulesWithPermissions } from '../../../services/modulesPermissions'
import { syncRolePermissionsBulk, listRolePermissionsByRole } from '../../../services/rolePermissions'
import { BtnSave, BtnCancel } from '../../bases/botones'
import { BtnSpinner, FullscreenLoader } from '../../bases/loaders'
import { authSession } from '../../../services/session'
import BaseModal from '../../bases/modals/BaseModal.vue'
import ModuleSearch from './ModuleSearch.vue'
import PermissionSearch from './PermissionSearch.vue'

defineOptions({ name: 'RoleSettingsModal' })

const props = defineProps({
  show: { type: Boolean, default: false },
  role: { type: Object, default: null }
})

const emit = defineEmits(['close'])

const modules = ref([])
const allPermissions = ref([])
const modalLoading = ref(false)
const modalError = ref('')
const selectedModuleIds = ref([])
const selectedPermissionsByModule = ref({})
const savingSettings = ref(false)
const search = ref('')
const permissionSearchByModule = reactive({})
// removed active-only toggle
const filteredModules = computed(() => {
  const list = modules.value.slice()
  if (!search.value) return list.sort((a, b) => (a?.module_name || '').localeCompare(b?.module_name || ''))
  const term = norm(search.value)
  return list
    .filter(m => norm(m?.module_name).includes(term))
    .sort((a, b) => (a?.module_name || '').localeCompare(b?.module_name || ''))
})

function norm(s) {
  return (s ?? '')
    .toString()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .toLowerCase()
    .trim()
}

function filteredPermissionsFor(moduleId) {
  const term = norm(permissionSearchByModule[moduleId] || '')
  const base = allPermissions.value.slice()
  const module = modules.value.find(mm => mm?.id === moduleId)
  const slug = (module?.slug || '').toString().toLowerCase().trim()
  const separators = ['.', ':', '_']
  // Filtrar permisos que correspondan al módulo por prefijo de slug
  const anyScopedExists = base.some(p => {
    const key = (p?.key || '').toString().toLowerCase()
    if (!slug || !key) return false
    return separators.some(sep => key.startsWith(`${slug}${sep}`))
  })
  let scoped = []
  if (anyScopedExists) {
    scoped = base.filter(p => {
      const key = (p?.key || '').toString().toLowerCase()
      if (!slug || !key) return false
      return separators.some(sep => key.startsWith(`${slug}${sep}`))
    })
  } else {
    // Si las claves son genéricas (create, edit, delete, view), aplicar todas al módulo
    scoped = base
  }
  // Búsqueda local por nombre
  if (term) {
    scoped = scoped.filter(p => norm(p?.permission_name).includes(term))
  }
  return scoped.sort((a, b) => (a?.permission_name || '').localeCompare(b?.permission_name || ''))
}

function resetState() {
  modules.value = []
  allPermissions.value = []
  modalError.value = ''
  selectedModuleIds.value = []
  selectedPermissionsByModule.value = {}
  savingSettings.value = false
  Object.keys(permissionSearchByModule).forEach(k => { delete permissionSearchByModule[k] })
}

async function loadModulesWithPermissions() {
  modalLoading.value = true
  modalError.value = ''
  try {
    const params = {}
    const { modules: mods, permissions: perms } = await fetchModulesWithPermissions()
    modules.value = mods
    allPermissions.value = perms

    // Precargar selección existente del rol
    if (props.role?.id) {
      const rels = await listRolePermissionsByRole(props.role.id)
      const items = Array.isArray(rels?.data) ? rels.data : []
      const moduleIds = [...new Set(items.map(i => i?.id_module).filter(Boolean))]
      selectedModuleIds.value = moduleIds
      const byModule = {}
      for (const i of items) {
        const mid = i?.id_module
        const pid = i?.id_permission
        if (!mid || !pid) continue
        byModule[mid] = byModule[mid] || []
        if (!byModule[mid].includes(pid)) byModule[mid].push(pid)
      }
      selectedPermissionsByModule.value = byModule
    }
  } catch (err) {
    modalError.value = err?.response?.data?.message || 'No fue posible cargar módulos y permisos'
  } finally {
    modalLoading.value = false
  }
}

function isModuleSelected(id) {
  return selectedModuleIds.value.includes(id)
}

function toggleModule(m) {
  const id = m?.id
  if (id == null) return
  const idx = selectedModuleIds.value.indexOf(id)
  if (idx >= 0) {
    selectedModuleIds.value.splice(idx, 1)
    // Limpia estado asociado al módulo al deseleccionarlo
    delete selectedPermissionsByModule.value[id]
    delete permissionSearchByModule[id]
  } else {
    selectedModuleIds.value.push(id)
  }
}

function isPermissionSelected(moduleId, permId) {
  const arr = selectedPermissionsByModule.value[moduleId] || []
  return arr.includes(permId)
}

function togglePermission(m, p) {
  const moduleId = m?.id
  const permId = p?.id
  if (moduleId == null || permId == null) return
  const arr = selectedPermissionsByModule.value[moduleId] || []
  const idx = arr.indexOf(permId)
  if (idx >= 0) {
    arr.splice(idx, 1)
  } else {
    arr.push(permId)
  }
  selectedPermissionsByModule.value[moduleId] = [...arr]
}

function hasAnyPermissionForModule(moduleId) {
  const arr = selectedPermissionsByModule.value[moduleId] || []
  return arr.length > 0
}

const canSave = computed(() => {
  if (selectedModuleIds.value.length === 0) return false
  // Cada módulo seleccionado debe tener al menos un permiso seleccionado
  return selectedModuleIds.value.every(id => hasAnyPermissionForModule(id))
})

async function onSaveSettings() {
  if (!canSave.value || savingSettings.value) return
  savingSettings.value = true
  try {
    const payload = {
      role_id: props.role?.id ?? null,
      id_companies: authSession.companyId ?? null,
      modules: selectedModuleIds.value.map(id => ({
        module_id: id,
        permission_ids: [...(selectedPermissionsByModule.value[id] || [])]
      }))
    }
    if (!payload.role_id || !payload.id_companies) {
      throw new Error('Falta el contexto de rol o empresa')
    }
    await syncRolePermissionsBulk(payload)
    emit('close')
  } catch (e) {
    modalError.value = e?.response?.data?.message || e?.message || 'No fue posible guardar las relaciones'
  } finally {
    savingSettings.value = false
  }
}

watch(() => props.show, (val) => {
  if (val) {
    resetState()
    loadModulesWithPermissions()
  } else {
    resetState()
  }
})

// removed active-only reload
</script>
