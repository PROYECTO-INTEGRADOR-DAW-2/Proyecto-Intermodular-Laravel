<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import ProductFilter from '@/components/ProductFilter.vue';

const route = useRoute();
const router = useRouter();

const products = ref([]);
const metadata = ref({ marcas: [], categories: [], sexos: [], maxPrice: 1000 });
const links = ref({});
const meta = ref({});
const isLoading = ref(true);

const fetchProducts = async (params = {}) => {
    isLoading.value = true;
    try {
        // Merge route query with new params
        const queryParams = { ...route.query, ...params };
        
        // Clean array params for URL
        const backendParams = {};
        for (const key in queryParams) {
             if (Array.isArray(queryParams[key])) {
                 backendParams[key] = queryParams[key];
             } else {
                 backendParams[key] = queryParams[key];
             }
        }

        const response = await axios.get('/api/products', { params: backendParams });
        products.value = response.data.data;
        links.value = response.data.links;
        meta.value = response.data.meta;
        
        // Metadata for filters (marcas, sexos, etc.) is in response.data.meta because of ->additional(['meta' => ...])
        if (response.data.meta) {
             metadata.value = response.data.meta;
        }

    } catch (error) {
        console.error('Error fetching products:', error);
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    fetchProducts();
});

// Watch route changes (e.g. back button or search updates)
watch(() => route.query, (newQuery) => {
    fetchProducts(newQuery); // actually we can just call fetchProducts() as it uses route.query
});

const handleFilterChange = (filters) => {
    // Update router query which triggers watch -> fetch
    // Remove empty filters
    const query = {};
    for (const key in filters) {
        if (Array.isArray(filters[key]) && filters[key].length > 0) {
            query[key] = filters[key]; // Vue router handles arrays
        } else if (!Array.isArray(filters[key]) && filters[key] !== '' && filters[key] !== 0 && filters[key] !== false) {
             if (key === 'max_price' && filters[key] === metadata.value.maxPrice) continue;
             query[key] = filters[key];
        }
    }
    router.push({ name: 'products', query });
};

const changePage = (url) => {
    if (!url) return;
    const urlObj = new URL(url);
    const page = urlObj.searchParams.get('page');
    router.push({ name: 'products', query: { ...route.query, page } });
};
</script>

<template>
    <div class="container-fluid px-4 px-lg-5 my-5">
        <div class="row">
            <!-- Sidebar (Desktop) -->
            <aside class="col-lg-3 col-xl-2 mb-4 d-none d-lg-block">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                         <ProductFilter :metadata="metadata" :initial-filters="route.query" @filter-change="handleFilterChange" />
                    </div>
                </div>
            </aside>
            
            <!-- Mobile Filters (Offcanvas trigger would go here, simplified for now) -->
            <div class="col-12 d-lg-none mb-3">
                 <button class="btn btn-dark w-100" type="button" data-bs-toggle="collapse" data-bs-target="#mobileFilters">
                     Filtros
                 </button>
                 <div class="collapse mt-2" id="mobileFilters">
                      <div class="card card-body">
                           <ProductFilter :metadata="metadata" :initial-filters="route.query" @filter-change="handleFilterChange" />
                      </div>
                 </div>
            </div>

            <!-- Product List -->
            <div class="col-lg-9 col-xl-10">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">Productos</h2>
                    <span class="text-muted small">{{ meta.total }} resultados</span>
                </div>
                
                <div v-if="isLoading" class="text-center py-5">
                    <div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>
                </div>

                <div v-else class="row">
                    <div v-for="product in products" :key="product.id" class="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-3 mb-4">
                        <div class="card h-100 shadow-sm border-0 product-card hover-lift">
                             <div class="card-img-top d-flex align-items-center justify-content-center p-3 position-relative bg-white" style="height: 250px;">
                                  <span v-if="product.oferta" class="badge bg-danger position-absolute top-0 start-0 m-3">Oferta</span>
                                  <router-link :to="{ name: 'product-detail', params: { id: product.id } }" class="d-flex align-items-center justify-content-center h-100 w-100 text-decoration-none">
                                      <img v-if="product.image_url" :src="product.image_url" :alt="product.nombre" class="img-fluid" style="width: 100%; height: 100%; object-fit: cover;">
                                      <div v-else class="text-muted d-flex flex-column align-items-center"><i class="bi bi-image fs-1"></i><span>Sin imagen</span></div>
                                  </router-link>
                             </div>
                             <div class="card-body d-flex flex-column">
                                 <small class="text-muted mb-1 text-uppercase fw-bold" style="font-size: 0.75rem;">{{ product.marca }}</small>
                                 <h5 class="card-title text-truncate mb-1" :title="product.nombre">
                                     <router-link :to="{ name: 'product-detail', params: { id: product.id } }" class="text-decoration-none text-dark stretched-link">{{ product.nombre }}</router-link>
                                 </h5>
                                 <div class="mb-2">
                                      <span class="badge bg-light text-dark border me-1">{{ product.sexo }}</span>
                                      <span class="badge bg-light text-dark border">{{ product.categoria }}</span>
                                 </div>
                                 <div class="mt-auto pt-3 border-top">
                                      <div class="d-flex justify-content-between align-items-center">
                                          <span class="fw-bold fs-5">{{ Number(product.precio).toFixed(2) }} €</span>
                                          <small v-if="product.oferta" class="text-danger text-decoration-line-through">{{ (product.precio * 1.2).toFixed(2) }} €</small>
                                      </div>
                                 </div>
                             </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <nav v-if="meta.last_page > 1" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item" :class="{ disabled: !links.prev }">
                            <button class="page-link" @click="changePage(links.prev)">Anterior</button>
                        </li>
                        <li class="page-item disabled">
                            <span class="page-link">{{ meta.current_page }} de {{ meta.last_page }}</span>
                        </li>
                        <li class="page-item" :class="{ disabled: !links.next }">
                            <button class="page-link" @click="changePage(links.next)">Siguiente</button>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</template>

<style scoped>
.hover-lift:hover {
    transform: translateY(-5px);
    transition: transform 0.3s ease;
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
}
</style>
