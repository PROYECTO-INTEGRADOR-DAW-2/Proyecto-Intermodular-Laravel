import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router/index.js'

import './style.css'
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'
import 'bootstrap-icons/font/bootstrap-icons.css';

const app = createApp(App)

const pinia = createPinia()
app.use(pinia)
app.use(router)

// Inicializar la autenticación si hay un token
import { useAuthStore } from './stores/authStore.js'

const authStore = useAuthStore(pinia)

if (localStorage.getItem('token')) {
    authStore.fetchUserAction()
}

app.mount('#app')
