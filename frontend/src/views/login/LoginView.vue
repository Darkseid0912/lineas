<template>
  <section class="relative min-h-screen bg-gradient-to-br from-indigo-50 via-white to-sky-50 flex items-center overflow-hidden">
    <!-- Blurred background image (place file at /public/images/login-bg.jpg) -->
    <div class="absolute inset-0 z-0" aria-hidden="true">
      <div
        class="absolute inset-0 bg-cover bg-center bg-no-repeat blur-md scale-110"
        style="background-image: url('/images/rutas.avif')"
      ></div>
    </div>
    <div class="relative z-10 mx-auto w-full max-w-6xl px-4 py-10 md:py-16">
      <div class="grid place-items-center gap-8">
        <!-- Auth card -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm md:p-10">
          <div class="w-full max-w-sm mx-auto">
            <h1 class="text-xl font-semibold text-slate-800 mb-1">Iniciar sesión</h1>
            <p class="text-sm text-slate-600 mb-6">Ingresa tus credenciales para acceder a tu cuenta.</p>

            <LoginForm :loading="loading" :error="error" @submit="onSubmit" />

            <div class="mt-6 flex items-center justify-between text-xs text-slate-500">
              <span>¿No tienes cuenta? <a href="/register" class="text-indigo-600 hover:text-indigo-700">Crear cuenta</a></span>
              <a href="#" class="hover:underline">Términos y privacidad</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { LoginForm } from '../../components/auth'
import { login } from '../../services/auth'

defineOptions({ name: 'LoginView' })

const loading = ref(false)
const error = ref('')
const router = useRouter()

function onSubmit({ email, password, remember }) {
  error.value = ''
  loading.value = true
  login({ email, password, remember })
    .then(() => {
      router.push('/principal')
    })
    .catch((err) => {
      const msg = err?.response?.data?.message || 'No fue posible iniciar sesión'
      const fieldErrors = err?.response?.data?.errors
      error.value = fieldErrors ? Object.values(fieldErrors).flat().join(' ') : msg
    })
    .finally(() => {
      loading.value = false
    })
}
</script>
