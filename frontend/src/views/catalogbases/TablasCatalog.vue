<template>
  <section class="app-container py-6 space-y-8">
    <header>
      <h1 class="text-2xl font-bold">Catálogo de Tablas</h1>
      <p class="text-slate-600">Demostración de `DataTable`</p>
    </header>

    <article class="rounded-lg border border-slate-200 p-5 bg-white">

      <DataTable
        :columns="columns"
        :rows="rows"
        rowKey="id"
        :loading="loading"
        emptyText="Sin resultados"
        @view="onView"
        @edit="onEdit"
        @delete="onDelete"
        @sort="onSort"
      >
        <!-- Celda custom para rol -->
        <template #{"cell:role"}="{ value }">
          <span class="px-2 py-0.5 rounded text-xs font-medium"
                :class="value === 'Admin' ? 'text-amber-700 bg-amber-50' : 'text-slate-700 bg-slate-100'">
            {{ value }}
          </span>
        </template>
      </DataTable>
    </article>
  </section>
</template>

<script>
import { ref } from 'vue'
import { DataTable } from '../../components/bases/tablas'

export default {
  name: 'TablasCatalog',
  components: { DataTable },
  setup() {
    const columns = [
      { key: 'id', label: 'ID', width: '70px', sortable: true, class: 'text-slate-500' },
      { key: 'name', label: 'Nombre', sortable: true },
      { key: 'email', label: 'Correo', sortable: true },
      { key: 'role', label: 'Rol', sortable: true },
      { key: 'active', label: 'Activo', sortable: true }
    ]

    const rows = ref([
      { id: 1, name: 'Ana', email: 'ana@acme.com', role: 'Admin', active: true },
      { id: 2, name: 'Luis', email: 'luis@acme.com', role: 'Editor', active: false },
      { id: 3, name: 'Marta', email: 'marta@acme.com', role: 'Viewer', active: true },
      { id: 4, name: 'Pedro', email: 'pedro@acme.com', role: 'Editor', active: true }
    ])

    const loading = ref(false)

    function onView(row) { /* muestra modal o detalle */ console.log('view', row) }
    function onEdit(row) { console.log('edit', row) }
    function onDelete(row) { console.log('delete', row) }
    function onSort({ sortBy, sortDir }) { console.log('sort', sortBy, sortDir) }

    return { columns, rows, loading, onView, onEdit, onDelete, onSort }
  }
}
</script>
