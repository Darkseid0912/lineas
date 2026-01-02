<template>
  <aside :class="['bg-white border-r border-slate-200 transition-all duration-200', isOpen ? 'w-64' : 'w-16']" class="h-screen sticky top-0 flex-shrink-0">
    <div class="h-14 flex items-center px-3 border-b border-slate-200">
      <button @click="isOpen = !isOpen" class="hidden md:inline-flex p-2 rounded hover:bg-slate-100 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-500/50" aria-label="Toggle sidebar">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>
      <span v-if="isOpen" class="ml-2 text-slate-700 font-medium">{{ title }}</span>
    </div>
    <nav class="p-2 space-y-1">
      <template v-for="(item, idx) in menuItems" :key="idx">
        <!-- Group with submenu -->
        <div v-if="item.children && item.children.length">
          <button
            @click="toggleGroup(idx)"
            :aria-expanded="isGroupOpen(idx)"
            class="group w-full flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-100 text-slate-700 select-none transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-500/40"
          >
            <span :class="['inline-block w-5 text-center transition-transform duration-200', isGroupOpen(idx) ? 'rotate-180' : 'rotate-0']">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </span>
            <span v-if="isOpen" class="flex-1 text-sm font-medium">{{ item.label }}</span>
          </button>
          <div v-if="isGroupOpen(idx) && isOpen" class="mt-1 space-y-1 ml-2 pl-2 border-l border-slate-200">
            <template v-for="(child, cidx) in item.children" :key="cidx">
              <MenuButton
                v-if="child.to"
                :to="child.to"
                :label="child.label"
                :collapsed="!isOpen"
              />
              <button
                v-else
                @click="emit('select', normalizeKey(child.label))"
                class="w-full flex items-center gap-2 px-3 py-2 rounded hover:bg-slate-100 text-slate-700 select-none transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-500/40"
              >
                <span class="text-sm font-medium">{{ child.label }}</span>
              </button>
            </template>
          </div>
        </div>
        <!-- Single item -->
        <MenuButton
          v-else
          :to="item.to"
          :label="item.label"
          :collapsed="!isOpen"
        />
      </template>
    </nav>
    <slot />
  </aside>
</template>
<script setup>
import { ref, computed } from 'vue'
import { MenuButton } from './base'
const emit = defineEmits(['select'])

defineOptions({ name: 'SideBar' })
const props = defineProps({
  items: { type: Array, default: () => [] },
  defaultOpen: { type: Boolean, default: true },
  title: { type: String, default: 'Nombre del proyecto' }
})
const isOpen = ref(props.defaultOpen)

const defaultItems = [
  { label: 'Configuración', children: [
    { label: 'Roles', to: '' },
    { label: 'Usuarios', to: '' }
  ] }
]

// Only show default items if role_name === 'Administrador'
const isAdmin = computed(() => {
  try {
    return (window.localStorage.getItem('role_name') || '').trim() === 'Administrador'
  } catch {
    return false
  }
})

// Read module_names stored in localStorage during login
const moduleNames = computed(() => {
  try {
    const raw = window.localStorage.getItem('module_names')
    return raw ? JSON.parse(raw) : []
  } catch {
    return []
  }
})

const menuItems = computed(() => {
  if (Array.isArray(props.items) && props.items.length) return props.items
  if (isAdmin.value) return defaultItems
  // For non-admin: show only children whose label is present in module_names
  const namesSet = new Set(
    (Array.isArray(moduleNames.value) ? moduleNames.value : [])
      .map(n => (n ?? '').toString().trim().toLowerCase())
  )
  const filteredChildren = (defaultItems[0]?.children || []).filter(child => {
    const labelKey = (child.label ?? '').toString().trim().toLowerCase()
    return namesSet.has(labelKey)
  })
  return filteredChildren.length ? [{ label: 'Configuración', children: filteredChildren }] : []
})

const openGroups = ref({})
function isGroupOpen(i) {
  return !!openGroups.value[i]
}
function toggleGroup(i) {
  openGroups.value[i] = !openGroups.value[i]
}
function normalizeKey(label) {
  return (label || '').toString().trim().toLowerCase()
}

</script>
