<script setup>
import { ref, onMounted } from 'vue';
import api from '../services/api';

const orders = ref([]);
const isLoading = ref(true);

onMounted(async () => {
    try {
        const response = await api.get('/orders');
        orders.value = response.data;
    } catch (error) {
        console.error('Error fetching orders:', error);
    } finally {
        isLoading.value = false;
    }
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString();
};
</script>

<template>
    <div class="container py-5">
        <h1 class="mb-4">Mis Pedidos</h1>

        <div v-if="isLoading" class="text-center py-5">
            <div class="spinner-border" role="status"><span class="visually-hidden">Cargando...</span></div>
        </div>

        <div v-else-if="orders.length > 0">
            <div v-for="order in orders" :key="order.id" class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <span class="text-muted small">PEDIDO REALIZADO</span>
                        <div class="fw-bold">{{ formatDate(order.created_at) }}</div>
                    </div>
                    <div>
                        <span class="text-muted small">TOTAL</span>
                        <div class="fw-bold">{{ Number(order.total).toFixed(2) }} €</div>
                    </div>
                    <div>
                        <span class="text-muted small">PEDIDO # {{ order.id }}</span>
                        <div class="text-capitalize badge bg-info text-dark">{{ order.status }}</div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div v-for="item in order.items" :key="item.id" class="d-flex align-items-center mb-3">
                        <div class="bg-light rounded p-2 me-3" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-box-seam fs-1 text-secondary"></i> 
                            <!-- In a real scenario, eagerly load product images -->
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">{{ item.product?.nombre || 'Producto eliminado' }}</h5>
                            <p class="text-muted mb-0 small">
                                Cantidad: {{ item.quantity }} | Talla: {{ item.size || 'N/A' }} | Precio: {{ Number(item.price).toFixed(2) }} €
                            </p>
                        </div>
                        <div class="ms-auto fw-bold">
                            {{ Number(item.subtotal).toFixed(2) }} €
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="text-center py-5 bg-light rounded shadow-sm">
            <i class="bi bi-bag-x fs-1 text-muted mb-3"></i>
            <h3>No tienes pedidos aún</h3>
            <p class="text-muted mb-4">¡Explora nuestros productos y realiza tu primera compra!</p>
            <router-link :to="{ name: 'products' }" class="btn btn-dark px-4 py-2">Ver Productos</router-link>
        </div>
    </div>
</template>
