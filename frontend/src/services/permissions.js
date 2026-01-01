import api from './api'

export async function createPermission(payload) {
  const res = await api.post('/cat-permissions', payload)
  return res.data
}

export async function getPermissions(params = {}) {
  const res = await api.get('/cat-permissions', { params })
  return res.data
}
