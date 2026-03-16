<script setup>
import { ref, onMounted } from 'vue';
import api from '../services/api';

const orders = ref([]);
const isLoading = ref(true);
const expandedOrders = ref({});

onMounted(async () => {
    try {
        const response = await api.get('/orders');
        orders.value = response.data;
        // Expand first order by default
        if (orders.value.length > 0) {
            expandedOrders.value[orders.value[0].id] = true;
        }
    } catch (error) {
        console.error('Error fetching orders:', error);
    } finally {
        isLoading.value = false;
    }
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' });
};

const toggleOrder = (id) => {
    expandedOrders.value[id] = !expandedOrders.value[id];
};

const statusClass = (status) => {
    const map = {
        delivered: 'bg-success',
        pending: 'bg-warning text-dark',
        processing: 'bg-primary',
        cancelled: 'bg-danger',
        shipped: 'bg-info text-dark',
    };
    return map[status?.toLowerCase()] || 'bg-secondary';
};

const statusLabel = (status) => {
    const map = {
        delivered: 'Entregado',
        pending: 'Pendiente',
        processing: 'En proceso',
        cancelled: 'Cancelado',
        shipped: 'Enviado',
    };
    return map[status?.toLowerCase()] || status;
};
</script>

<template>
    <div class="container py-5">
        <div class="d-flex align-items-center mb-5">
            <i class="bi bi-bag-check fs-2 me-3 text-dark"></i>
            <div>
                <h1 class="mb-0 fw-bold">Mis Pedidos</h1>
                <p class="text-muted mb-0">Historial completo de tus compras</p>
            </div>
        </div>

        <div v-if="isLoading" class="text-center py-5">
            <div class="spinner-border text-dark" role="status"><span class="visually-hidden">Cargando...</span></div>
            <p class="mt-3 text-muted">Cargando tus pedidos...</p>
        </div>

        <div v-else-if="orders.length > 0">
            <div v-for="order in orders" :key="order.id" class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <!-- Order Header -->
                <div class="card-header bg-white border-0 p-4 cursor-pointer" @click="toggleOrder(order.id)" style="cursor: pointer;">
                    <div class="row align-items-center g-3">
                        <div class="col-6 col-md-3">
                            <div class="text-muted small text-uppercase fw-bold mb-1">Pedido Nº</div>
                            <div class="fw-bold fs-5">#{{ order.id }}</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="text-muted small text-uppercase fw-bold mb-1">Fecha</div>
                            <div class="fw-bold">{{ formatDate(order.created_at) }}</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="text-muted small text-uppercase fw-bold mb-1">Total</div>
                            <div class="fw-bold fs-5 text-dark">{{ Number(order.total).toFixed(2) }} €</div>
                        </div>
                        <div class="col-6 col-md-3 d-flex align-items-center justify-content-between">
                            <span :class="['badge fs-6 px-3 py-2 rounded-pill', statusClass(order.status)]">
                                {{ statusLabel(order.status) }}
                            </span>
                            <i :class="['bi ms-3 fs-5', expandedOrders[order.id] ? 'bi-chevron-up' : 'bi-chevron-down']"></i>
                        </div>
                    </div>
                </div>

                <!-- Order Items (collapsible) -->
                <div v-if="expandedOrders[order.id]" class="card-body px-4 pb-4 pt-0">
                    <hr class="mt-0 mb-4">
                    <div v-for="item in order.items" :key="item.id"
                         class="d-flex align-items-center p-3 mb-3 bg-light rounded-3">
                        <div class="bg-white rounded-3 shadow-sm d-flex align-items-center justify-content-center me-4 flex-shrink-0"
                             style="width: 80px; height: 80px;">
                            <img v-if="item.product?.image_url" 
                                 :src="item.product.image_url" 
                                 :alt="item.product?.nombre"
                                 class="img-fluid"
                                 style="max-height: 70px; object-fit: contain;">
                            <i v-else class="bi bi-box-seam fs-2 text-muted"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="fw-bold mb-1">{{ item.product?.nombre || 'Producto eliminado' }}</h6>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-rulers me-1"></i>Talla: {{ item.size || 'N/A' }}
                                </span>
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-hash me-1"></i>Cantidad: {{ item.quantity }}
                                </span>
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-tag me-1"></i>{{ Number(item.price).toFixed(2) }} € / ud.
                                </span>
                            </div>
                        </div>
                        <div class="ms-3 text-end fw-bold fs-5 text-dark flex-shrink-0">
                            {{ Number(item.subtotal).toFixed(2) }} €
                        </div>
                    </div>

                    <!-- Order total row -->
                    <div class="d-flex justify-content-end mt-3">
                        <div class="bg-dark text-white rounded-3 px-5 py-3 text-end">
                            <span class="small text-white-50 d-block">TOTAL DEL PEDIDO</span>
                            <span class="fs-4 fw-bold">{{ Number(order.total).toFixed(2) }} €</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="text-center py-5 bg-light rounded-4 shadow-sm">
            <i class="bi bi-bag-x fs-1 text-muted mb-3 d-block"></i>
            <h3 class="fw-bold">No tienes pedidos aún</h3>
            <p class="text-muted mb-4">¡Explora nuestros productos y realiza tu primera compra!</p>
            <router-link :to="{ name: 'products' }" class="btn btn-dark px-5 py-2 rounded-pill">
                <i class="bi bi-cart me-2"></i>Ver Productos
            </router-link>
        </div>
    </div>
</template>

<style scoped>
.card-header:hover {
    background-color: #f8f9fa !important;
}
</style>
