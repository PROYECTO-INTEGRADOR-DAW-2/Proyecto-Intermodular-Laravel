<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useCartStore } from '@/stores/cart';

const cartStore = useCartStore();
const masComprados = ref([]);
const promotedProduct = ref(null);
const isLoading = ref(true);

const fetchHomeData = async () => {
    try {
        const response = await axios.get('/api/home-products');
        masComprados.value = response.data.masComprados;
        promotedProduct.value = response.data.promotedProduct;
    } catch (error) {
        console.error('Error fetching home data:', error);
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    fetchHomeData();
});
</script>

<template>
    <div>
        <header id="header-home" class="d-flex align-items-center justify-content-center text-center" style="background-color: #f8f9fa;">
            <!-- Background image should be handled via CSS or inline style if dynamic URL -->
            <div class="container-fluid">
                <h1 class="display-3 fw-bold text-white text-shadow">J&A Sports</h1>
                <p class="lead text-white text-shadow">Compra a montones y viste a tu propio estilo</p>
            </div>
        </header>
        <!-- Inline style for background to use asset from public folder -->
        <component :is="'style'">
            #header-home {
                background-image: url('/img/fondopagina.jpg');
                background-size: cover;
                background-position: center;
                height: 85vh;
                position: relative;
            }
            .text-shadow {
                text-shadow: 2px 2px 4px rgba(0,0,0,0.7);
            }
        </component>


        <section id="novedades" class="container-fluid px-0 px-sm-3 my-5">
            <h2 class="text-center mb-5 fw-bold">Novedades</h2>
            <div id="brands" class="row justify-content-center g-4">
                <article class="col-6 col-md-3 text-center">
                    <router-link :to="{ name: 'products', query: { marca: 'Nike' } }" class="d-block text-decoration-none transition-transform hover-scale">
                        <img src="/imgNike/logoNike.jpg" alt="Nike" class="fotoMasNovedades">
                        <img src="/imgNike/simboloNike.png" alt="" class="fotoSimbolo d-none d-sm-inline">
                    </router-link>
                </article>

                <article class="col-6 col-md-3 text-center">
                    <router-link :to="{ name: 'products', query: { marca: 'Adidas' } }" class="d-block text-decoration-none transition-transform hover-scale">
                        <img src="/imgAdidas/logoAdidass.jpg" alt="Adidas" class="fotoMasNovedades">
                        <img src="/imgAdidas/AdidasSimbolo.png" alt="" class="fotoSimbolo d-none d-sm-inline">
                    </router-link>
                </article>

                <article class="col-6 col-md-3 text-center">
                    <router-link :to="{ name: 'products', query: { marca: 'Puma' } }" class="d-block text-decoration-none transition-transform hover-scale">
                        <img src="/imgPuma/logospuma.jpg" alt="Puma" class="fotoMasNovedades">
                        <img src="/imgPuma/SimboloPuma.png" alt="" class="fotoSimbolo d-none d-sm-inline">
                    </router-link>
                </article>

                <article class="col-6 col-md-3 text-center">
                    <router-link :to="{ name: 'products', query: { marca: 'Asics' } }" class="d-block text-decoration-none transition-transform hover-scale">
                        <img src="/imgAsics/asicslogo.jpg" alt="Asics" class="fotoMasNovedades">
                        <img src="/imgAsics/SimboloAsics.png" alt="" class="fotoSimbolo d-none d-sm-inline">
                    </router-link>
                </article>
            </div>
        </section>

        <div id="videos-carusel" class="container-fluid p-0 mt-5 position-relative">
            <div class="video-container position-relative overflow-hidden" style="max-height: 750px;">
                <video src="/vid/Zapatillas nike.mp4" autoplay loop muted class="w-100 h-100 object-fit-cover" aria-label="Video promocional"></video>
                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-center align-items-center bg-dark bg-opacity-25">
                    <h2 class="text-white display-4 fw-bold mb-4">Nuevas Nike Gato 2.0</h2>
                    <router-link :to="promotedProduct ? { name: 'product-detail', params: { id: promotedProduct.id } } : { name: 'products' }" class="btn btn-light btn-lg rounded-pill px-5 fw-bold">Ver producto</router-link>
                </div>
            </div>
        </div>

        <section id="mas-comprados" class="container mt-5 mb-5">
            <h2 class="text-center mb-5 fw-bold">Más comprados</h2>

            <div v-if="isLoading" class="text-center py-5">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </div>

            <div v-else class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
                <div v-for="product in masComprados" :key="product.id" class="col">
                    <div class="card h-100 border-0 shadow-sm product-card transition-transform hover-lift">
                        <router-link :to="{ name: 'product-detail', params: { id: product.id } }" class="card-img-top d-flex align-items-center justify-content-center p-3 bg-white text-decoration-none" style="height: 250px;">
                             <!-- Image Logic Handling needed here if URLs vary -->
                             <img v-if="product.image_url" :src="product.image_url" :alt="product.nombre" class="img-fluid" style="max-height: 200px; object-fit: contain;">
                             <div v-else class="text-muted d-flex flex-column align-items-center"><i class="bi bi-image fs-1"></i><span>Sin imagen</span></div>
                        </router-link>
                        <div class="card-body text-center d-flex flex-column">
                             <h5 class="card-title text-truncate"><router-link :to="{ name: 'product-detail', params: { id: product.id } }" class="text-dark text-decoration-none">{{ product.nombre }}</router-link></h5>
                             <p class="card-text fw-bold mb-auto">{{ Number(product.precio).toFixed(2) }} €</p>
                             
                             <div class="d-flex gap-2 mt-3">
                                <router-link :to="{ name: 'product-detail', params: { id: product.id } }" class="btn btn-dark flex-grow-1">Ver</router-link>
                             </div>
                        </div>
                    </div>
                </div>
                <div v-if="masComprados.length === 0" class="col-12 text-center">
                    <p>No hay productos destacados.</p>
                </div>
            </div>
        </section>
    </div>
</template>

<style scoped>
.hover-scale:hover {
    transform: scale(1.05);
}
.hover-lift:hover {
    transform: translateY(-5px);
}
.transition-transform {
    transition: transform 0.3s ease;
}
</style>
