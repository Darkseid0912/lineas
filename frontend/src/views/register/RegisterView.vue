<template>
  <section class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-sky-50 p-6">
    <FullscreenLoader :show="submitting" message="Guardando registro…" />

    <div class="max-w-6xl mx-auto space-y-6">
      <header class="flex items-start justify-between">
        <div>
          <h1 class="text-2xl font-semibold text-slate-800">Registro de empresa y administrador</h1>
          <p class="text-slate-600 mt-1">Completa los datos por pasos y guarda todo al final.</p>
        </div>
      </header>

      <!-- Step indicator -->
      <ol class="flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:gap-6">
        <li class="flex items-center gap-3">
          <span :class="['inline-flex h-8 w-8 items-center justify-center rounded-full text-sm', step === 1 ? 'bg-indigo-600 text-white' : 'bg-slate-200 text-slate-700']">1</span>
          <span :class="step === 1 ? 'font-medium text-indigo-700' : 'text-slate-600'">Datos de la empresa</span>
        </li>
        <li class="flex items-center gap-3">
          <span :class="['inline-flex h-8 w-8 items-center justify-center rounded-full text-sm', step === 2 ? 'bg-indigo-600 text-white' : 'bg-slate-200 text-slate-700']">2</span>
          <span :class="step === 2 ? 'font-medium text-indigo-700' : 'text-slate-600'">Usuario administrador</span>
        </li>
      </ol>

      <!-- Step 1: Company form -->
      <div v-if="step === 1" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-slate-800 font-semibold">Datos de la empresa</h2>
        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
          <InputText v-model="taxId" label="RFC / Tax ID" :maxlength="20" placeholder="Opcional" :error="companyFieldError('tax_id')" />
          <InputText v-model="businessName" label="Razón social" :required="true" :maxlength="200" :showCount="true" :error="companyFieldError('business_name')" />

          <InputText v-model="tradeName" label="Nombre comercial" :maxlength="200" :showCount="true" :error="companyFieldError('trade_name')" />
          <InputText v-model="companyEmail" label="Email" :required="true" :maxlength="150" placeholder="correo@empresa.com" :error="companyFieldError('email')" />

          <InputText v-model="phone" label="Teléfono" :maxlength="20" placeholder="Opcional" :error="companyFieldError('phone')" />
          <InputText v-model="city" label="Ciudad" :maxlength="100" placeholder="Opcional" :error="companyFieldError('city')" />

          <InputText v-model="country" label="País" :maxlength="100" placeholder="Opcional" :error="companyFieldError('country')" />
          <InputText v-model="address" label="Dirección" placeholder="Opcional" :error="companyFieldError('address')" />
        </div>

        <div class="mt-6 flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
          <div class="w-full sm:w-auto"><BtnSave class="w-full" @click="goNext">Siguiente</BtnSave></div>
          <div class="w-full sm:w-auto"><BtnCancel class="w-full" @click="resetCompany">Limpiar</BtnCancel></div>
        </div>

        <p v-if="companyError" class="mt-3 text-sm text-rose-600">{{ companyError }}</p>
      </div>

      <!-- Step 2: User form -->
      <div v-if="step === 2" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-slate-800 font-semibold">Datos del usuario administrador</h2>
        <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
          <div class="sm:col-span-2 lg:col-span-2">
            <p class="text-slate-600">Empresa seleccionada: <span class="font-medium">{{ businessName || '—' }}</span></p>
          </div>
          <!-- Company summary card -->
          <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 sm:col-span-2 lg:col-span-1">
            <h3 class="text-slate-700 font-medium">Resumen de empresa</h3>
            <dl class="mt-2 text-sm text-slate-600 space-y-1">
              <div class="flex justify-between"><dt>RFC/Tax ID</dt><dd class="font-medium">{{ taxId || '—' }}</dd></div>
              <div class="flex justify-between"><dt>Email</dt><dd class="font-medium">{{ companyEmail || '—' }}</dd></div>
              <div class="flex justify-between"><dt>Teléfono</dt><dd class="font-medium">{{ phone || '—' }}</dd></div>
              <div class="flex justify-between"><dt>Ciudad</dt><dd class="font-medium">{{ city || '—' }}</dd></div>
              <div class="flex justify-between"><dt>País</dt><dd class="font-medium">{{ country || '—' }}</dd></div>
            </dl>
          </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-4">
          <InputText v-model="userName" label="Nombre" :required="true" :maxlength="255" :showCount="true" :error="userFieldError('name')" />
          <InputText v-model="userEmail" label="Email" :required="true" :maxlength="255" placeholder="correo@dominio.com" :error="userFieldError('email')" />
          <InputPassword v-model="password" label="Contraseña" :required="true" :error="userFieldError('password')" />
          <InputPassword v-model="passwordConfirmation" label="Confirmar contraseña" :required="true" :error="userFieldError('password')" />
          <label class="inline-flex items-center gap-2 text-sm text-slate-600">
            <input type="checkbox" v-model="remember" class="rounded border-slate-300" />
            Mantener sesión iniciada
          </label>
        </div>

        <div class="mt-6 flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
          <div class="w-full sm:w-auto"><BtnCancel class="w-full" @click="goBack">Atrás</BtnCancel></div>
          <div class="w-full sm:w-auto"><BtnSave class="w-full" :disabled="submitting" @click="saveAll">Guardar</BtnSave></div>
          <div class="sm:w-auto"><BtnSpinner v-if="submitting" /></div>
        </div>

        <p v-if="userError" class="mt-3 text-sm text-rose-600">{{ userError }}</p>
        <p v-if="success" class="mt-3 text-sm text-emerald-600">{{ success }}</p>
      </div>
    </div>
  </section>
