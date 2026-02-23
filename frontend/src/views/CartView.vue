<script setup>
import { ref, onMounted } from 'vue';
import { useCartStore } from '@/stores/cart';
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';
import api from '@/services/api';
import { useToastStore } from '@/stores/toast';

const cartStore = useCartStore();
const authStore = useAuthStore();
const router = useRouter();
const toastStore = useToastStore();

const wishlistItems = ref([]);
const isLoadingWishlist = ref(false);

const fetchWishlist = async () => {
    if (!authStore.isAuthenticated) return;
    isLoadingWishlist.value = true;
    try {
        const response = await api.get('/wishlist');
        wishlistItems.value = response.data.data;
    } catch (error) {
        console.error('Error fetching wishlist:', error);
    } finally {
        isLoadingWishlist.value = false;
    }
};

const removeFromWishlist = async (id) => {
    try {
        await api.post(`/wishlist/${id}`);
        await fetchWishlist(); // Refresh list
        toastStore.addToast('Producto eliminado de la lista de deseos', 'info');
    } catch (error) {
        console.error('Error removing from wishlist:', error);
        toastStore.addToast('Error al eliminar de la lista de deseos', 'error');
    }
};

const moveToCart = (product) => {
    // Default size logic (simplified for wishlist move)
    let size = 'M';
    const catLower = product.categoria ? product.categoria.toLowerCase() : '';
    if (catLower.includes('zapatillas') || catLower.includes('calzado')) {
        size = '42';
    } else if (catLower.includes('calcetines')) {
        size = 'M (38-42)';
    } else {
        size = 'M';
    }
    
    cartStore.addToCart(product, size, 1);
    toastStore.addToast('Producto añadido al carrito (Talla: ' + size + ')', 'success');
};

onMounted(() => {
    cartStore.fetchDetails();
    fetchWishlist();
});

const handleQuantityChange = (index, newQty) => {
    if (newQty < 1) return;
    cartStore.updateQuantity(index, newQty);
};

const handleRemove = (index) => {
    if (confirm('¿Estás seguro de eliminar este producto del carrito?')) {
        cartStore.removeFromCart(index);
    }
};

const handleClean = () => {
    if (confirm('¿Vaciar todo el carrito?')) {
        cartStore.clearCart();
    }
};

const handleCheckout = () => {
    router.push({ name: 'checkout' });
};
</script>

