<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import api from '@/services/api';
import { useCartStore } from '@/stores/cart';
import { useAuthStore } from '@/stores/auth';
import { useToastStore } from '@/stores/toast';

const route = useRoute();
const cartStore = useCartStore();
const authStore = useAuthStore();
const toastStore = useToastStore();

const product = ref(null);
const relatedProducts = ref([]);
const selectedSize = ref(null);
const quantity = ref(1);
const isLoading = ref(true);
const currentImage = ref(null);

const fetchProduct = async (id) => {
    isLoading.value = true;
    try {
        const response = await axios.get(`/api/products/${id}`);
        product.value = response.data.data;
        currentImage.value = product.value.image_url;
        if (response.data.additional) {
             relatedProducts.value = response.data.additional.related;
        }
        
        // Reset selection
        selectedSize.value = null;
        quantity.value = 1;
        
        // Default size selection logic
        const sizes = availableSizes.value;
        if (sizes.length > 0) {
            selectedSize.value = sizes[0];
        }
    } catch (error) {
        console.error('Error fetching product:', error);
    } finally {
        isLoading.value = false;
    }
};


// Removed original onMounted as it is now handled in the new block above


watch(() => route.params.id, (newId) => {
    fetchProduct(newId);
});

const availableSizes = computed(() => {
    if (!product.value) return [];
    
    // Logic ported from blade
    const catLower = product.value.categoria ? product.value.categoria.toLowerCase() : '';
    if (catLower.includes('zapatillas') || catLower.includes('calzado') || catLower.includes('botas')) {
        return Array.from({length: 9}, (_, i) => i + 38); // 38-46
    } else if (catLower.includes('calcetines')) {
        return ['S (34-38)', 'M (38-42)', 'L (42-46)'];
    } else {
        return ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
    }
});

const addToCart = () => {
    if (!selectedSize.value) {
        toastStore.addToast('Por favor selecciona una talla.', 'error');
        return;
    }
    cartStore.addToCart(product.value, selectedSize.value, quantity.value);
    toastStore.addToast('Producto añadido al carrito', 'success');
};

const isInWishlist = ref(false);

const checkWishlist = async () => {
    if (!authStore.isAuthenticated) return;
    try {
        const response = await api.get('/wishlist');
        const wishlistIds = response.data.data.map(p => p.id);
        isInWishlist.value = wishlistIds.includes(product.value.id);
    } catch (error) {
        console.error('Error checking wishlist:', error);
    }
};

const handleWishlist = async () => {
    if (!authStore.isAuthenticated) {
        toastStore.addToast('Debes iniciar sesión para añadir a la lista de deseos.', 'error');
        return;
    }
    try {
        const response = await api.post(`/wishlist/${product.value.id}`);
        isInWishlist.value = response.data.status === 'added';
        
        if (response.data.status === 'added') {
             toastStore.addToast('Producto añadido a la lista de deseos', 'success');
        } else {
             toastStore.addToast('Producto eliminado de la lista de deseos', 'info');
        }
    } catch (error) {
        console.error('Error toggling wishlist:', error);
        toastStore.addToast('Error al actualizar la lista de deseos', 'error');
    }
};

onMounted(() => {
    fetchProduct(route.params.id).then(() => {
        checkWishlist();
    });
});

const newReview = ref({
    rating: 5,
    comment: ''
});

const submitReview = async () => {
    if (!newReview.value.comment.trim()) {
        toastStore.addToast('Por favor escribe un comentario.', 'error');
        return;
    }

    try {
        await api.post(`/products/${product.value.id}/reviews`, newReview.value);
        // Refresh product to get new review
        await fetchProduct(product.value.id);
        newReview.value.comment = '';
        newReview.value.rating = 5;
        toastStore.addToast('¡Gracias por tu valoración!', 'success');
    } catch (error) {
        console.error('Error submitting review:', error);
        const errorMsg = error.response && error.response.data && error.response.data.error 
            ? error.response.data.error 
            : 'Error al enviar la valoración.';
        toastStore.addToast(errorMsg, 'error');
    }
};
</script>

