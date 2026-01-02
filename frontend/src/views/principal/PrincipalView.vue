<template>
  <div class="h-screen bg-slate-50">
    <div class="flex h-full">
      <!-- Desktop sidebar -->
      <SideBar class="hidden md:block" :defaultOpen="true" @select="onSelect" />
      <!-- Mobile sidebar overlay -->
      <div v-if="sidebarOpen" class="fixed inset-0 z-40 md:hidden">
        <div class="absolute inset-0 bg-slate-900/40" @click="sidebarOpen = false"></div>
        <div class="absolute left-0 top-0 bottom-0 w-64 bg-white shadow-lg">
          <SideBar :defaultOpen="true" @select="onSelectAndClose" />
        </div>
      </div>
      <div class="flex-1 min-w-0 flex flex-col h-full">
        <NavBar :title="(authSession.typeText || '') + (authSession.companyTradeName ? ' • ' + authSession.companyTradeName : '')" :items="navItems" @toggle-sidebar="sidebarOpen = !sidebarOpen" />
        <div class="flex-1 min-h-0 overflow-y-auto">
          <RolesPanel v-if="panel === 'roles'" />
          <ModulesPanel v-else-if="panel === 'módulos' || panel === 'modulos'" />
          <PermissionsPanel v-else-if="panel === 'permisos'" />
          <UsersPanel v-else-if="panel === 'usuarios'" />
          <main v-else class="p-6">
            <div class="rounded-xl border border-slate-200 bg-white p-6">
              <h2 class="text-slate-800 font-semibold">Bienvenido</h2>
              <p class="text-slate-600 mt-2">Esta es la vista principal.</p>
            </div>
          </main>
        </div>
      </div>
    </div>
  </div>
  
</template>
<script setup>
import NavBar from '../../components/menu/NavBar.vue'
import SideBar from '../../components/menu/SideBar.vue'
import { authSession } from '../../services/session'
import RolesPanel from '../../components/config/roles/RolesPanel.vue'
import ModulesPanel from '../../components/config/modules/ModulesPanel.vue'
import PermissionsPanel from '../../components/config/permissions/PermissionsPanel.vue'
import UsersPanel from '../../components/config/users/UsersPanel.vue'
import { ref } from 'vue'

defineOptions({ name: 'PrincipalView' })

const navItems = [
  { label: 'cerrar sesion', to: '' }
]

// Sidebar uses its internal default items when none are provided

const panel = ref('')
function onSelect(key) {
  panel.value = key
}

const sidebarOpen = ref(false)
function onSelectAndClose(key) {
  panel.value = key
  sidebarOpen.value = false
}
</script>
