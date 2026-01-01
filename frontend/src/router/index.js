import { createRouter, createWebHistory } from 'vue-router'
import { readToken, clearToken } from '../services/api'
import { clearAuthSession, setAuthSession } from '../services/session'
import { me } from '../services/auth'
import ButtonsCatalog from '../views/catalogbases/ButtonsCatalog.vue'
import FormCatalog from '../views/catalogbases/FormCatalog.vue'
import LoadersCatalog from '../views/catalogbases/LoadersCatalog.vue'
import TablasCatalog from '../views/catalogbases/TablasCatalog.vue'
import PaginadosCatalog from '../views/catalogbases/PaginadosCatalog.vue'
import LoginView from '../views/login/LoginView.vue'
import CatalogoLayout from '../layouts/CatalogoLayout.vue'
import PrincipalView from '../views/principal/PrincipalView.vue'
import RegisterView from '../views/register/RegisterView.vue'

const routes = [
  // Ruta pública sin Navbar
  { path: '/', name: 'Login', component: LoginView },
  { path: '/register', name: 'Register', component: RegisterView },
  // Rutas de catálogos con Navbar (layout)
  {
    path: '/catalogo',
    meta: { requiresAuth: true },
    component: CatalogoLayout,
    children: [
      { path: '', redirect: '/catalogo/botones' },
      { path: 'botones', name: 'Botones', component: ButtonsCatalog },
      { path: 'formularios', name: 'Formularios', component: FormCatalog },
      { path: 'loaders', name: 'Loaders', component: LoadersCatalog },
      { path: 'tablas', name: 'Tablas', component: TablasCatalog },
      { path: 'paginados', name: 'Paginados', component: PaginadosCatalog }
    ]
  },
  // Nueva vista principal en blanco (protegida)
  { path: '/principal', name: 'Principal', component: PrincipalView, meta: { requiresAuth: true } }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach(async (to, from, next) => {
  const needsAuth = to.matched.some(record => record.meta?.requiresAuth)
  if (!needsAuth) return next()

  const token = readToken()
  if (!token) {
    return next({ path: '/', query: { redirect: to.fullPath } })
  }

  try {
    const data = await me() // validate token with backend
    // Populate in-memory session with server data (user + type_text if available)
    const typeText = data?.type_text ?? data?.type_catalog?.name ?? null
    const companyTradeName = data?.company_trade_name ?? data?.company?.trade_name ?? null
    const companyId = data?.company_id ?? data?.company?.id ?? data?.id_companies ?? null
    setAuthSession({ user: data ?? null, typeText, companyTradeName, companyId })
    return next()
  } catch (err) {
    // invalid/expired token: clear and redirect to login
    clearToken()
    clearAuthSession()
    return next({ path: '/', query: { redirect: to.fullPath } })
  }
})

export default router
