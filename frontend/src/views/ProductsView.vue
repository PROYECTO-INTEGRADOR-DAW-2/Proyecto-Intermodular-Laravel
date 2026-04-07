<script setup>
    import { useProductsStore } from '../stores/productsStore.js';
    import { computed, onMounted, ref, watch } from 'vue';
    import { useRoute } from 'vue-router';
    import FilterSideBar from '../components/FilterSideBar.vue';

    const store = useProductsStore();
    const route = useRoute();
    const products = computed(() => store.products);
    const metaData = computed(() => store.meta);

    // --- Helper Functions (Moved up for initialization) ---

    //Funcion para limpiar los parametros en string con multiples valores separados por coma en HTML
    const cleanURLParams = (query) => {
        const cleaned = { ...query };
        const arrayFields = ['categoria', 'marca', 'deporte', 'altura', 'sexo'];
        
        for (let param in cleaned) {
            if (arrayFields.includes(param)) {
                if (typeof cleaned[param] === 'string' && cleaned[param].includes(',')) {
                    cleaned[param] = cleaned[param].split(',');
                } else if (typeof cleaned[param] === 'string') {
                    cleaned[param] = [cleaned[param]];
                }
            }
        }
        return cleaned;
    }

    //Funcion para generar URL de la imagen
    const getProductImgUrl = (product) => {
        if (!product?.marca || !product?.categoria || !product?.img ) return '/img/no-image.png';
        
        try {
            const productBrand = product.marca.charAt(0).toUpperCase() + product.marca.slice(1);
            const productCategory = product.categoria.toLowerCase();
            const productFileName = product.img;
            return `/img/${productBrand}/${productCategory}/${productFileName}`;
        } catch (e) {
            console.error("Error generating product image URL:", e);
            return '/img/placeholder.png';
        }
    }

    // --- State Initialization ---

    // Inicializamos inmediatamente si hay query para que el hijo lo reciba en su onMounted
    const initialQuery = ref({});
    const currentQuery = ref({});
    

    // --- API Interactions ---

    //Funcion para obtener los productos con filtro o sin
    const fetchProducts = (query) => {
        store.getProducts(query);
        currentQuery.value = {...query};
    }

    // --- Lifecycle Hooks ---



    // --- Watchers ---

    //Funcion debug para ver que productos nuevos se han obtenido en el fetch
    watch(products, (newProducts) => {
        console.log("Products updated in store:", newProducts);
    }, { deep: true });

    watch(() => route.query, () => {
        const cleanedQuery = cleanURLParams(route.query);
        initialQuery.value = route.query && Object.keys(route.query).length > 0 
            ? cleanedQuery 
            : null;
        
        // Ejecutamos el fetch aquí para que sea la única fuente de verdad
        fetchProducts(cleanedQuery);
    }, { immediate: true, deep: true})

    onMounted(() => {
        if(Object.keys(route.query).length === 0) fetchProducts();
    })

</script>


<template>
    <div class="main-container">

        <FilterSideBar :initialQuery="initialQuery" :metaData="metaData" @filter="fetchProducts"></FilterSideBar>
        

        <div class="main-products-container" >
            <p v-if="metaData">Mostrando {{ metaData?.to }} de {{ metaData?.total }} productos</p>

            <div class="products-container">
                <div v-if="products.length === 0" class="no-products">
                    No se han encontrado productos o no se han cargado todavía.
                </div>
                <div class="product" v-for="(product, index) in products" :key="index">
                    <div class="product-img-container">
                        <img :src="getProductImgUrl(product)" class="product-img" alt="img product">
                        <p v-if="product.oferta" class="oferta-badge">Oferta</p>
                    </div>
                    <div class="product-badges">
                        <p>{{ product.sexo }}</p>
                        <p>{{ product.categoria }}</p>
                    </div>
                    <div class="product-details-container">
                        <p class="product-name"> {{ product.nombre }} </p>
                        <p class="product-price"> {{ product.precio }} €</p>
                    </div>
                    <router-link :to="{ name: 'product-details', params: { id: product.id } }" class="button">Ver producto</router-link>
                </div>
            </div>
            <div v-if="metaData" class="pagination-container">
                <router-link :to="{name: 'products', query: { page: metaData.links[0].page}}" class="pagination-button"><i class="bi bi-arrow-left"></i>Pagina anterior</router-link>
                <p>{{ metaData.current_page }}</p>
                <router-link :to="{name: 'products', query: { page: metaData.links[metaData.links.length - 1].page}}">Pagina siguiente<i class="bi bi-arrow-right"></i></router-link>
            </div>
        </div>

        
    </div>
    
</template>

<style scoped>
    .main-container {
        margin-top: 5em;
        display: grid;
        grid-template-areas: "filter products";
        grid-template-columns: 0.5fr 3fr;
    }

    .main-products-container {
        grid-area: products;
        display: grid; 
        grid-template-columns: 1fr; 
        width: 90%;
        justify-self: center;
        row-gap: 20px;
    }

    .main-products-container > p{
        margin: 0;
        justify-self: end;
    }

    .products-container {
        
        gap: 40px 40px;
        justify-self: center;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        grid-template-rows: repeat(4, 1fr);
        
    }

    .product {
        border-radius: 10px;
        width: 100%;
        height: 100%;
        display: grid;
        grid-template-columns: 1fr;
        grid-template-rows: 1fr;
        gap: 20px 0;
        transition: all 0.3s ease;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .product:hover {
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.4);
        transform: translateY(-5px)
    }

    .product-img-container {
        text-align: center;
        position: relative;
    }

    .oferta-badge {
        position: absolute;
        top: 5%;
        right: 5%;
        background-color: red;
        color: white;
        padding: 5px 10px;
        border-radius: 8px;
    }

    .product-img {
        width: 100%;
        height: 300px;
    }

    .product-badges {
        display: grid;
        grid-template-columns: auto auto;
        column-gap: 5px;
        width: 50%;
    }

    .product-badges p {
        margin: 0;
        text-align: center;
        background-color: rgba(128, 128, 128, 0.425);
        border: 2px solid grey;
        border-radius: 8px;
    }


    .product-name {
        font-size: 20px;
    }

    .product-price {
        font-size: 18px;
    }

    .pagination-container {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        justify-content: center;
        text-align: center;
    }

    .pagination-container *{
        margin: 0;
        padding: 20px;
        color: black;
        text-decoration: none;
    }





</style>