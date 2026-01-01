<template>
  <div class="min-h-screen bg-gray-100">
    <Navbar />
    <main class="app-container">
      <router-view />
    </main>
  </div>
</template>

<script>
import Navbar from '../components/bases/Navbar.vue'

export default {
  name: 'DefaultLayout',
  components: { Navbar }
}
</script>
