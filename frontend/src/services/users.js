import api from './api'

export async function createUser(payload) {
  const res = await api.post('/users', payload)
  return res.data
}

export async function getUsers(params = {}) {
  const res = await api.get('/users', { params })
  return res.data
}

export async function getUserRoleModulesPermissions(userId) {
  const res = await api.get('/users/role-modules-permissions', { params: { id: userId } })
  return res.data
}