<template>
    <main class="container my-5">
        <h1 class="text-center mb-5">Tu Carrito de Compras</h1>

        <div v-if="cartStore.isLoading" class="text-center py-5">
            <div class="spinner-border" role="status"><span class="visually-hidden">Cargando...</span></div>
        </div>

        <div v-else-if="cartStore.items.length > 0">
            <!-- Desktop View -->
            <div class="d-none d-md-block table-responsive shadow-sm border rounded-4 overflow-hidden mb-5">
                <table class="table table-hover align-middle mb-0 bg-white">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col" class="py-4 ps-5">PRODUCTO</th>
                            <th scope="col" class="py-4 text-center">TALLA</th>
                            <th scope="col" class="py-4 text-center">CANTIDAD</th>
                            <th scope="col" class="py-4 text-center">PRECIO</th>
                            <th scope="col" class="py-4 text-center">SUBTOTAL</th>
                            <th scope="col" class="py-4 text-center pe-5">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in cartStore.details" :key="index">
                            <td class="py-4 ps-5">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-3 p-2 me-4 shadow-sm" style="width: 100px; height: 100px; display: flex; align-items: center; justify-content: center;">
                                         <img v-if="item.image" :src="item.image" :alt="item.name" class="img-fluid" style="max-height: 80px; object-fit: contain;">
                                         <i v-else class="bi bi-image text-muted"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold fs-5">{{ item.name }}</div>
                                        <div class="text-muted small">ID: #{{ item.product_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 text-center fw-bold">{{ item.size }}</td>
                            <td class="py-4 text-center">
                                <input type="number" class="form-control border-2 text-center fw-bold d-inline-block" style="width: 80px;" 
                                       :value="item.quantity" min="1" 
                                       @change="handleQuantityChange(index, $event.target.value)">
                            </td>
                            <td class="py-4 text-center fs-5">{{ Number(item.price).toFixed(2) }} €</td>
                            <td class="py-4 text-center fs-5 fw-bold text-success">{{ Number(item.subtotal).toFixed(2) }} €</td>
                            <td class="py-4 text-center pe-5">
                                <button class="btn btn-outline-danger btn-sm rounded-circle p-2 shadow-sm"  @click="handleRemove(index)">
                                    <i class="bi bi-trash fs-5"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Mobile View -->
            <div class="row row-cols-1 g-4 d-md-none">
                 <div v-for="(item, index) in cartStore.details" :key="index" class="col">
                     <div class="card h-100 shadow border-0 rounded-4 overflow-hidden">
                         <div class="bg-light d-flex align-items-center justify-content-center p-4" style="min-height: 200px;">
                              <img v-if="item.image" :src="item.image" :alt="item.name" class="img-fluid" style="max-height: 150px; object-fit: contain;">
                         </div>
                         <div class="card-body p-4">
                              <div class="d-flex justify-content-between align-items-start mb-3">
                                  <h3 class="fw-bold fs-4 mb-0">{{ item.name }}</h3>
                                  <button class="btn btn-outline-danger btn-sm rounded-circle p-2" @click="handleRemove(index)">
                                      <i class="bi bi-trash fs-5"></i>
                                  </button>
                              </div>
                              <div class="row g-3 mb-3">
                                   <div class="col-6">
                                       <small class="text-muted fw-bold d-block">Talla</small>
                                       <span class="fs-5">{{ item.size }}</span>
                                   </div>
                                   <div class="col-6">
                                       <small class="text-muted fw-bold d-block">Cantidad</small>
                                       <input type="number" class="form-control" :value="item.quantity" min="1" @change="handleQuantityChange(index, $event.target.value)">
                                   </div>
                              </div>
                              <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                   <span class="fw-bold text-success fs-3">{{ Number(item.subtotal).toFixed(2) }} €</span>
                              </div>
                         </div>
                     </div>
                 </div>
            </div>

            <!-- Summary -->
            <div class="row mt-5 justify-content-center justify-content-md-end">
                <div class="col-12 col-md-5 col-xl-4">
                    <div class="card border-0 shadow rounded-4 bg-dark text-white p-4">
                        <div class="d-flex justify-content-between align-items-center mb-0">
                            <span class="fs-3 fw-bold">TOTAL:</span>
                            <span class="fs-1 fw-bold text-success">{{ Number(cartStore.total).toFixed(2) }} €</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-3 mt-4">
                <router-link :to="{ name: 'products' }" class="btn btn-outline-secondary order-2 order-md-1">
                    <i class="bi bi-arrow-left"></i> Continuar comprando
                </router-link>
                <div class="d-flex flex-column flex-md-row gap-2 order-1 order-md-2">
                     <button @click="handleClean" class="btn btn-outline-danger">Vaciar Carrito</button>
                     <button @click="handleCheckout" class="btn btn-success px-4 fw-bold shadow-sm">Finalizar Compra</button>
                </div>
            </div>

        </div>

        <!-- Empty Cart Section -->
        <div v-else class="text-center py-5">
            <h3 class="mb-4 text-muted">Aún no hay ningún producto en tu carrito.</h3>
            <router-link :to="{ name: 'products' }" class="btn btn-dark btn-lg px-4 shadow-sm mb-4">
                <i class="bi bi-cart me-2"></i> Ir a Productos
            </router-link>
        </div>

        <!-- Wishlist Section -->
        <div v-if="authStore.isAuthenticated" class="mt-5">
            <hr class="my-5">
            <h2 class="mb-4 text-center">Tu Lista de Deseos</h2>
            
            <div v-if="isLoadingWishlist" class="text-center py-3">
                <div class="spinner-border" role="status"><span class="visually-hidden">Cargando...</span></div>
            </div>

            <div v-else-if="wishlistItems.length > 0" class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">
                 <div v-for="item in wishlistItems" :key="item.id" class="col">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="bg-white p-3 rounded-top d-flex align-items-center justify-content-center position-relative" style="height: 200px;">
                             <img v-if="item.image_url" :src="item.image_url" class="img-fluid" style="max-height: 100%; object-fit: contain;">
                             <div v-else class="text-muted"><i class="bi bi-image"></i></div>
                             <button @click="removeFromWishlist(item.id)" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 rounded-circle" title="Eliminar de la lista">
                                <i class="bi bi-x"></i>
                             </button>
                        </div>
                        <div class="card-body text-center">
                            <h6 class="card-title text-truncate"><router-link :to="{ name: 'product-detail', params: { id: item.id } }" class="text-decoration-none text-dark">{{ item.nombre }}</router-link></h6>
                            <p class="card-text fw-bold">{{ Number(item.precio).toFixed(2) }} €</p>
                            <button @click="moveToCart(item)" class="btn btn-sm btn-dark w-100">
                                <i class="bi bi-cart-plus me-1"></i> Añadir al Carrito
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="text-center py-4 bg-light rounded">
                <p class="text-muted mb-0">No tienes productos en tu lista de deseos.</p>
            </div>
        </div>
    </main>
</template>