</template>
<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api, { clearToken } from '../../services/api'
import { setAuthSession, clearAuthSession } from '../../services/session'
import { InputText, InputPassword } from '../../components/bases/formularios'
import { BtnSave, BtnCancel } from '../../components/bases/botones'
import { BtnSpinner, FullscreenLoader } from '../../components/bases/loaders'

defineOptions({ name: 'RegisterView' })

// Step control
const step = ref(1)

// Company form state
const taxId = ref('')
const businessName = ref('')
const tradeName = ref('')
const companyEmail = ref('')
const phone = ref('')
const address = ref('')
const city = ref('')
const country = ref('')
const statusStr = ref('1')

const companyValidationErrors = ref({})
const companyError = ref('')

function companyFieldError(field) {
  const errs = companyValidationErrors.value?.[field]
  return Array.isArray(errs) ? errs[0] : ''
}

function resetCompany() {
  taxId.value = ''
  businessName.value = ''
  tradeName.value = ''
  companyEmail.value = ''
  phone.value = ''
  address.value = ''
  city.value = ''
  country.value = ''
  statusStr.value = '1'
  companyValidationErrors.value = {}
  companyError.value = ''
}

function goNext() {
  companyError.value = ''
  companyValidationErrors.value = {}
  if (!businessName.value.trim()) {
    companyValidationErrors.value = { business_name: ['La razón social es requerida'] }
    return
  }
  if (!companyEmail.value.trim()) {
    companyValidationErrors.value = { email: ['El email es requerido'] }
    return
  }
  step.value = 2
  if (!userEmail.value) userEmail.value = companyEmail.value
}

function goBack() {
  step.value = 1
}

// User form state
const userName = ref('')
const userEmail = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const remember = ref(true)

const userValidationErrors = ref({})
const userError = ref('')
const success = ref('')

function userFieldError(field) {
  const errs = userValidationErrors.value?.[field]
  return Array.isArray(errs) ? errs[0] : ''
}

function resetUser() {
  userName.value = ''
  userEmail.value = ''
  password.value = ''
  passwordConfirmation.value = ''
  remember.value = true
  userValidationErrors.value = {}
  userError.value = ''
  success.value = ''
}

// Save sequence
const submitting = ref(false)
const router = useRouter()

async function saveAll() {
  userError.value = ''
  success.value = ''
  userValidationErrors.value = {}

  if (!userName.value.trim()) {
    userValidationErrors.value = { name: ['El nombre es requerido'] }
    return
  }
  if (!userEmail.value.trim()) {
    userValidationErrors.value = { email: ['El email es requerido'] }
    return
  }
  if (!password.value || !passwordConfirmation.value) {
    userValidationErrors.value = { password: ['La contraseña y confirmación son requeridas'] }
    return
  }

  // Ensure passwords match before any server calls
  if (password.value !== passwordConfirmation.value) {
    userValidationErrors.value = { password: ['Las contraseñas no coinciden'] }
    return
  }

  submitting.value = true
  try {
    // Atomic call: company + admin user
    const payload = {
      // Company
      tax_id: taxId.value?.trim() || null,
      business_name: businessName.value.trim(),
      trade_name: tradeName.value?.trim() || null,
      email: companyEmail.value.trim(),
      phone: phone.value?.trim() || null,
      address: address.value?.trim() || null,
      city: city.value?.trim() || null,
      country: country.value?.trim() || null,
      status: statusStr.value?.trim() === '0' ? 0 : 1,
      // User
      name: userName.value.trim(),
      user_email: userEmail.value.trim(),
      password: password.value,
      password_confirmation: passwordConfirmation.value,
    }
    const { data } = await api.post('/register/company-admin', payload)

    // 3) Persist token and set session
    clearToken()
    clearAuthSession()
    if (data?.token) {
      try {
        if (remember.value) {
          window.localStorage.setItem('auth_token', data.token)
          window.sessionStorage.removeItem('auth_token')
        } else {
          window.sessionStorage.setItem('auth_token', data.token)
          window.localStorage.removeItem('auth_token')
        }
      } catch {}
    }
    // typeText is Admin by flow
    setAuthSession({ user: data?.user ?? null, typeText: 'Administrador' })

    success.value = 'Registro completado, sesión iniciada'
    router.push('/principal')
  } catch (err) {
    // Try to map validation errors based on which call failed
    const respErrs = err?.response?.data?.errors
    if (respErrs) {
      // Heuristic: if company fields exist, map to company; else to user
      if (respErrs.business_name || respErrs.email || respErrs.tax_id) {
        companyValidationErrors.value = respErrs
        step.value = 1
        const companyMsg = respErrs.business_name?.[0]
          || respErrs.email?.[0]
          || respErrs.tax_id?.[0]
          || respErrs.phone?.[0]
          || respErrs.address?.[0]
          || respErrs.city?.[0]
          || respErrs.country?.[0]
        companyError.value = companyMsg || (err?.response?.data?.message) || 'Error al guardar empresa'
      } else {
        userValidationErrors.value = respErrs
        const userMsg = respErrs.user_email?.[0]
          || respErrs.email?.[0]
          || respErrs.name?.[0]
          || respErrs.password?.[0]
        userError.value = userMsg || (err?.response?.data?.message) || 'Error al registrar usuario'
      }
    } else {
      userError.value = err?.response?.data?.message || 'No fue posible completar el registro'
    }
  } finally {
    submitting.value = false
  }
}
</script>
