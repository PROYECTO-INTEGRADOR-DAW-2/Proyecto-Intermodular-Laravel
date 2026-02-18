<script setup>
import { ref } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useRouter } from 'vue-router'

const email = ref('')
const password = ref('')
const authStore = useAuthStore()
const router = useRouter()
const errorMessage = ref('')

const handleLogin = async () => {
    try {
        await authStore.login({ email: email.value, password: password.value })
        router.push('/')
    } catch (error) {
        errorMessage.value = 'Credenciales incorrectas o error en el servidor.'
    }
}
</script>

<template>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-lg border-0 rounded-4" style="max-width: 450px; width: 100%;">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <img src="/img/logo.png" alt="Logo" class="img-fluid mb-3" style="max-height: 80px;">
                    <h3 class="fw-bold">Bienvenido de nuevo</h3>
                    <p class="text-muted">Ingresa a tu cuenta para continuar</p>
                </div>

                <div v-if="errorMessage" class="alert alert-danger">{{ errorMessage }}</div>

                <form @submit.prevent="handleLogin">
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">Email</label>
                        <div class="input-group">
                             <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope"></i></span>
                             <input type="email" class="form-control border-start-0 ps-0 bg-light" id="email" v-model="email" placeholder="name@example.com" required>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="form-label fw-bold">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control border-start-0 ps-0 bg-light" id="password" v-model="password" placeholder="********" required>
                        </div>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-dark btn-lg fw-bold">Iniciar Sesión</button>
                    </div>

                    <div class="text-center">
                        <p class="text-muted mb-0">¿No tienes cuenta? <router-link :to="{ name: 'register' }" class="text-danger fw-bold text-decoration-none">Regístrate aquí</router-link></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped>
.input-group-text {
    border-color: #dee2e6;
}
.form-control:focus {
    box-shadow: none;
    border-color: #dee2e6;
}
.input-group:focus-within {
    box-shadow: 0 0 0 0.25rem rgba(33, 37, 41, 0.25);
    border-radius: 0.375rem;
}
.input-group:focus-within .input-group-text,
.input-group:focus-within .form-control {
    border-color: #212529;
}
</style>
