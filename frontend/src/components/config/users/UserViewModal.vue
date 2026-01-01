<template>
  <FullscreenLoader :show="loading" message="Cargando detalles…" :z-index="2001" />
  <BaseModal :show="show" title="Detalle de usuario" @close="emit('close')">
    <div class="max-h-[65vh] overflow-y-auto">
      <div class="p-3 space-y-3">
        <p v-if="loading" class="text-slate-600 text-sm">Cargando detalles…</p>
        <p v-else-if="error" class="text-rose-600 text-sm">{{ error }}</p>
        <template v-else>
          <div class="rounded-lg border border-slate-200 p-3 bg-white">
            <p class="text-slate-800 font-semibold">Usuario</p>
            <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-2 text-slate-700 text-sm">
              <p><span class="font-medium">Nombre:</span> {{ userDetail?.name || '-' }}</p>
              <p><span class="font-medium">Correo:</span> {{ userDetail?.email || '-' }}</p>
              <p><span class="font-medium">Rol:</span> {{ userDetail?.role_name || '-' }}</p>
            </div>
          </div>

          <div class="rounded-lg border border-slate-200 p-3 bg-white">
            <div class="flex items-center justify-between">
              <p class="text-slate-800 font-semibold">Módulos y permisos</p>
              <span class="text-xs text-slate-500">Total módulos: {{ filteredModules.length }}</span>
            </div>
            <div class="mt-2">
              <UserModulesSearch v-model:search="searchTerm" />
            </div>
            <div v-if="filteredModules.length" class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-2">
              <div v-for="m in filteredModules" :key="m.module_name" class="border border-slate-100 rounded-lg p-3 bg-slate-50">
                <p class="text-slate-800 text-sm font-semibold">{{ m.module_name }}</p>
                <div class="mt-2 flex flex-wrap gap-1.5">
                  <span
                    v-for="perm in m.permissions"
                    :key="perm"
                    class="inline-flex items-center px-2.5 py-0.5 text-xs rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100"
                  >
                    {{ perm }}
                  </span>
                </div>
              </div>
            </div>
            <p v-else class="text-slate-600 text-sm">Sin módulos o permisos.</p>
          </div>
        </template>
      </div>
    </div>
  </BaseModal>
</template>

<script setup>
import BaseModal from '../../bases/modals/BaseModal.vue'
import { FullscreenLoader } from '../../bases/loaders'
import { ref, watch, computed } from 'vue'
import { getUserRoleModulesPermissions } from '../../../services/users'
import UserModulesSearch from './UserModulesSearch.vue'

defineOptions({ name: 'UserViewModal' })

const props = defineProps({
  show: { type: Boolean, default: false },
  user: { type: Object, default: null }
})

const emit = defineEmits(['close'])

const loading = ref(false)
const error = ref('')
const userDetail = ref(null)
const modules = ref([])
const searchTerm = ref('')

function norm(s) {
  return (s ?? '')
    .toString()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .toLowerCase()
    .trim()
}

const filteredModules = computed(() => {
  const list = Array.isArray(modules.value) ? modules.value.slice() : []
  const term = norm(searchTerm.value)
  if (!term) return list
  return list.filter(m => norm(m?.module_name).includes(term))
})

async function fetchDetails() {
  if (!props?.user?.id) return
  loading.value = true
  error.value = ''
  try {
    const res = await getUserRoleModulesPermissions(props.user.id)
    const data = res?.data ?? {}
    userDetail.value = data?.user ?? null
    modules.value = Array.isArray(data?.modules) ? data.modules : []
  } catch (e) {
    error.value = e?.response?.data?.message || 'No fue posible obtener los detalles'
  } finally {
    loading.value = false
  }
}

watch(() => props.show, (val) => {
  if (val && props?.user?.id) {
    fetchDetails()
  }
})
watch(() => props.user?.id, (id) => {
  if (props.show && id) {
    fetchDetails()
  }
})
</script>
