<script setup>
    import { useAuthStore } from '../stores/authStore';
import { useCartStore } from '../stores/cartStore';
    import { useWishlistStore } from '../stores/wishlistStore';
    import { computed, onMounted } from 'vue';
    
    const cartStore = useCartStore();
    const wishlistStore = useWishlistStore();
    const authStore = useAuthStore();
    
    const cart = computed(() => cartStore.items);
    const wishlist = computed(() => wishlistStore.wishlistItems);
    
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

    onMounted(async () => {
        if(authStore.isAuthenticated) await wishlist.getWishlistAction();
    })
    

</script>

<template>
    <div class="main-container">
        <h1>Mi carrito</h1>
        <div v-if="cartStore.items.length > 0" class="cart-table-wrapper">
            <table class="cart-container">
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
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
                        {{ Number((product.precio).toFixed(2)) }}
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

        <div class="wishlist-container">
            <h2>Mi lista de deseos</h2>
            
            <div v-if="!authStore.isAuthenticated" class="no-logued-container">
                <h1>¡No estas logueado!</h1>
                <p>Debes iniciar sesion para poder obtener su lista de deseos</p>
                <router-link to="/login" class="back-to-products-link">Iniciar sesion</router-link>
            </div>

            <div v-else-if="!wishlist.length" class="no-wishlist-container">
                <h1>¡Tu lista de deseos esta vacia!</h1>
                <p>Actualmente no tienes ningun producto en tu lista de deseos</p>
                <router-link to="/products" class="back-to-products-link" style="width: 60%; ">Busca productos que deseas comprar</router-link>
            </div>

            <div v-else class="wishlist-products">
                <div v-for="(wishlistItem, index) in wishlist" class="wishlistItem">

                </div>
            </div>

            
        </div>
    </div>

</template>

<style scoped>

    /* Contenedor principal */
.main-container {
    display: grid;
    padding: 10px 20px;
    grid-template-rows: auto 1fr auto;
    min-height: 100vh;
    width: 100%;
}

.main-container > h1 {
    font-size: 50px;
}

.cart-table-wrapper {
    width: 100%;
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

.wishlist-container{
    margin-bottom: 5em;
}

.wishlist-container > h2{
    font-size: 50px;
}

.no-logued-container {
    margin: 5em;
    display: grid;
    grid-template-rows: 1fr 1fr 1fr;
    text-align: center;
    align-items: center;
    justify-content: center;
}

.no-logued-container p{
    margin: 0;
}

.no-wishlist-container {
    margin: 5em;
    display: grid;
    grid-template-rows: 1fr 1fr 1fr;
    text-align: center;
    align-items: center;
    justify-items: center;
    justify-content: center;
    
}

.no-wishlist-container p{
    margin: 0;
}

.wishlist-products {
    display: grid;
    grid-template-columns: auto;
    grid-template-rows: 1fr;
    column-gap: 40px;
    overflow-x: auto;
}







</style>