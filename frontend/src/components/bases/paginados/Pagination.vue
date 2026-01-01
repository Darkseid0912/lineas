<template>
  <nav
    class="flex items-center justify-between gap-3 text-sm"
    role="navigation"
    aria-label="Paginación"
  >
    <!-- Summary (optional) -->
    <div v-if="showSummary" class="hidden md:block text-slate-600">
      Mostrando {{ startIndex + 1 }}–{{ endIndex }} de {{ total }}
    </div>

    <ul class="flex items-center gap-1 select-none">
      <!-- First -->
      <li v-if="showFirstLast">
        <button
          type="button"
          class="px-2.5 py-1.5 rounded-md border border-slate-300 bg-white hover:bg-slate-50 disabled:opacity-50"
          :disabled="pageComputed === 1 || disabled"
          :aria-label="ariaFirst"
          @click="goTo(1)"
        >
          «
        </button>
      </li>
      <!-- Prev -->
      <li>
        <button
          type="button"
          class="px-2.5 py-1.5 rounded-md border border-slate-300 bg-white hover:bg-slate-50 disabled:opacity-50"
          :disabled="pageComputed === 1 || disabled"
          :aria-label="ariaPrev"
          @click="goTo(pageComputed - 1)"
        >
          ‹
        </button>
      </li>

      <!-- Numbered pages with ellipsis -->
      <li v-for="item in items" :key="item.key">
        <span v-if="item.type === 'ellipsis'" class="px-2.5 py-1.5 text-slate-500">…</span>
        <button
          v-else
          type="button"
          :aria-current="item.page === pageComputed ? 'page' : undefined"
          :class="[
            'px-3 py-1.5 rounded-md border text-sm',
            item.page === pageComputed
              ? 'border-indigo-600 bg-indigo-600 text-white'
              : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-50',
          ]"
          :disabled="disabled"
          @click="goTo(item.page)"
        >
          {{ item.page }}
        </button>
      </li>

      <!-- Next -->
      <li>
        <button
          type="button"
          class="px-2.5 py-1.5 rounded-md border border-slate-300 bg-white hover:bg-slate-50 disabled:opacity-50"
          :disabled="pageComputed >= totalPages || disabled"
          :aria-label="ariaNext"
          @click="goTo(pageComputed + 1)"
        >
          ›
        </button>
      </li>
      <!-- Last -->
      <li v-if="showFirstLast">
        <button
          type="button"
          class="px-2.5 py-1.5 rounded-md border border-slate-300 bg-white hover:bg-slate-50 disabled:opacity-50"
          :disabled="pageComputed >= totalPages || disabled"
          :aria-label="ariaLast"
          @click="goTo(totalPages)"
        >
          »
        </button>
      </li>
    </ul>
  </nav>
</template>

<script setup>
import { computed, watch, ref } from 'vue'

const props = defineProps({
  modelValue: { type: Number, default: 1 }, // current page
  total: { type: Number, required: true },  // total items
  pageSize: { type: Number, default: 10 },
  maxVisible: { type: Number, default: 5 }, // how many page buttons
  disabled: { type: Boolean, default: false },
  showFirstLast: { type: Boolean, default: true },
  showSummary: { type: Boolean, default: true },

  ariaFirst: { type: String, default: 'Primera página' },
  ariaPrev: { type: String, default: 'Página anterior' },
  ariaNext: { type: String, default: 'Página siguiente' },
  ariaLast: { type: String, default: 'Última página' }
})

const emit = defineEmits(['update:modelValue', 'change'])

const totalPages = computed(() => Math.max(1, Math.ceil(props.total / props.pageSize)))
const pageComputed = computed(() => Math.min(Math.max(1, props.modelValue || 1), totalPages.value))

watch(() => props.modelValue, (p) => {
  if (p !== pageComputed.value) emit('update:modelValue', pageComputed.value)
})

function goTo(p) {
  const next = Math.min(Math.max(1, p), totalPages.value)
  if (next === props.modelValue) return
  emit('update:modelValue', next)
  emit('change', next)
}

const items = computed(() => {
  const pages = totalPages.value
  const max = Math.max(3, props.maxVisible)
  const current = pageComputed.value

  if (pages <= max) {
    return Array.from({ length: pages }, (_, i) => ({ type: 'page', key: `p-${i+1}` , page: i + 1 }))
  }

  const out = []
  const half = Math.floor((max - 3) / 2) // room around current
  const start = Math.max(2, current - half)
  const end = Math.min(pages - 1, start + (max - 3))
  const adjStart = Math.max(2, end - (max - 3))

  out.push({ type: 'page', key: 'p-1', page: 1 })
  if (adjStart > 2) out.push({ type: 'ellipsis', key: 'e-start' })

  for (let p = adjStart; p <= end; p++) {
    out.push({ type: 'page', key: `p-${p}`, page: p })
  }

  if (end < pages - 1) out.push({ type: 'ellipsis', key: 'e-end' })
  out.push({ type: 'page', key: `p-${pages}`, page: pages })

  return out
})

const startIndex = computed(() => (pageComputed.value - 1) * props.pageSize)
const endIndex = computed(() => Math.min(props.total, startIndex.value + props.pageSize))
</script>
