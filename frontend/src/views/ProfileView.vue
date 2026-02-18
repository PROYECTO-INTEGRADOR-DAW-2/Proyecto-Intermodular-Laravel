<script setup>
import { computed } from 'vue';
import { useAuthStore } from '../stores/auth';
import RoleBadge from '../components/RoleBadge.vue';

const authStore = useAuthStore();

const displayRoles = computed(() => {
    const roles = new Set();
    if (authStore.user?.role) roles.add(authStore.user.role);
    if (authStore.user?.roles) {
        const rolesList = Array.isArray(authStore.user.roles) ? authStore.user.roles : [authStore.user.roles];
        rolesList.forEach(r => roles.add(typeof r === 'string' ? r : r.name));
    }
    return Array.from(roles);
});
</script>

<template>
    <div class="container py-5">
        <h1 class="mb-4">Mi Perfil</h1>
        
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-5">
                <div class="row align-items-center">
                    <div class="col-md-3 text-center mb-4 mb-md-0">
                        <img src="/img/user.png" alt="Usuario" class="img-fluid rounded-circle shadow-sm" style="max-width: 150px;">
                    </div>
                    <div class="col-md-9">
                        <h2 class="fw-bold mb-3">{{ authStore.user?.name }}</h2>
                        <p class="text-muted mb-1"><strong>Email:</strong> {{ authStore.user?.email }}</p>
                        
                        <div class="mb-4 d-flex align-items-center">
                            <strong class="me-2">Roles:</strong>
                            <div v-if="displayRoles.length > 0">
                                <RoleBadge v-for="role in displayRoles" :key="role" :role="role" class="me-1" />
                            </div>
                            <span v-else class="text-muted">Sin roles asignados</span>
                        </div>
                        
                        <div class="d-flex gap-3">
                            <router-link :to="{ name: 'orders' }" class="btn btn-outline-danger btn-lg">
                                <i class="bi bi-box-seam me-2"></i> Mis Pedidos
                            </router-link>
                            <!-- Future: Edit Profile -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
