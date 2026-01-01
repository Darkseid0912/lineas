import api from './api'

export async function createModule(payload) {
  const res = await api.post('/cat-modules', payload)
  return res.data
}

export async function getModules(params = {}) {
  const res = await api.get('/cat-modules', { params })
  return res.data
}
