<script setup>
    import { useProductsStore } from '../stores/productsStore.js';
    import { computed, onMounted, ref } from 'vue';
    import { watch } from 'vue';

    const store = useProductsStore();
    const products = computed(() => store.products);
    const query = ref({
        name: ""
    })
    

    //Funcion para generar URL de la imagen
    const getProductImgUrl = (product) => {
        const productBrand = product?.marca.charAt(0).toUpperCase() + product?.marca.slice(1);;
        const productCategory = product?.categoria.toLowerCase();
        const productFileName = product?.img;

        const url = `/img/${productBrand}/${productCategory}/${productFileName}`;

        return url;
    }

    const fetchProducts = () => {
        console.log("Fetching products...");
        store.getProducts({})
    }

    onMounted(() => {
        console.log("ProductsView mounted");
        fetchProducts();
        console.log("Current products in store:", products.value);
    });

    
    watch(products, (newProducts) => {
        console.log("Products updated in store:", newProducts);
    }, { deep: true });
    




</script>


<template>
    <div class="main-container">

        <div class="filter-container">
            <div class="filter">
                <form method="get">
                    <label for="nombre">Nombre</label>
                    <input type="text" name="nombre" v-bind="query.name">

                </form>
            </div>
        </div>


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
        grid-template-areas: "filter" "products";
    }

    .products-container {
        grid-area: products;
        width: 90%;
        gap: 10px 10px;
        justify-self: center;
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        grid-template-rows: auto;
    }

    .product {
        border-radius: 10px;
        width: 100%;
        height: 100%;
        display: grid;
        grid-template-columns: 1fr;
        grid-template-rows: 1fr;
    }

    .product-img-container {
        text-align: center;
    }

    .product-img {
        width: 300px;
        height: 300px;
    }

    .product-name {
        font-size: 20px;
    }

    .product-price {
        font-size: 18px;
    }

</style>