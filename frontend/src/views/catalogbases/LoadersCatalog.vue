<template>
  <section class="app-container py-6 space-y-10">
    <header>
      <h1 class="text-2xl font-bold">Catálogo de Loaders</h1>
      <p class="text-slate-600">Ejemplos de loader pequeño para botones y loader de pantalla completa.</p>
    </header>

    <div class="grid gap-10 md:grid-cols-2">
      <!-- Loader para botones -->
      <article class="rounded-lg border border-slate-200 p-5 bg-white">
        <h2 class="text-lg font-semibold mb-3">Loader pequeño (botones)</h2>
        <div class="flex items-center gap-3">
          <BtnAccept :disabled="loadingBtn" @click="startBtnLoading">
            <span class="inline-flex items-center gap-2">
              <BtnSpinner v-if="loadingBtn" size="xs" />
              <span>{{ loadingBtn ? 'Procesando…' : 'Guardar' }}</span>
            </span>
          </BtnAccept>

          <button
            type="button"
            class="px-3 py-1.5 rounded-md border border-slate-300 hover:bg-slate-50"
            @click="loadingBtn = !loadingBtn"
          >
            Toggle
          </button>
        </div>
        <p class="text-sm text-slate-500 mt-3">Usa <code>BtnSpinner</code> dentro de tus botones base. Props: <code>size</code> (xs|sm), <code>colorClass</code>.</p>
      </article>

      <!-- Loader pantalla completa -->
      <article class="rounded-lg border border-slate-200 p-5 bg-white">
        <h2 class="text-lg font-semibold mb-3">Loader de pantalla completa</h2>
        <div class="flex items-center gap-3">
          <button
            type="button"
            class="px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700 disabled:opacity-50"
            :disabled="loadingFull"
            @click="showFull"
          >
            {{ loadingFull ? 'Cargando…' : 'Mostrar Loader' }}
          </button>
          <select v-model="size" class="border rounded-md px-2 py-1">
            <option value="md">md</option>
            <option value="lg">lg</option>
            <option value="xl">xl</option>
          </select>
        </div>
        <p class="text-sm text-slate-500 mt-3">Cubre toda la pantalla y bloquea interacciones mientras <code>show</code> es verdadero.</p>
      </article>
    </div>

    <FullscreenLoader :show="loadingFull" :message="message" :size="size" :zIndex="100" />
  </section>
</template>

<script>
import { BtnAccept } from '../../components/bases/botones'
import { BtnSpinner, FullscreenLoader } from '../../components/bases/loaders'

export default {
  name: 'LoadersCatalog',
  components: { BtnAccept, BtnSpinner, FullscreenLoader },
  data() {
    return {
      loadingBtn: false,
      loadingFull: false,
      message: 'Cargando…',
      size: 'xl'
    }
  },
  methods: {
    startBtnLoading() {
      if (this.loadingBtn) return
      this.loadingBtn = true
      setTimeout(() => {
        this.loadingBtn = false
      }, 1500)
    },
    showFull() {
      if (this.loadingFull) return
      this.loadingFull = true
      setTimeout(() => {
        this.loadingFull = false
      }, 2000)
    }
  }
}
</script>
