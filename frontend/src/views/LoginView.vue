<script setup>
import { useForm, useField } from 'vee-validate'
import { object, string } from 'yup'
import { useAuthStore } from '../stores/auth'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { ref } from 'vue'

const authStore = useAuthStore()
const router = useRouter()
const serverError = ref('')
const googleLoading = ref(false)

// ── Yup schema ────────────────────────────────────────────────────────────────
const schema = object({
    email: string().required('El email és obligatori').email('Format d\'email invàlid'),
    password: string().required('La contrasenya és obligatòria').min(6, 'Mínim 6 caràcters'),
})

// ── Vee-Validate form ─────────────────────────────────────────────────────────
const { handleSubmit, isSubmitting } = useForm({ validationSchema: schema })
const { value: email, errorMessage: emailError, meta: emailMeta } = useField('email')
const { value: password, errorMessage: passwordError, meta: passwordMeta } = useField('password')

// ── Submit ────────────────────────────────────────────────────────────────────
const handleLogin = handleSubmit(async (values) => {
    serverError.value = ''
    try {
        await authStore.login({ email: values.email, password: values.password })
        router.push('/')
    } catch (error) {
        serverError.value = error.response?.data?.message ?? 'Credencials incorrectes o error en el servidor.'
    }
})

// ── Google OAuth ──────────────────────────────────────────────────────────────
const loginWithGoogle = async () => {
    googleLoading.value = true
    try {
        const response = await axios.get('/api/oauth/google/redirect')
        window.location.href = response.data.url
    } catch (error) {
        serverError.value = 'No s\'ha pogut connectar amb Google. Torna-ho a intentar.'
        googleLoading.value = false
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

                <div v-if="serverError" class="alert alert-danger">{{ serverError }}</div>

                <form @submit.prevent="handleLogin" novalidate>
                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope"></i></span>
                            <input
                                type="email"
                                id="email"
                                v-model="email"
                                placeholder="name@example.com"
                                class="form-control border-start-0 ps-0 bg-light"
                                :class="{ 'is-invalid': emailMeta.touched && emailError, 'is-valid': emailMeta.touched && !emailError }"
                            >
                            <div class="invalid-feedback">{{ emailError }}</div>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label for="password" class="form-label fw-bold">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock"></i></span>
                            <input
                                type="password"
                                id="password"
                                v-model="password"
                                placeholder="********"
                                class="form-control border-start-0 ps-0 bg-light"
                                :class="{ 'is-invalid': passwordMeta.touched && passwordError, 'is-valid': passwordMeta.touched && !passwordError }"
                            >
                            <div class="invalid-feedback">{{ passwordError }}</div>
                        </div>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-dark btn-lg fw-bold" :disabled="isSubmitting">
                            <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-2"></span>
                            Iniciar Sesión
                        </button>
                    </div>

                    <div class="text-center">
                        <p class="text-muted mb-0">¿No tienes cuenta? <router-link :to="{ name: 'register' }" class="text-danger fw-bold text-decoration-none">Regístrate aquí</router-link></p>
                    </div>
                </form>

                <div class="my-3 d-flex align-items-center">
                    <hr class="flex-grow-1">
                    <span class="px-3 text-muted small">o continúa con</span>
                    <hr class="flex-grow-1">
                </div>

                <button
                    @click="loginWithGoogle"
                    :disabled="googleLoading"
                    class="btn w-100 d-flex align-items-center justify-content-center gap-2 fw-semibold"
                    style="border: 1.5px solid #dadce0; background: #fff; color: #3c4043;"
                >
                    <span v-if="googleLoading" class="spinner-border spinner-border-sm"></span>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 48 48">
                        <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                        <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                        <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                        <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                    </svg>
                    {{ googleLoading ? 'Redirigiendo...' : 'Iniciar sesión con Google' }}
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.input-group-text { border-color: #dee2e6; }
.form-control:focus { box-shadow: none; border-color: #dee2e6; }
.input-group:focus-within {
    box-shadow: 0 0 0 0.25rem rgba(33, 37, 41, 0.25);
    border-radius: 0.375rem;
}
.input-group:focus-within .input-group-text,
.input-group:focus-within .form-control { border-color: #212529; }
</style>
