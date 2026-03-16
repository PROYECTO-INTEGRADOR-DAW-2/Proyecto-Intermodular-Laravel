<script setup>
import { ref } from 'vue';
import { useToastStore } from '@/stores/toast';
import axios from 'axios';

const toastStore = useToastStore();

const form = ref({
    name: '',
    email: '',
    subject: '',
    message: ''
});

const isSubmitting = ref(false);

const submitForm = async () => {
    isSubmitting.value = true;
    try {
        await axios.post('/api/contact', form.value);
        toastStore.addToast('¡Mensaje enviado con éxito! Nos pondremos en contacto contigo pronto.', 'success');
        form.value = { name: '', email: '', subject: '', message: '' };
    } catch (error) {
        toastStore.addToast('Error al enviar el mensaje. Inténtalo de nuevo.', 'error');
    } finally {
        isSubmitting.value = false;
    }
};
</script>

<template>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="text-center mb-5">
                    <h1 class="display-5 fw-bold mb-3">Contacto</h1>
                    <p class="lead text-muted">¿Tienes alguna duda o sugerencia? Escríbenos y te responderemos lo antes posible.</p>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <form @submit.prevent="submitForm">
                            <div class="mb-3">
                                <label for="name" class="form-label fw-bold">Nombre completo</label>
                                <input type="text" class="form-control" id="name" v-model="form.name" required placeholder="Tu nombre">
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold">Correo electrónico</label>
                                <input type="email" class="form-control" id="email" v-model="form.email" required placeholder="tu@email.com">
                            </div>

                            <div class="mb-3">
                                <label for="subject" class="form-label fw-bold">Asunto</label>
                                <select class="form-select" id="subject" v-model="form.subject" required>
                                    <option value="" disabled selected>Selecciona un asunto...</option>
                                    <option value="Duda sobre un producto">Duda sobre un producto</option>
                                    <option value="Problema con mi pedido">Problema con mi pedido</option>
                                    <option value="Devoluciones/Cambios">Devoluciones / Cambios</option>
                                    <option value="Sugerencias">Sugerencias</option>
                                    <option value="Otros">Otros</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="message" class="form-label fw-bold">Mensaje</label>
                                <textarea class="form-control" id="message" v-model="form.message" rows="5" required placeholder="¿En qué podemos ayudarte?"></textarea>
                            </div>

                            <button type="submit" class="btn btn-dark w-100 btn-lg" :disabled="isSubmitting">
                                <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                {{ isSubmitting ? 'Enviando...' : 'Enviar Mensaje' }}
                            </button>
                        </form>
                    </div>
                </div>

                <div class="row mt-5 text-center g-4">
                    <div class="col-md-4">
                        <div class="p-3">
                            <i class="bi bi-geo-alt fs-1 text-danger mb-3 d-block"></i>
                            <h5 class="fw-bold">Nuestra Tienda</h5>
                            <p class="text-muted mb-0">Calle Falsa 123<br>28000 Madrid, España</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3">
                            <i class="bi bi-telephone fs-1 text-danger mb-3 d-block"></i>
                            <h5 class="fw-bold">Llámanos</h5>
                            <p class="text-muted mb-0">+34 900 123 456<br>Lun - Vie, 9:00 - 18:00</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3">
                            <i class="bi bi-envelope fs-1 text-danger mb-3 d-block"></i>
                            <h5 class="fw-bold">Escríbenos</h5>
                            <p class="text-muted mb-0">info@jasports.com<br>soporte@jasports.com</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.form-control:focus, .form-select:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
}
</style>
