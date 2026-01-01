import { reactive } from 'vue'

export const authSession = reactive({
  user: null,
  typeText: null,
  companyTradeName: null,
  companyId: null,
  roleName: null,
  modulesPermissions: []
})

export function setAuthSession({ user, typeText, companyTradeName, companyId, roleName, modulesPermissions }) {
  authSession.user = user ?? null
  authSession.typeText = typeText ?? null
  authSession.companyTradeName = companyTradeName ?? null
  authSession.companyId = companyId ?? null
  authSession.roleName = roleName ?? null
  authSession.modulesPermissions = Array.isArray(modulesPermissions) ? modulesPermissions : []
}

export function clearAuthSession() {
  authSession.user = null
  authSession.typeText = null
  authSession.companyTradeName = null
  authSession.companyId = null
  authSession.roleName = null
  authSession.modulesPermissions = []
}
