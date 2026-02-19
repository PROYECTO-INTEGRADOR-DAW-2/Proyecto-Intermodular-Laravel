<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';
import api from '../services/api';
import * as yup from 'yup';
import { useForm, useField } from 'vee-validate';

const authStore = useAuthStore();
const router = useRouter();
const isLoading = ref(false);
const serverError = ref('');
const successMessage = ref('');

// Schema definition
const schema = yup.object({
  name: yup.string().required('El nombre es obligatorio'),
  email: yup.string().email('Email no válido').required('El email es obligatorio'),
  password: yup.string().min(6, 'Mínimo 6 caracteres').nullable(),
  password_confirmation: yup.string()
    .oneOf([yup.ref('password')], 'Las contraseñas no coinciden')
    .nullable()
});

// Form setup
const { handleSubmit, errors } = useForm({
  validationSchema: schema,
});

const { value: name } = useField('name');
const { value: email } = useField('email');
const { value: password } = useField('password');
const { value: password_confirmation } = useField('password_confirmation');

onMounted(() => {
    if (authStore.user) {
        name.value = authStore.user.name;
        email.value = authStore.user.email;
    }
});

const onSubmit = handleSubmit(async (values) => {
    isLoading.value = true;
    serverError.value = '';
    successMessage.value = '';
    
    try {
        // Filter out empty password fields to avoid sending empty strings if not changed
        const payload = {
            name: values.name,
            email: values.email,
        };

        if (values.password) {
            payload.password = values.password;
            payload.password_confirmation = values.password_confirmation;
        }

        const response = await api.put('/user/profile', payload);
        
        // Update store with new user data
        if (response.data.data) {
             // We need to keep the structure consistent. Auth store expects token or user object.
             // We update the user object in the store directly.
             authStore.user.name = response.data.data.name;
             authStore.user.email = response.data.data.email;
             localStorage.setItem('user', JSON.stringify(authStore.user));
        }
        
        successMessage.value = 'Perfil actualizado correctamente';
        setTimeout(() => router.push({ name: 'profile' }), 1500);
        
    } catch (error) {
        if (error.response?.data?.data) {
            // Laravel validation errors
            // You might want to map these to vee-validate setErrors if keys match your fields
            // For simplicity, just showing generic error or first validation error
             const valErrors = Object.values(error.response.data.data).flat();
             serverError.value = valErrors[0] || 'Error al actualizar el perfil';
        } else {
             serverError.value = error.response?.data?.message || 'Error al actualizar el perfil';
        }
    } finally {
        isLoading.value = false;
    }
});
</script>

<template>
    <div class="edit-profile-wrapper">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="card-header bg-primary text-white p-4">
                            <h2 class="h4 mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i> Editar Perfil</h2>
                        </div>
                        <div class="card-body p-5">
                            
                            <div v-if="successMessage" class="alert alert-success d-flex align-items-center mb-4" role="alert">
                                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                                <div>{{ successMessage }}</div>
                            </div>

                             <div v-if="serverError" class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                                <div>{{ serverError }}</div>
                            </div>

                            <form @submit="onSubmit">
                                <!-- Name -->
                                <div class="mb-4">
                                    <label for="name" class="form-label fw-bold">Nombre Completo</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                                        <input 
                                            v-model="name"
                                            id="name" 
                                            type="text" 
                                            class="form-control border-start-0 ps-0" 
                                            :class="{ 'is-invalid': errors.name }"
                                            placeholder="Tu nombre"
                                        >
                                    </div>
                                    <div class="invalid-feedback d-block" v-if="errors.name">{{ errors.name }}</div>
                                </div>

                                <!-- Email -->
                                <div class="mb-4">
                                    <label for="email" class="form-label fw-bold">Email</label>
                                     <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                                        <input 
                                            v-model="email"
                                            id="email" 
                                            type="email" 
                                            class="form-control border-start-0 ps-0" 
                                             :class="{ 'is-invalid': errors.email }"
                                            placeholder="tu@email.com"
                                        >
                                    </div>
                                    <div class="invalid-feedback d-block" v-if="errors.email">{{ errors.email }}</div>
                                </div>

                                <hr class="my-4">
                                <h5 class="mb-3 text-muted">Cambiar Contraseña <small>(Opcional)</small></h5>

                                <!-- Password -->
                                <div class="mb-3">
                                    <label for="password" class="form-label">Nueva Contraseña</label>
                                     <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                                        <input 
                                            v-model="password"
                                            id="password" 
                                            type="password" 
                                            class="form-control border-start-0 ps-0" 
                                            :class="{ 'is-invalid': errors.password }"
                                            placeholder="Mínimo 6 caracteres"
                                        >
                                    </div>
                                    <div class="invalid-feedback d-block" v-if="errors.password">{{ errors.password }}</div>
                                </div>

                                <!-- Password Confirmation -->
                                <div class="mb-5">
                                    <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                                     <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock-fill text-muted"></i></span>
                                        <input 
                                            v-model="password_confirmation"
                                            id="password_confirmation" 
                                            type="password" 
                                            class="form-control border-start-0 ps-0" 
                                            :class="{ 'is-invalid': errors.password_confirmation }"
                                            placeholder="Repite la contraseña"
                                        >
                                    </div>
                                    <div class="invalid-feedback d-block" v-if="errors.password_confirmation">{{ errors.password_confirmation }}</div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <router-link :to="{ name: 'profile' }" class="text-decoration-none text-muted">
                                        <i class="bi bi-arrow-left me-1"></i> Volver
                                    </router-link>
                                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill fw-bold" :disabled="isLoading">
                                        <span v-if="isLoading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                        {{ isLoading ? 'Guardando...' : 'Guardar Cambios' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.edit-profile-wrapper {
    background-color: #f8f9fa;
    min-height: 80vh;
}
</style>