<template>
    <div v-if="isLoading" class="container my-5 text-center">
        <div class="spinner-border" role="status"><span class="visually-hidden">Cargando...</span></div>
    </div>

    <div v-else-if="product" class="container my-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><router-link :to="{ name: 'home' }" class="text-decoration-none text-muted">Inicio</router-link></li>
                <li class="breadcrumb-item"><router-link :to="{ name: 'products' }" class="text-decoration-none text-muted">Productos</router-link></li>
                <li class="breadcrumb-item active" aria-current="page">{{ product.nombre }}</li>
            </ol>
        </nav>

        <div class="row g-5">
            <!-- Image -->
            <div class="col-md-6">
                <!-- Image Container -->
                <div class="mb-4 d-flex justify-content-center align-items-center position-relative overflow-hidden bg-white rounded-3 shadow-sm" style="height: 500px; width: 100%;">
                     <span v-if="product.oferta" class="badge bg-danger position-absolute top-0 start-0 m-3 fs-5 px-3 py-2" style="z-index: 10;">Oferta</span>
                     
                     <img v-if="currentImage" :src="currentImage" :alt="product.nombre" class="img-fluid" style="width: 100%; height: 100%; object-fit: cover;">
                     <div v-else class="text-center text-muted">
                        <i class="bi bi-image" style="font-size: 5rem;"></i>
                        <p>Sin imagen</p>
                    </div>
                </div>

                <!-- Thumbnails -->
                <div v-if="product.images && product.images.length > 0" class="d-flex justify-content-center gap-3 overflow-auto py-2">
                    <div 
                        @click="currentImage = product.image_url"
                        class="ratio ratio-1x1 rounded-3 border overflow-hidden position-relative"
                        :class="currentImage === product.image_url ? 'border-dark ring-2' : 'border-light opacity-75'"
                        style="width: 80px; cursor: pointer; transition: all 0.2s;"
                    >
                         <img :src="product.image_url" class="img-fluid w-100 h-100" style="object-fit: cover;">
                    </div>

                    <div 
                        v-for="img in product.images" 
                        :key="img.id" 
                        @click="currentImage = img.image_url"
                        class="ratio ratio-1x1 rounded-3 border overflow-hidden position-relative"
                        :class="currentImage === img.image_url ? 'border-dark ring-2' : 'border-light opacity-75'"
                        style="width: 80px; cursor: pointer; transition: all 0.2s;"
                    >
                         <img :src="img.image_url" class="img-fluid w-100 h-100" style="object-fit: cover;">
                    </div>
                </div>
            </div>

            <!-- Details -->
            <div class="col-md-6">
                <h6 class="text-muted text-uppercase fw-bold">{{ product.marca }}</h6>
                <h1 class="display-5 fw-bold mb-3">{{ product.nombre }}</h1>

                <div class="d-flex align-items-center mb-4">
                    <span class="fs-2 fw-bold text-dark me-3">{{ Number(product.precio).toFixed(2) }} €</span>
                    <span v-if="product.oferta" class="fs-5 text-muted text-decoration-line-through">{{ (product.precio * 1.2).toFixed(2) }} €</span>
                </div>

                <p class="lead mb-4">{{ product.descripcion || 'Sin descripción disponible.' }}</p>

                <div class="mb-4">
                    <span class="badge bg-light text-dark border me-2 p-2">{{ product.sexo }}</span>
                    <span class="badge bg-light text-dark border me-2 p-2">{{ product.categoria }}</span>
                    <span :class="['badge p-2', product.stock > 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger']">
                        {{ product.stock > 0 ? 'En Stock' : 'Agotado' }}
                    </span>
                </div>

                <form @submit.prevent="addToCart">
                    <div v-if="product.stock > 0" class="mb-4">
                        <label class="form-label fw-bold">Selecciona tu talla:</label>
                        <div class="d-flex flex-wrap gap-2">
                             <div v-for="(size, index) in availableSizes" :key="index">
                                 <input type="radio" class="btn-check" name="talla" :id="'size_'+index" :value="size" v-model="selectedSize">
                                 <label class="btn btn-outline-dark" :for="'size_'+index">{{ size }}</label>
                             </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                        <button type="submit" class="btn btn-dark btn-lg px-4 flex-grow-1" :disabled="!product.stock">
                           <i class="bi bi-cart-plus me-2"></i> Añadir al carrito
                        </button>
                        <button type="button" @click="handleWishlist" class="btn btn-outline-danger btn-lg px-4">
                            <i :class="isInWishlist ? 'bi bi-heart-fill' : 'bi bi-heart'"></i>
                        </button>
                    </div>
                </form>

                <hr class="my-5">
                <div class="small text-muted">
                    <p class="mb-1"><i class="bi bi-truck me-2"></i> Envío gratis en pedidos superiores a 50€</p>
                    <p class="mb-1"><i class="bi bi-arrow-repeat me-2"></i> Devoluciones gratuitas en 30 días</p>
                    <p class="mb-0"><i class="bi bi-shield-check me-2"></i> Garantía de 2 años</p>
                </div>
            </div>
        </div>
        
        <!-- Reviews Section -->
        <div class="mt-5">
            <hr class="my-5">
            <h3 class="mb-4 fw-bold">Valoraciones</h3>
            
            <div class="row">
                <div class="col-md-6 mb-4">
                    <h5 class="mb-3">Opiniones de clientes</h5>
                    <div v-if="product.reviews && product.reviews.length > 0">
                        <div v-for="review in product.reviews" :key="review.id" class="card mb-3 border-0 shadow-sm bg-light">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold mb-0">{{ review.user.nombre }}</h6>
                                    <div class="text-warning small">
                                        <i v-for="n in 5" :key="n" :class="n <= review.rating ? 'bi bi-star-fill' : 'bi bi-star'"></i>
                                    </div>
                                </div>
                                <p class="mb-0 text-muted">{{ review.comment }}</p>
                                <small class="text-muted" style="font-size: 0.75rem;">{{ new Date(review.created_at).toLocaleDateString() }}</small>
                            </div>
                        </div>
                    </div>
                    <div v-else class="alert alert-info">
                        No hay valoraciones aún. ¡Sé el primero en opinar!
                    </div>
                </div>

                <div class="col-md-6">
                    <h5 class="mb-3">Escribe tu opinión</h5>
                    <div v-if="authStore.isAuthenticated" class="card border-0 shadow-sm">
                        <div class="card-body">
                            <form @submit.prevent="submitReview">
                                <div class="mb-3">
                                    <label class="form-label">Puntuación</label>
                                    <div class="text-warning fs-4" style="cursor: pointer;">
                                        <i v-for="n in 5" :key="n" 
                                           @click="newReview.rating = n"
                                           :class="n <= newReview.rating ? 'bi bi-star-fill' : 'bi bi-star'"
                                           class="me-1"></i>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="comment" class="form-label">Comentario</label>
                                    <textarea id="comment" v-model="newReview.comment" class="form-control" rows="3" required placeholder="¿Qué te ha parecido este producto?"></textarea>
                                </div>
                                <button type="submit" class="btn btn-dark w-100">Enviar Valoración</button>
                            </form>
                        </div>
                    </div>
                    <div v-else class="alert alert-warning">
                        Debes <router-link :to="{ name: 'login' }" class="alert-link">iniciar sesión</router-link> para dejar una valoración.
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <div v-if="relatedProducts.length > 0" class="mt-5">
            <hr class="my-5">
            <h3 class="mb-4 fw-bold">Productos Relacionados</h3>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">
                <div v-for="related in relatedProducts" :key="related.id" class="col">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="bg-white p-3 rounded-top d-flex align-items-center justify-content-center" style="height: 200px;">
                             <img v-if="related.image_url" :src="related.image_url" class="img-fluid" style="max-height: 100%; object-fit: contain;">
                             <div v-else class="text-muted"><i class="bi bi-image"></i></div>
                        </div>
                        <div class="card-body text-center">
                            <h6 class="card-title text-truncate"><router-link :to="{ name: 'product-detail', params: { id: related.id } }" class="text-decoration-none text-dark">{{ related.nombre }}</router-link></h6>
                            <p class="card-text fw-bold">{{ Number(related.precio).toFixed(2) }} €</p>
                            <router-link :to="{ name: 'product-detail', params: { id: related.id } }" class="btn btn-sm btn-outline-dark w-100">Ver producto</router-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div v-else class="container my-5 text-center">
        <h3>Producto no encontrado</h3>
        <router-link :to="{ name: 'products' }" class="btn btn-dark mt-3">Volver a la tienda</router-link>
    </div>
</template>
