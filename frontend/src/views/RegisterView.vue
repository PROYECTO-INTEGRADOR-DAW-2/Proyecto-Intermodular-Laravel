<script setup>
import { useForm, useField } from 'vee-validate'
import { object, string, ref as yupRef } from 'yup'
import { useAuthStore } from '../stores/auth'
import { useRouter } from 'vue-router'
import { ref } from 'vue'

const authStore = useAuthStore()
const router = useRouter()
const serverError = ref('')

// ── Yup schema ────────────────────────────────────────────────────────────────
const schema = object({
    name: string()
        .required('El nom és obligatori')
        .min(2, 'Mínim 2 caràcters'),
    email: string()
        .required('El email és obligatori')
        .email('Format d\'email invàlid'),
    password: string()
        .required('La contrasenya és obligatòria')
        .min(6, 'Mínim 6 caràcters'),
    password_confirmation: string()
        .required('Confirma la contrasenya')
        .oneOf([yupRef('password')], 'Les contrasenyes no coincideixen'),
})

// ── Vee-Validate form ─────────────────────────────────────────────────────────
const { handleSubmit, isSubmitting } = useForm({ validationSchema: schema })

const { value: name, errorMessage: nameError, meta: nameMeta } = useField('name')
const { value: email, errorMessage: emailError, meta: emailMeta } = useField('email')
const { value: password, errorMessage: passwordError, meta: passwordMeta } = useField('password')
const { value: passwordConfirmation, errorMessage: passwordConfirmationError, meta: passwordConfirmationMeta } = useField('password_confirmation')

// ── Submit ────────────────────────────────────────────────────────────────────
const handleRegister = handleSubmit(async (values) => {
    serverError.value = ''
    try {
        await authStore.register({
            name: values.name,
            email: values.email,
            password: values.password,
            password_confirmation: values.password_confirmation,
        })
        router.push('/')
    } catch (error) {
        if (error.response?.data?.errors) {
            serverError.value = Object.values(error.response.data.errors).flat().join(' ')
        } else {
            serverError.value = 'Error al registrarse. Inténtalo de nuevo.'
        }
    }
})
</script>

<template>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-lg border-0 rounded-4" style="max-width: 450px; width: 100%;">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <img src="/img/logo.png" alt="Logo" class="img-fluid mb-3" style="max-height: 80px;">
                    <h3 class="fw-bold">Crear Cuenta</h3>
                    <p class="text-muted">Únete a J&A Sports hoy mismo</p>
                </div>

                <div v-if="serverError" class="alert alert-danger">{{ serverError }}</div>

                <form @submit.prevent="handleRegister" novalidate>
                    <!-- Nombre -->
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold">Nombre</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-person"></i></span>
                            <input
                                type="text"
                                id="name"
                                v-model="name"
                                placeholder="Tu nombre"
                                class="form-control border-start-0 ps-0 bg-light"
                                :class="{ 'is-invalid': nameMeta.touched && nameError, 'is-valid': nameMeta.touched && !nameError }"
                            >
                            <div class="invalid-feedback">{{ nameError }}</div>
                        </div>
                    </div>

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
                    <div class="mb-3">
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

                    <!-- Confirm Password -->
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label fw-bold">Confirmar Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock-fill"></i></span>
                            <input
                                type="password"
                                id="password_confirmation"
                                v-model="passwordConfirmation"
                                placeholder="********"
                                class="form-control border-start-0 ps-0 bg-light"
                                :class="{ 'is-invalid': passwordConfirmationMeta.touched && passwordConfirmationError, 'is-valid': passwordConfirmationMeta.touched && !passwordConfirmationError }"
                            >
                            <div class="invalid-feedback">{{ passwordConfirmationError }}</div>
                        </div>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-danger btn-lg fw-bold" :disabled="isSubmitting">
                            <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-2"></span>
                            Registrarse
                        </button>
                    </div>

                    <div class="text-center">
                        <p class="text-muted mb-0">¿Ya tienes cuenta? <router-link :to="{ name: 'login' }" class="text-danger fw-bold text-decoration-none">Inicia Sesión</router-link></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped>
.input-group-text { border-color: #dee2e6; }
.form-control:focus { box-shadow: none; border-color: #dee2e6; }
.input-group:focus-within {
    box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
    border-radius: 0.375rem;
}
.input-group:focus-within .input-group-text,
.input-group:focus-within .form-control { border-color: #dc3545; }
</style>
