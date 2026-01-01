import api from './api'

/**
 * Guarda relaciones rol-permisos en bloque.
 * @param {{ role_id: number, id_companies: number, modules: Array<{module_id: number, permission_ids: number[]}> }} payload
 * @returns {Promise<any>} Respuesta del backend
 */
export async function saveRolePermissionsBulk(payload) {
  const { role_id, id_companies } = payload || {}
  if (!role_id || !id_companies) {
    throw new Error('Falta el contexto de rol o empresa')
  }
  const res = await api.post('/rel-role-permissions/bulk', payload)
  return res?.data
}

/**
 * Sincroniza relaciones: elimina las no seleccionadas y agrega/restaura las seleccionadas.
 * @param {{ role_id: number, id_companies: number, modules: Array<{module_id: number, permission_ids: number[]}> }} payload
 * @returns {Promise<any>} Respuesta del backend
 */
export async function syncRolePermissionsBulk(payload) {
  const { role_id, id_companies } = payload || {}
  if (!role_id || !id_companies) {
    throw new Error('Falta el contexto de rol o empresa')
  }
  const res = await api.post('/rel-role-permissions/sync', payload)
  return res?.data
}

/**
 * Lista relaciones rol-permisos (opcional de utilidad).
 * @param {{ id_role?: number, id_module?: number, id_permission?: number, page?: number, per_page?: number }} params
 * @returns {Promise<any>} Respuesta del backend
 */
export async function listRolePermissions(params = {}) {
  const res = await api.get('/rel-role-permissions', { params })
  return res?.data
}

/**
 * Lista TODAS las relaciones de un rol sin paginación.
 * @param {number} id_role
 * @returns {Promise<any>} Respuesta con arreglo en `data`
 */
export async function listRolePermissionsByRole(id_role) {
  if (!id_role) throw new Error('id_role es requerido')
  const res = await api.get('/rel-role-permissions/by-role', { params: { id_role } })
  return res?.data
}
