<template>
  <form class="space-y-4" @submit.prevent="onSubmit" novalidate>
    <InputEmail
      v-model="email"
      :label="emailLabel"
      :placeholder="emailPlaceholder"
      :autovalidate="true"
      :error="emailError"
      name="email"
      id="login-email"
      autocomplete="username"
    />

    <InputPassword
      v-model="password"
      :label="passwordLabel"
      :placeholder="passwordPlaceholder"
      :error="passwordError"
    />

    <div class="flex items-center justify-between">
      <label class="inline-flex items-center gap-2 text-sm text-slate-700 select-none">
        <input type="checkbox" v-model="remember" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-600" />
        Recuérdame
      </label>
      <slot name="aux-actions">
        <button type="button" class="text-sm text-indigo-600 hover:text-indigo-700">¿Olvidaste tu contraseña?</button>
      </slot>
    </div>

    <p v-if="error" class="text-sm text-rose-600" role="alert" aria-live="polite">{{ error }}</p>

    <div class="pt-2">
      <BtnAccept :disabled="loading || !canSubmit" type="submit" aria-label="Iniciar sesión">
        <span class="inline-flex items-center gap-2">
          <BtnSpinner v-if="loading" size="xs" />
          <span>{{ loading ? submitLoadingText : submitText }}</span>
        </span>
      </BtnAccept>
    </div>
  </form>
</template>

<script setup>
import { ref, computed } from 'vue'
import { InputEmail, InputPassword } from '../bases/formularios'
import { BtnAccept } from '../bases/botones'
import { BtnSpinner } from '../bases/loaders'

const props = defineProps({
  loading: { type: Boolean, default: false },
  error: { type: String, default: '' },
  submitText: { type: String, default: 'Entrar' },
  submitLoadingText: { type: String, default: 'Entrando…' },
  emailLabel: { type: String, default: 'Correo' },
  emailPlaceholder: { type: String, default: 'tu@correo.com' },
  passwordLabel: { type: String, default: 'Contraseña' },
  passwordPlaceholder: { type: String, default: '••••••••' }
})

const emit = defineEmits(['submit'])

const email = ref('')
const password = ref('')
const remember = ref(false)

const emailError = computed(() => '')
const passwordError = computed(() => (password.value ? '' : 'La contraseña es requerida'))

const isEmailValid = computed(() => {
  if (!email.value) return false
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return re.test(email.value)
})

const canSubmit = computed(() => isEmailValid.value && Boolean(password.value))

function onSubmit() {
  if (!canSubmit.value) return
  emit('submit', { email: email.value, password: password.value, remember: remember.value })
}
</script>
