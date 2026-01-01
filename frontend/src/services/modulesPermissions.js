import api from './api'

/**
 * Obtiene módulos y permisos.
 * @returns {Promise<{ modules: any[], permissions: any[] }>} Listas de módulos y permisos
 */
export async function fetchModulesWithPermissions() {
  const res = await api.get('/modules-with-permissions')
  const data = res?.data?.data ?? {}
  return {
    modules: Array.isArray(data.modules) ? data.modules : [],
    permissions: Array.isArray(data.permissions) ? data.permissions : [],
  }
}
