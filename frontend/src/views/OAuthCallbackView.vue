<script setup>
import { onMounted, ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import axios from 'axios'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()
const error = ref('')

onMounted(async () => {
    const code = route.query.code
    const state = route.query.state

    if (!code) {
        error.value = 'No se recibió el código de autorización de Google.'
        return
    }

    try {
        // Send code + state to backend callback endpoint
        const response = await axios.get('/api/oauth/google/callback', {
            params: { code, state }
        })

        const data = response.data.data ?? response.data

        // Store token and user in Pinia (same format as normal login)
        authStore.setSession({
            token: data.token,
            name: data.name,
            email: data.email,
            role: data.role,
            roles: data.roles ?? [],
        })

        router.push({ name: 'home' })
    } catch (err) {
        error.value = err.response?.data?.message ?? 'Error al iniciar sesión con Google.'
    }
})
</script>

<template>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="text-center">
            <div v-if="!error">
                <div class="spinner-border text-dark mb-3" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="text-muted">Completando inicio de sesión con Google...</p>
            </div>
            <div v-else class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ error }}
                <div class="mt-3">
                    <router-link :to="{ name: 'login' }" class="btn btn-dark btn-sm">Volver al login</router-link>
                </div>
            </div>
        </div>
    </div>
</template>
