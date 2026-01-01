<template>
  <section class="app-container py-6 space-y-8">
    <header>
      <h1 class="text-2xl font-bold">Catálogo de Paginados</h1>
      <p class="text-slate-600">Demostración del componente <code>Pagination</code> con UI/UX clara y accesible.</p>
    </header>

    <article class="rounded-lg border border-slate-200 p-5 bg-white space-y-4">

      <!-- Lista simulada para visualizar el rango -->
      <ul class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
        <li v-for="n in visibleItems" :key="`it-`+n" class="rounded-md border border-slate-200 p-3 text-sm text-slate-700">
          Ítem {{ n }}
        </li>
      </ul>

      <div class="pt-2">
        <Pagination
          v-model="page"
          :total="total"
          :pageSize="pageSize"
          :maxVisible="7"
          @change="onChange"
        />
      </div>
    </article>
  </section>
</template>

<script>
import { ref, computed } from 'vue'
import { Pagination } from '../../components/bases/paginados'

export default {
  name: 'PaginadosCatalog',
  components: { Pagination },
  setup() {
    const page = ref(1)
    const pageSize = ref(10)
    const total = ref(42)

    const start = computed(() => (page.value - 1) * pageSize.value)
    const end = computed(() => Math.min(total.value, start.value + pageSize.value))
    const visibleItems = computed(() => Array.from({ length: Math.max(0, end.value - start.value) }, (_, i) => start.value + i + 1))

    function onChange(next) {
      // Aquí podrías cargar datos remotos con la nueva página
      // console.log('page changed to', next)
    }

    return { page, pageSize, total, start, end, visibleItems, onChange }
  }
}
</script>
