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
    <div class="profile-page-wrapper">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-9">
                    
                    <!-- Main Profile Card -->
                    <div class="card profile-card border-0 shadow-lg overflow-hidden fade-in-up">
                        
                        <!-- Header Banner -->
                        <div class="profile-header">
                            <div class="header-content text-white">
                                <h1 class="fw-bold mb-0 text-shadow">Mi Perfil</h1>
                                <p class="opacity-75 mb-0">Gestiona tu información</p>
                            </div>
                        </div>

                        <div class="card-body p-0">
                            <div class="row g-0">
                                
                                <!-- Sidebar / Avatar Section -->
                                <div class="col-md-5 col-lg-4 text-center bg-light-subtle p-5 d-flex flex-column align-items-center justify-content-center border-end-md">
                                    <div class="avatar-container mb-4">
                                        <div class="avatar-ring">
                                            <img src="/img/user.png" alt="Usuario" class="profile-avatar img-fluid rounded-circle">
                                        </div>
                                        <div class="status-indicator"></div>
                                    </div>
                                    <h3 class="fw-bold text-dark mb-1">{{ authStore.user?.name || 'Usuario' }}</h3>
                                    <p class="text-muted small mb-3">{{ authStore.user?.email || 'email@ejemplo.com' }}</p>
                                    
                                    <div class="d-flex flex-wrap justify-content-center gap-2 mb-3">
                                        <RoleBadge v-if="displayRoles.length > 0" v-for="role in displayRoles" :key="role" :role="role" />
                                        <span v-else class="badge bg-secondary rounded-pill px-3 py-2">Usuario</span>
                                    </div>
                                </div>

                                <!-- Content Section -->
                                <div class="col-md-7 col-lg-8 p-5">
                                    <h5 class="text-uppercase text-muted fw-bold small mb-4 tracking-wider">Acciones Rápidas</h5>
                                    
                                    <div class="row g-3">
                                        <!-- Orders Action -->
                                        <div class="col-12 col-sm-6">
                                            <router-link :to="{ name: 'orders' }" class="action-card text-decoration-none">
                                                <div class="action-icon-wrapper bg-danger-subtle text-danger">
                                                    <i class="bi bi-box-seam-fill fs-4"></i>
                                                </div>
                                                <div class="action-details">
                                                    <h6 class="fw-bold text-dark mb-1">Mis Pedidos</h6>
                                                    <span class="text-muted small">Ver historial de compras</span>
                                                </div>
                                                <i class="bi bi-chevron-right ms-auto text-muted small"></i>
                                            </router-link>
                                        </div>

                                        <!-- Edit Profile -->
                                        <div class="col-12 col-sm-6">
                                            <router-link :to="{ name: 'profile-edit' }" class="action-card text-decoration-none">
                                                <div class="action-icon-wrapper bg-secondary-subtle text-secondary">
                                                    <i class="bi bi-pencil-fill fs-4"></i>
                                                </div>
                                                <div class="action-details">
                                                    <h6 class="fw-bold text-dark mb-1">Editar Perfil</h6>
                                                    <span class="text-muted small">Actualizar datos</span>
                                                </div>
                                                <i class="bi bi-chevron-right ms-auto text-muted small"></i>
                                            </router-link>
                                        </div>

                                        <!-- Reviews -->
                                        <div class="col-12 col-sm-6">
                                            <router-link :to="{ name: 'reviews' }" class="action-card text-decoration-none">
                                                <div class="action-icon-wrapper bg-warning-subtle text-warning">
                                                    <i class="bi bi-star-fill fs-4"></i>
                                                </div>
                                                <div class="action-details">
                                                    <h6 class="fw-bold text-dark mb-1">Mis Reseñas</h6>
                                                    <span class="text-muted small">Opiniones realizadas</span>
                                                </div>
                                                <i class="bi bi-chevron-right ms-auto text-muted small"></i>
                                            </router-link>
                                        </div>
                                        
                                        <!-- Settings -->
                                        <div class="col-12 col-sm-6">
                                            <router-link :to="{ name: 'settings' }" class="action-card text-decoration-none">
                                                <div class="action-icon-wrapper bg-info-subtle text-info">
                                                    <i class="bi bi-gear-fill fs-4"></i>
                                                </div>
                                                <div class="action-details">
                                                    <h6 class="fw-bold text-dark mb-1">Ajustes</h6>
                                                    <span class="text-muted small">Configuración de cuenta</span>
                                                </div>
                                                <i class="bi bi-chevron-right ms-auto text-muted small"></i>
                                            </router-link>
                                        </div>
                                    </div>
                                    
                                    <!-- Account Status or ID -->
                                    <div class="mt-5 pt-4 border-top">
                                        <div class="d-flex align-items-center text-muted small">
                                            <i class="bi bi-shield-check me-2 fs-5 text-success"></i>
                                            <span>Cuenta activa y verificada</span>
                                            <span class="ms-auto font-monospace opacity-50">ID: {{ authStore.user?.id || '---' }}</span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in-up {
    animation: fadeInUp 0.6s ease-out forwards;
}

/* Tracking */
.tracking-wider {
    letter-spacing: 0.1em;
}

/* Profile Page Wrapper */
.profile-page-wrapper {
    background-color: #f8f9fa; /* Fallback */
    min-height: 80vh;
    display: flex;
    align-items: center;
}

/* Card Styling */
.profile-card {
    border-radius: 1.5rem;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 1rem 3rem rgba(0,0,0,0.08) !important;
    transition: transform 0.3s ease;
}

.profile-card:hover {
    transform: translateY(-5px);
}

/* Header Banner */
.profile-header {
    background: linear-gradient(135deg, #FF6B6B 0%, #EE5253 100%);
    padding: 3rem 2rem;
    position: relative;
    overflow: hidden;
}

.profile-header::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
    opacity: 0.8;
}

.header-content {
    position: relative;
    z-index: 1;
    text-align: center;
}

.text-shadow {
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Avatar Styling */
.avatar-container {
    position: relative;
    display: inline-block;
    margin-top: -6rem; /* Pull up into banner area if desired, or keep clean */
    margin-top: 0;
}

.avatar-ring {
    padding: 5px;
    background: #fff;
    border-radius: 50%;
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
}

.profile-avatar {
    width: 140px;
    height: 140px;
    object-fit: cover;
    border: 3px solid #f8f9fa;
}

.status-indicator {
    position: absolute;
    bottom: 12px;
    right: 12px;
    width: 18px;
    height: 18px;
    background-color: #2ecc71;
    border: 3px solid #fff;
    border-radius: 50%;
}

@media (min-width: 768px) {
    .border-end-md {
        border-right: 1px solid rgba(0,0,0,0.05);
    }
}

/* Action Cards */
.action-card {
    display: flex;
    align-items: center;
    padding: 1.25rem;
    background: #fff;
    border: 1px solid rgba(0,0,0,0.05);
    border-radius: 1rem;
    transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
    height: 100%;
    position: relative;
    overflow: hidden;
}

.action-card:not(.disabled):hover {
    box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.08);
    transform: translateY(-3px);
    border-color: transparent;
}

.action-card:not(.disabled):active {
    transform: translateY(-1px);
}

.action-icon-wrapper {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    flex-shrink: 0;
    transition: transform 0.3s ease;
}

.action-card:hover .action-icon-wrapper {
    transform: scale(1.1) rotate(5deg);
}

.action-details {
    flex-grow: 1;
}

/* Disabled State */
.action-card.disabled {
    opacity: 0.6;
    cursor: default;
    background: #f8f9fa;
}

</style>
