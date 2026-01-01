import axios from 'axios'

const baseURL = import.meta.env.VITE_API_BASE_URL

function readToken() {
  try {
    return (
      window.localStorage.getItem('auth_token') ||
      window.sessionStorage.getItem('auth_token') ||
      null
    )
  } catch {
    return null
  }
}

function clearToken() {
  try {
    window.localStorage.removeItem('auth_token')
    window.sessionStorage.removeItem('auth_token')
  } catch {}
}

// No storage helpers for type_text; keep sensitive role data in memory only.

const api = axios.create({
  baseURL,
  timeout: 8000,
  headers: { 'Accept': 'application/json' }
})

// Attach Bearer token on each request
api.interceptors.request.use((config) => {
  const token = readToken()
  if (token) {
    config.headers = config.headers || {}
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

// Handle unauthorized globally
api.interceptors.response.use(
  (res) => res,
  (error) => {
    if (error?.response?.status === 401) {
      clearToken()
      try {
        window.localStorage.removeItem('role_name')
        window.localStorage.removeItem('module_names')
        window.localStorage.removeItem('module_permissions_map')
      } catch {}
    }
    return Promise.reject(error)
  }
)

export { readToken, clearToken }
export default api
