<script setup>
import { ref } from 'vue';

const notificationsEnabled = ref(true);
const newsletterEnabled = ref(false);
const twoFactorEnabled = ref(false);

</script>

<template>
    <div class="settings-wrapper">
        <div class="container py-5">
             <div class="d-flex justify-content-between align-items-center mb-4">
                 <h1 class="fw-bold m-0">Ajustes</h1>
                 <router-link :to="{ name: 'profile' }" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Volver al Perfil
                 </router-link>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <!-- Notifications Card -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white p-4 border-bottom-0">
                            <h5 class="fw-bold mb-0"><i class="bi bi-bell me-2 text-primary"></i> Notificaciones</h5>
                        </div>
                        <div class="card-body px-4 pb-4 pt-0">
                            <div class="form-check form-switch py-3 border-bottom">
                                <input class="form-check-input" type="checkbox" role="switch" id="notifSwitch" v-model="notificationsEnabled">
                                <label class="form-check-label" for="notifSwitch">Recibir notificaciones por email sobre pedidos</label>
                            </div>
                             <div class="form-check form-switch py-3">
                                <input class="form-check-input" type="checkbox" role="switch" id="newsSwitch" v-model="newsletterEnabled">
                                <label class="form-check-label" for="newsSwitch">Suscribirse al boletín de noticias</label>
                            </div>
                        </div>
                    </div>

                    <!-- Security Card -->
                     <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white p-4 border-bottom-0">
                            <h5 class="fw-bold mb-0"><i class="bi bi-shield-lock me-2 text-success"></i> Seguridad</h5>
                        </div>
                        <div class="card-body px-4 pb-4 pt-0">
                            <div class="form-check form-switch py-3 border-bottom">
                                <input class="form-check-input" type="checkbox" role="switch" id="2faSwitch" v-model="twoFactorEnabled" disabled>
                                <label class="form-check-label text-muted" for="2faSwitch">
                                    Autenticación de dos factores (2FA) 
                                    <span class="badge bg-secondary ms-2">Próximamente</span>
                                </label>
                            </div>
                            <div class="py-3">
                                <router-link :to="{ name: 'profile-edit' }" class="btn btn-outline-primary btn-sm">Cambiar Contraseña</router-link>
                            </div>
                        </div>
                    </div>

                     <!-- Data Management -->
                     <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white p-4 border-bottom-0">
                            <h5 class="fw-bold mb-0 text-danger"><i class="bi bi-exclamation-triangle me-2"></i> Zona de Peligro</h5>
                        </div>
                        <div class="card-body px-4 pb-4 pt-0">
                            <p class="text-muted small">Una vez que elimines tu cuenta, no hay vuelta atrás. Por favor, asegúrate.</p>
                            <button class="btn btn-danger btn-sm" disabled>Eliminar Cuenta</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.settings-wrapper {
    background-color: #f8f9fa;
    min-height: 80vh;
}
</style>
