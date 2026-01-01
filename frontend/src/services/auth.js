import api, { clearToken } from './api'
import { clearAuthSession } from './session'

function persistToken(token, remember = false) {
  try {
    if (remember) {
      window.localStorage.setItem('auth_token', token)
      window.sessionStorage.removeItem('auth_token')
    } else {
      window.sessionStorage.setItem('auth_token', token)
      window.localStorage.removeItem('auth_token')
    }
  } catch {}
}

// No storage persistence for type_text; keep it only in memory to avoid tampering

export async function login({ email, password, remember = false }) {
  const { data } = await api.post('/auth/login', { email, password })
  // Clear any stale auth before persisting the new token/role
  clearToken()
  if (data?.token) persistToken(data.token, remember)
  const roleName = data?.role_name ?? null
  try {
    // Consistent storage: set valid values, remove when missing
    if (typeof roleName === 'string' && roleName.trim()) {
      window.localStorage.setItem('role_name', roleName.trim())
    } else {
      window.localStorage.removeItem('role_name')
    }
    const modulesPermissions = Array.isArray(data?.modules_permissions) ? data.modules_permissions : null
    if (modulesPermissions) {
      const moduleNames = modulesPermissions
        .map(mp => (mp?.module_name ?? '').toString().trim())
        .filter(name => !!name)
      window.localStorage.setItem('module_names', JSON.stringify(moduleNames))

      // Build and persist a map of module_name -> keypermissions
      const permMap = {}
      modulesPermissions.forEach(mp => {
        const name = (mp?.module_name ?? '').toString().trim()
        if (!name) return
        const keys = Array.isArray(mp?.keypermissions)
          ? mp.keypermissions.map(k => (k ?? '').toString().trim()).filter(Boolean)
          : []
        permMap[name] = keys
      })
      window.localStorage.setItem('module_permissions_map', JSON.stringify(permMap))
    } else {
      window.localStorage.removeItem('module_names')
      window.localStorage.removeItem('module_permissions_map')
    }
  } catch {}
  return data
}

export async function me() {
  const { data } = await api.get('/auth/me')
  return data
}

export async function logout() {
  try {
    await api.post('/auth/logout')
  } catch {
    // Ignore network/auth errors; proceed to clear client session
  }
  clearToken()
  clearAuthSession()
  try {
    window.localStorage.removeItem('role_name')
    window.localStorage.removeItem('module_names')
    window.localStorage.removeItem('module_permissions_map')
  } catch {}
  return true
}
