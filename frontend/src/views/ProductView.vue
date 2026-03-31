<script setup>
    import { useRoute } from 'vue-router';
    import { ref, onMounted } from 'vue';
    import { useMessageStore } from '../stores/messageStore';
    import { useProductsStore } from '../stores/productsStore';
    import { RouterLink } from 'vue-router';
    import Reviews from '../components/Reviews.vue';
    import { useAuthStore } from '../stores/authStore';

    const route = useRoute();
    const messageStore = useMessageStore();
    const productStore = useProductsStore();

    const product = ref(null);
    const loading = ref(true);

    onMounted(async () => {
        loading.value = true;
        try {
            if (route.params.id) {
                const response = await productStore.getProduct(route.params.id);

                if (response && response.success === false) {
                    product.value = null;
                } else {
                    product.value = response.data || response;
                }

            } else {
                messageStore.addMessage({ type: 'error', message: 'ID de producto no proporcionado' });
            }
        } catch (error) {
            messageStore.addMessage({ type: 'error', message: 'Error al cargar el producto' });
        } finally {
            loading.value = false;
        }
    });

</script>

<template>
    <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <p>Cargando producto...</p>
    </div>

    <div v-else-if="product" class="product-page">
        <div class="product-container">
            <div class="product-image-section">
                <img :src="product.img || 'https://via.placeholder.com/600'" :alt="product.nombre" class="main-image">
            </div>
            
            <div class="product-info-section">
                <div class="breadcrumb">
                    <RouterLink to="/products">Productos</RouterLink> / {{ product.categoria }}
                </div>
                
                <h1 class="product-title">{{ product.nombre }}</h1>
                <p class="product-sku">Ref: {{ product.sku }}</p>
                
                <div class="price-section">
                    <span class="price">{{ product.precio }}€</span>
                    <span v-if="product.oferta" class="offer-badge">¡Oferta!</span>
                </div>

                <div class="divider"></div>

                <div class="product-details">
                    <p><strong>Marca:</strong> {{ product.marca }}</p>
                    <p><strong>Deporte:</strong> {{ product.deporte }}</p>
                    <p><strong>Sexo:</strong> {{ product.sexo }}</p>
                    <p v-if="product.altura"><strong>Altura:</strong> {{ product.altura }}</p>
                </div>

                <div class="description">
                    <h3>Descripción</h3>
                    <p>{{ product.descripcion }}</p>
                </div>

                <button class="add-to-cart-btn">Añadir al carrito</button>

                <div v-if="product" class="new-badge">Novedad</div>
            </div>
        </div>
    </div>

    <div v-else class="error-message-container">
        <h1 class="error-message">¡Vaya! hubo un problema al buscar el producto</h1>
        <p>No pudimos encontrar el producto que buscas.</p>
        <router-link to="/products" class="back-link">Volver al catálogo de productos</router-link>
    </div>

    <Reviews v-if="product" :productId="product.id"></Reviews>

</template>

<style scoped>
    .product-page {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
    }

    .product-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }

    .product-image-section {
        display: flex;
        justify-content: center;
        align-items: flex-start;
    }

    .main-image {
        width: 100%;
        max-width: 500px;
        border-radius: 8px;
        object-fit: cover;
    }

    .product-info-section {
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .breadcrumb {
        font-size: 0.9em;
        color: #666;
        margin-bottom: 10px;
    }

    .breadcrumb a {
        color: #D72631;
        text-decoration: none;
    }

    .product-title {
        font-size: 2.5em;
        font-weight: 700;
        margin: 0;
        color: #222;
    }

    .product-sku {
        font-size: 0.85em;
        color: #888;
        margin-bottom: 20px;
    }

    .price-section {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 25px;
    }

    .price {
        font-size: 2em;
        font-weight: 700;
        color: #D72631;
    }

    .offer-badge {
        background: #28a745;
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.85em;
        font-weight: 600;
    }

    .divider {
        height: 1px;
        background: #eee;
        margin-bottom: 25px;
    }

    .product-details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-bottom: 30px;
        font-size: 1.05em;
    }

    .description h3 {
        font-size: 1.25em;
        margin-bottom: 10px;
        color: #333;
    }

    .description p {
        line-height: 1.6;
        color: #555;
    }

    .add-to-cart-btn {
        margin-top: auto;
        background: #222;
        color: white;
        border: none;
        padding: 18px;
        font-size: 1.1em;
        font-weight: 600;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .add-to-cart-btn:hover {
        background: #D72631;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(215, 38, 49, 0.3);
    }

    .new-badge {
        position: absolute;
        top: 0;
        right: 0;
        background: #007bff;
        color: white;
        padding: 6px 15px;
        border-radius: 4px;
        font-weight: 600;
        transform: rotate(10deg);
    }

    .loading-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100vh;
    }

    .spinner {
        width: 50px;
        height: 50px;
        border: 5px solid #f3f3f3;
        border-top: 5px solid #D72631;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-bottom: 15px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .error-message-container {
        height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        gap: 15px;
    }

    .back-link {
        color: #D72631;
        text-decoration: none;
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .product-container {
            grid-template-columns: 1fr;
        }
        
        .product-title {
            font-size: 1.8em;
        }
    }
</style>