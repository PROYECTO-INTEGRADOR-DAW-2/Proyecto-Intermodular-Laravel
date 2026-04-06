<script setup>
    import { useCartStore } from '../stores/cartStore';
    import { computed } from 'vue';
    
    const cartStore = useCartStore();
    
    const cart = computed(() => cartStore.items);
    
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

    const handleQuantityChange = (productId, event) => {
        cartStore.updateCart(productId, parseInt(event.target.value));
    }
    

</script>

<template>
    <div class="main-container">
        <div v-if="cartStore.items.length > 0" class="cart-table-wrapper">
            <table class="cart-container">
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                    <th>Acciones</th>
                </tr>

                <tr v-for="(product, index) in cart">
                    <td>
                        <div style="display: grid; grid-template-columns: 1fr 1fr;">
                            <img :src="getProductImgUrl(product)" style="width: 50px;">
                            <strong>{{ product.nombre }}</strong>
                        </div>
                    </td>

                    <td>
                        <input type="number" min="1" :max="product.stock" :value="product.quantity" name="quantity" id="quantity" @input="handleQuantityChange(product.id, $event)">
                    </td>

                    <td>
                        {{ Number((product.subtotal).toFixed(2)) }}
                    </td>

                    <td>
                        <button type="button" class="btn btn-danger" @click="cartStore.removeFromCart(product.id)">
                                <i class="bi bi-trash"></i>
                        </button>
                    </td>
                    
                </tr>
            </table>

            <div class="cart-summary">
                <strong>Total: <span style="color: green;">{{ Number(cartStore.totalCart).toFixed(2) }}</span></strong>
            </div>
        </div>

        <div v-else class="no-products-container">
            <h1>¡Tu carrito esta vacio!</h1>
            <router-link to="/products" class="back-to-products-link">Comprar productos</router-link>
        </div>
    </div>

</template>

<style scoped>

    /* Contenedor principal */
.main-container {
    display: grid;
    grid-template-rows: auto 1fr auto;
    min-height: 100vh;
    width: 100%;
}

.cart-table-wrapper {
    width: 90%;
    justify-self: center;
    max-height: 60vh;
    overflow-y: auto;
    margin: 20px 0;
    align-self: start;
}

.cart-container {
    width: 100%;
    background-color: #1F1F1F;
    color: white;
    border-collapse: collapse;
    table-layout: fixed;
    display: table;
}

.cart-container tr {
    width: 100%;
    display: table-row; 
}

.cart-container td, .cart-container th {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid #333;
}

.cart-container th {
    background-color: #2D2D2D;
    position: sticky;
    top: 0;
    z-index: 1;
}

.cart-container td:first-child {
    width: 50%;
}


.cart-summary {
    color: white;
    background-color: #1F1F1F;
    padding: 20px;
    position: sticky;
    bottom: 0;
}

.cart-summary *{
    font-size: 30px;
}

.no-products-container {
    display: grid;
    grid-template-rows: 1fr auto;
    row-gap: 20px;
    justify-content: center;
    align-self: center;
}

.back-to-products-link {
    padding: 10px 15px;
    background-color: #1F1F1F;
    color: white;
    border-radius: 8px;
    text-decoration: none;
    text-align: center;
    
}



</style>