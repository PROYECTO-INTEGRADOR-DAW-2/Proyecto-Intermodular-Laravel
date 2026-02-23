<script setup>
import { ref, onMounted } from 'vue';
import api from '../services/api';

const reviews = ref([]);
const isLoading = ref(true);

onMounted(async () => {
    try {
        const response = await api.get('/user/reviews');
        // Handle array response directly
        reviews.value = response.data;
    } catch (error) {
        console.error('Error fetching reviews:', error);
    } finally {
        isLoading.value = false;
    }
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString();
};

const renderStars = (rating) => {
    return '★'.repeat(rating) + '☆'.repeat(5 - rating);
};
</script>

<template>
    <div class="reviews-wrapper">
        <div class="container py-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                 <h1 class="fw-bold m-0">Mis Reseñas</h1>
                 <router-link :to="{ name: 'profile' }" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Volver al Perfil
                 </router-link>
            </div>
           

            <div v-if="isLoading" class="text-center py-5">
                <div class="spinner-border text-warning" role="status"><span class="visually-hidden">Cargando...</span></div>
            </div>

            <div v-else-if="reviews.length > 0" class="row g-4">
                <div v-for="review in reviews" :key="review.id" class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 review-card">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                             <div class="d-flex align-items-center mb-2">
                                <span class="text-warning fs-5 me-2" style="letter-spacing: 2px;">{{ renderStars(review.rating) }}</span>
                                <span class="fw-bold text-dark">{{ review.rating }}/5</span>
                             </div>
                             <small class="text-muted"><i class="bi bi-clock me-1"></i> {{ formatDate(review.created_at) }}</small>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <h5 class="card-title fw-bold mb-3 text-primary text-truncate">
                                <router-link :to="{ name: 'product-detail', params: { id: review.product_id } }" class="text-decoration-none text-inherit">
                                    {{ review.product?.nombre || 'Producto desconocido' }}
                                </router-link>
                            </h5>
                            <p class="card-text text-secondary fst-italic">"{{ review.comment }}"</p>
                        </div>
                         <div class="card-footer bg-light border-0 px-4 py-3 rounded-bottom-4">
                             <router-link :to="{ name: 'product-detail', params: { id: review.product_id } }" class="btn btn-sm btn-outline-primary rounded-pill w-100">
                                Ver Producto
                             </router-link>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="text-center py-5 bg-white rounded-4 shadow-sm">
                <div class="d-inline-flex align-items-center justify-content-center bg-warning-subtle text-warning rounded-circle mb-3" style="width: 80px; height: 80px;">
                    <i class="bi bi-star fs-1"></i>
                </div>
                <h3>No has escrito reseñas aún</h3>
                <p class="text-muted mb-4">Tu opinión ayuda a otros usuarios. ¡Compra un producto y deja tu valoración!</p>
                <router-link :to="{ name: 'products' }" class="btn btn-dark px-4 py-2 rounded-pill">Explorar Productos</router-link>
            </div>
        </div>
    </div>
</template>

<style scoped>
.reviews-wrapper {
    background-color: #f8f9fa;
    min-height: 80vh;
}
.text-inherit {
    color: inherit;
}
.review-card {
    transition: transform 0.2s;
}
.review-card:hover {
    transform: translateY(-5px);
}
</style>
