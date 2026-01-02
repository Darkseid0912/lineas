<template>
  <nav class="w-full border-b border-slate-200 bg-white">
    <div class="mx-auto max-w-7xl px-4 h-14 flex items-center justify-between">
      <div class="flex items-center gap-3">
         <!-- Mobile menu toggle -->
        <button
          class="md:hidden inline-flex items-center gap-2 rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm"
          @click="$emit('toggle-sidebar')"
          aria-label="Toggle sidebar"
        >
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-5 h-5">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
        </button>
        <router-link :to="brandTo" class="text-slate-800 font-semibold">{{ title }}</router-link>
       
      </div>
      <!-- Mobile logout button -->
      <div class="md:hidden ml-auto" v-if="items && items.some(isLogout)">
        <button
          type="button"
          @click="handleLogout"
          class="inline-flex items-center gap-2 rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm hover:bg-indigo-50 hover:text-indigo-700"
          aria-label="Cerrar sesión"
        >
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 12H18M18 12L15.5 9.77778M18 12L15.5 14.2222M18 7.11111V5C18 4.44772 17.5523 4 17 4H7C6.44772 4 6 4.44772 6 5V19C6 19.5523 6.44772 20 7 20H17C17.5523 20 18 19.5523 18 19V16.8889" stroke="#0F172A" stroke-linecap="round" stroke-linejoin="round"/></svg>
          <span class="sr-only">Cerrar sesión</span>
        </button>
      </div>
      <div class="hidden md:flex items-center gap-4 ml-auto">
            <template v-for="(item, idx) in items">
              <button
                v-if="isLogout(item)"
                :key="'logout-'+idx"
                type="button"
                @click="handleLogout"
                class="text-slate-600 hover:text-slate-800"
                aria-label="Cerrar sesión"
              >
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-slate-100 text-slate-600 hover:bg-indigo-100 hover:text-indigo-700">
                  <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M10 12H18M18 12L15.5 9.77778M18 12L15.5 14.2222M18 7.11111V5C18 4.44772 17.5523 4 17 4H7C6.44772 4 6 4.44772 6 5V19C6 19.5523 6.44772 20 7 20H17C17.5523 20 18 19.5523 18 19V16.8889" stroke="#000000" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                </span>
              </button>

              <router-link
                v-else
                :key="'item-'+idx"
                :to="item.to"
                class="text-slate-600 hover:text-slate-800"
              >
                <span>{{ item.label }}</span>
              </router-link>
            </template>
      </div>
      <div v-if="$slots.actions" class="flex items-center gap-2">
        <slot name="actions"></slot>
      </div>
    </div>
  </nav>
</template>
<script setup>
defineOptions({ name: 'NavBar' })
import { useRouter } from 'vue-router'
import { logout } from '../../services/auth'
defineProps({
  title: { type: String, default: 'App' },
  items: { type: Array, default: () => [] },
  brandTo: { type: String, default: '/principal' },
})
defineEmits(['toggle-sidebar'])
const router = useRouter()

function isLogout(item) {
  const label = (item?.label || '').toString().toLowerCase()
  return label.includes('cerrar')
}

async function handleLogout() {
  try {
    await logout()
  } catch {}
  router.push('/')
}
</script>
