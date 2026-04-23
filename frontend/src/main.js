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

// El router se encargará de inicializar la autenticación en el beforeEach
app.mount('#app')
