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
        for (let param in cleaned) {
            if (typeof cleaned[param] === 'string' && cleaned[param].includes(',')) {
                cleaned[param] = cleaned[param].split(',');
            } else if (typeof cleaned[param] === 'string') {
                cleaned[param] = [cleaned[param]];
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

    // --- API Interactions ---

    //Funcion para obtener los productos con filtro o sin
    const fetchProducts = (query) => {
        store.getProducts(query);
    }

    // --- Lifecycle Hooks ---



    // --- Watchers ---

    //Funcion debug para ver que productos nuevos se han obtenido en el fetch
    watch(products, (newProducts) => {
        console.log("Products updated in store:", newProducts);
    }, { deep: true });

    watch(() => route.query, () => {
        initialQuery.value = route.query && Object.keys(route.query).length > 0 
        ? cleanURLParams(route.query) 
        : null;
    }, { immediate: true, deep: true})

    onMounted(() => {
        if(Object.keys(route.query).length === 0) fetchProducts();
    })

</script>


<template>
    <div class="main-container">

        <FilterSideBar :initialQuery="initialQuery" :metaData="metaData" @filter="fetchProducts"></FilterSideBar>


        <div class="products-container">
            <div v-if="products.length === 0" class="no-products">
                No se han encontrado productos o no se han cargado todavía.
            </div>
            <div class="product" v-for="(product, index) in products" :key="index">
                <div class="product-img-container">
                    <img :src="getProductImgUrl(product)" class="product-img" alt="img product">
                </div>
                <div class="product-details-container">
                    <p class="product-name"> {{ product.nombre }} </p>
                    <p class="product-price"> {{ product.precio }} </p>
                </div>
                <a href="#" class="button">Ver producto</a>
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

    .products-container {
        grid-area: products;
        width: 90%;
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
    }

    .product-img-container {
        text-align: center;
    }

    .product-img {
        width: 100%;
        height: 300px;
    }


    .product-name {
        font-size: 20px;
    }

    .product-price {
        font-size: 18px;
    }



</style>