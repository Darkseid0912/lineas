import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import './assets/index.css'
import { readToken, clearToken } from './services/api'
import { me } from './services/auth'
import { setAuthSession, clearAuthSession } from './services/session'
async function bootstrap() {
	try {
		if (readToken()) {
			const data = await me()
			const typeText = data?.type_text ?? data?.type_catalog?.name ?? null
			setAuthSession({ user: data ?? null, typeText })
		}
	} catch {
		// Token invalid/revoked: clear and stay on login
		clearToken()
		clearAuthSession()
	}

	const app = createApp(App)
	app.use(router)
	app.mount('#app')
}

bootstrap()
