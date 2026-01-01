<template>
  <section class="w-full">    

    <!-- Table wrapper -->
    <div class="relative overflow-x-auto border border-slate-200 rounded-md bg-white">
      <!-- Loading overlay -->
      <div v-if="loading" class="absolute inset-0 bg-white/70 backdrop-blur-[1px] z-10 flex items-center justify-center">
        <div class="flex items-center gap-3 text-slate-600">
          <BtnSpinner :show="true" size="sm" colorClass="text-indigo-600" />
          <span class="font-medium">{{ loadingText }}</span>
        </div>
      </div>

      <table class="w-full text-left text-sm">
        <thead class="sticky top-0 z-[1] bg-slate-50 text-slate-700">
          <tr>
            <th v-if="selectable" class="px-3 py-2 w-10">
              <input type="checkbox" :checked="allVisibleSelected" @change="toggleSelectAll($event.target.checked)" />
            </th>
            <th
              v-for="col in columns"
              :key="col.key"
              :class="['px-3 py-2 font-semibold', col.class]"
              :style="{ width: col.width || undefined }"
            >
              <button
                v-if="col.sortable"
                type="button"
                class="inline-flex items-center gap-1 hover:text-indigo-600"
                @click="toggleSort(col.key)"
              >
                <span>{{ col.label }}</span>
                <svg v-if="sortBy === col.key && sortDir === 'asc'" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M3 12l7-7 7 7"/></svg>
                <svg v-else-if="sortBy === col.key && sortDir === 'desc'" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M3 8l7 7 7-7"/></svg>
                <svg v-else class="h-4 w-4 opacity-40" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M7 7l3-3 3 3M7 13l3 3 3-3"/></svg>
              </button>
              <span v-else>{{ col.label }}</span>
            </th>
            <th v-if="hasActions" class="px-3 py-2 text-right w-32">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="!filteredSortedRows.length && !loading">
            <td :colspan="columns.length + (selectable ? 1 : 0) + (hasActions ? 1 : 0)" class="px-4 py-10 text-center text-slate-500">
              {{ emptyText }}
            </td>
          </tr>

          <tr
            v-for="row in rowsToRender"
            :key="row[rowKey] ?? JSON.stringify(row)"
            class="border-t border-slate-100 hover:bg-slate-50"
          >
            <td v-if="selectable" class="px-3 py-2">
              <input type="checkbox" :value="row[rowKey]" v-model="selectedKeysLocal" @change="emitSelection" />
            </td>

            <td
              v-for="col in columns"
              :key="col.key + String(row[rowKey])"
              :class="['px-3 py-2', col.tdClass]"
            >
              <slot :name="`cell:${col.key}`" :row="row" :value="row[col.key]">
                <span class="text-slate-700">{{ formatCell(row[col.key]) }}</span>
              </slot>
            </td>

            <td v-if="hasActions" class="px-3 py-2">
              <div class="flex items-center justify-end gap-1.5">
                <button
                  v-if="showView"
                  class="p-1.5 rounded-md hover:bg-slate-100 text-emerald-600"
                  title="Ver"
                  @click="$emit('view', row)"
                >
                  <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
                <button
                  v-if="showEdit"
                  class="p-1.5 rounded-md hover:bg-slate-100 text-amber-600"
                  title="Editar"
                  @click="$emit('edit', row)"
                >
                  <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                </button>
                <button
                  v-if="showDelete"
                  class="p-1.5 rounded-md hover:bg-slate-100 text-rose-600"
                  title="Eliminar"
                  @click="$emit('delete', row)"
                >
                  <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/></svg>
                </button>
                <slot name="row-actions" :row="row" />
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    
  </section>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { BtnSpinner } from '../loaders'

const props = defineProps({
  columns: { type: Array, default: () => [] },
  rows: { type: Array, default: () => [] },
  rowKey: { type: String, default: 'id' },

  

  loading: { type: Boolean, default: false },
  loadingText: { type: String, default: 'Cargando…' },

  selectable: { type: Boolean, default: false },
  selectedKeys: { type: Array, default: () => [] },

  showView: { type: Boolean, default: true },
  showEdit: { type: Boolean, default: true },
  showDelete: { type: Boolean, default: true },

  hasActions: { type: Boolean, default: true },
  emptyText: { type: String, default: 'No hay registros' }
})

const emit = defineEmits([
  'update:page',
  'update:pageSize',
  'update:selectedKeys',
  'sort',
  'view', 'edit', 'delete',
  'selection-change'
])

// no pagination state

const sortBy = ref(null)
const sortDir = ref('asc')

const selectedKeysLocal = ref([...props.selectedKeys])
watch(() => props.selectedKeys, v => { selectedKeysLocal.value = [...v] })

const normalizedColumns = computed(() => props.columns.map(c => ({ sortable: false, align: 'left', ...c })))

const filteredSortedRows = computed(() => {
  let out = props.rows
  // sort
  if (sortBy.value) {
    const key = sortBy.value
    const dir = sortDir.value === 'asc' ? 1 : -1
    out = [...out].sort((a, b) => {
      const av = a[key]
      const bv = b[key]
      if (av == null && bv == null) return 0
      if (av == null) return -1 * dir
      if (bv == null) return 1 * dir
      if (typeof av === 'number' && typeof bv === 'number') return (av - bv) * dir
      return String(av).localeCompare(String(bv)) * dir
    })
  }
  return out
})

const rowsToRender = computed(() => filteredSortedRows.value)

const allVisibleSelected = computed(() => {
  if (!props.selectable) return false
  const keys = rowsToRender.value.map(r => r[props.rowKey])
  return keys.length > 0 && keys.every(k => selectedKeysLocal.value.includes(k))
})

function toggleSelectAll(checked) {
  const keys = rowsToRender.value.map(r => r[props.rowKey])
  if (checked) {
    const set = new Set([...selectedKeysLocal.value, ...keys])
    selectedKeysLocal.value = Array.from(set)
  } else {
    selectedKeysLocal.value = selectedKeysLocal.value.filter(k => !keys.includes(k))
  }
  emitSelection()
}

function emitSelection() {
  emit('update:selectedKeys', selectedKeysLocal.value)
  emit('selection-change', selectedKeysLocal.value)
}

function toggleSort(key) {
  if (sortBy.value === key) {
    sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortBy.value = key
    sortDir.value = 'asc'
  }
  emit('sort', { sortBy: sortBy.value, sortDir: sortDir.value })
}

// pagination removed

function formatCell(val) {
  if (val == null) return ''
  if (val === true) return 'Sí'
  if (val === false) return 'No'
  return val
}
</script>
