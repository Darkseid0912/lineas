<template>
  <header class="bg-indigo-600 text-white">
    <div class="app-container flex items-center justify-between py-3">
      <nav class="space-x-4">
        <router-link to="/catalogo/botones" class="hover:underline">Botones</router-link>
        <router-link to="/catalogo/formularios" class="hover:underline">Formularios</router-link>
        <router-link to="/catalogo/loaders" class="hover:underline">Loaders</router-link>
        <router-link to="/catalogo/tablas" class="hover:underline">Tablas</router-link>
        <router-link to="/catalogo/paginados" class="hover:underline font-semibold">Paginados</router-link>
      </nav>
    </div>
  </header>
</template>

<script>
export default {
  name: 'Navbar',
  components: {}
}
</script>
