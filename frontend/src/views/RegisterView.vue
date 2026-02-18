<script setup>
import { ref } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';

const name = ref('');
const email = ref('');
const password = ref('');
const password_confirmation = ref('');
const authStore = useAuthStore();
const router = useRouter();
const errorMessage = ref('');

const handleRegister = async () => {
    try {
        await authStore.register({
            name: name.value,
            email: email.value,
            password: password.value,
            password_confirmation: password_confirmation.value
        });
        router.push('/');
    } catch (error) {
        if (error.response && error.response.data && error.response.data.errors) {
             errorMessage.value = Object.values(error.response.data.errors).flat().join('\n');
        } else {
             errorMessage.value = 'Registration failed. Please try again.';
        }
    }
};
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

                <div v-if="errorMessage" class="alert alert-danger">{{ errorMessage }}</div>

                <form @submit.prevent="handleRegister">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold">Nombre</label>
                        <div class="input-group">
                             <span class="input-group-text bg-light border-end-0"><i class="bi bi-person"></i></span>
                             <input type="text" class="form-control border-start-0 ps-0 bg-light" id="name" v-model="name" placeholder="Tu nombre" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">Email</label>
                        <div class="input-group">
                             <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope"></i></span>
                             <input type="email" class="form-control border-start-0 ps-0 bg-light" id="email" v-model="email" placeholder="name@example.com" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control border-start-0 ps-0 bg-light" id="password" v-model="password" placeholder="********" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label fw-bold">Confirmar Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" class="form-control border-start-0 ps-0 bg-light" id="password_confirmation" v-model="password_confirmation" placeholder="********" required>
                        </div>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-danger btn-lg fw-bold">Registrarse</button>
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
.input-group-text {
    border-color: #dee2e6;
}
.form-control:focus {
    box-shadow: none;
    border-color: #dee2e6;
}
.input-group:focus-within {
    box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
    border-radius: 0.375rem;
}
.input-group:focus-within .input-group-text,
.input-group:focus-within .form-control {
    border-color: #dc3545;
}
</style>
