<script setup>
    import { useAuthStore } from '../stores/authStore';
    import { useCartStore } from '../stores/cartStore';
    import { useWishlistStore } from '../stores/wishlistStore';
    import { computed, onMounted, watch, ref } from 'vue';
    
    const cartStore = useCartStore();
    const wishlistStore = useWishlistStore();
    const authStore = useAuthStore();
    
    const cart = computed(() => cartStore.items);
    const wishlist = computed(() => wishlistStore.wishlistItems);

    const carousel = ref(null)

    const scroll = (direction) => {
        const scrollAmount = 350; 
        if (direction === 'left') {
            carousel.value.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
        } else {
            carousel.value.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        }
    };
    
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
        if(authStore.isAuthenticated) await wishlistStore.getWishlistAction();
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
                <router-link class="purchase-cart" to="/checkout">Proceder al pago</router-link>
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

            <div v-else class="wishlist-products" ref="carousel">
                <div class="product" v-for="(wishlistItem, index) in wishlist" :key="index" > 
                    <button class="delete-from-wishlist-button" @click="wishlistStore.toggleWishlistItemAction(wishlistItem.id)">x</button>
                    <div class="product-img-container">
                        <img :src="getProductImgUrl(wishlistItem)" class="product-img" alt="img product">
                        <p v-if="wishlistItem.oferta" class="oferta-badge">Oferta</p>
                    </div>
                    <div class="product-badges">
                        <p>{{ wishlistItem.sexo }}</p>
                        <p>{{ wishlistItem.categoria }}</p>
                    </div>
                    <div class="product-details-container">
                        <p class="product-name"> {{ wishlistItem.nombre }} </p>
                        <p class="product-price"> {{ wishlistItem.precio }} €</p>
                    </div>
                    <router-link :to="{ name: 'product-details', params: { id: wishlistItem.id } }" class="button">Ver producto</router-link>
                </div>
            </div>

            <div class="carousel-controls">
                <button @click="scroll('left')"><i class="bi bi-chevron-left"></i></button>
                <button @click="scroll('right')"><i class="bi bi-chevron-right"></i></button>
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
    display: grid;
    grid-template-columns: auto auto;
    justify-content: space-between;
    align-items: center;
    color: white;
    background-color: #1F1F1F;
    padding: 20px;
    position: sticky;
    bottom: 0;
}

.purchase-cart {
    border: none;
    background-color: #D72631;
    color: white;
    width: auto;
    font-size: 20px;
    padding: 10px;
    border-radius: 8px;
    text-decoration: none;
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

.wishlist-container {
    margin-bottom: 5em;
    width: 100%;       /* Asegura que ocupe todo el ancho disponible */
    min-width: 0;      /* CRÍTICO: permite que los hijos desborden sin empujar el contenedor */
    overflow: hidden;  /* Evita que el contenedor entero crezca */
}

.wishlist-container > h2{
    font-size: 50px;
}

.wishlist-products {
    padding: 30px;
    display: grid;
    grid-auto-flow: column;     
    grid-auto-columns: 350px;   
    gap: 30px;
    overflow-x: auto;           
    width: 100%; 
    scroll-snap-type: x mandatory;
    overscroll-behavior-x: contain;
    scrollbar-width: thin;
}

.carousel-controls {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    margin-top: 10px;
}
.carousel-controls button {
    background: #1F1F1F;
    color: white;
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    cursor: pointer;
    transition: background 0.3s;
}
.carousel-controls button:hover {
    background: #D72631; /* Un color de acento */
}


    .product {
        scroll-snap-align: start;
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
        position: relative;
    }

    .delete-from-wishlist-button {
        z-index: 1;
        position: absolute;
        top: 5px;
        left: 5px;
        border: none;
        border-radius: 50%;
        height: 30px;
        width: 30px;
        background-color: #D72631;
        color: white;
    }

    .product:hover {
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.4);
        transform: translateY(-5px);
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







</style>