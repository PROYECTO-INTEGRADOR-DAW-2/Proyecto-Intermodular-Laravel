<script setup>
import { ref, computed, onUnmounted } from 'vue';
import { useCartStore } from '@/stores/cart';
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';
import api from '@/services/api';
import { useToastStore } from '@/stores/toast';

const cartStore = useCartStore();
const authStore = useAuthStore();
const router = useRouter();
const toastStore = useToastStore();

const checkoutItems = computed(() => {
    return cartStore.buyNowItem ? [cartStore.buyNowItem] : cartStore.details;
});

const checkoutTotal = computed(() => {
    return cartStore.buyNowItem ? cartStore.buyNowItem.subtotal : cartStore.total;
});

onUnmounted(() => {
    cartStore.clearBuyNowItem();
});

const formData = ref({
    address: '',
    city: '',
    postal_code: '',
    phone: '',
    guest_email: ''
});

const payment = ref({
    cardName: '',
    cardNumber: '',
    expiration: '',
    cvv: ''
});

const isSubmitting = ref(false);
const errorMessage = ref('');

const handleSubmit = async () => {
    if (checkoutItems.value.length === 0) {
        errorMessage.value = 'No hay artículos para comprar.';
        toastStore.addToast('No hay artículos para comprar.', 'error');
        return;
    }

    isSubmitting.value = true;
    errorMessage.value = '';

    try {
        const orderData = {
            items: checkoutItems.value.map(item => ({
                product_id: item.product_id,
                quantity: item.quantity,
                size: String(item.size)
            })),
            total: checkoutTotal.value,
            ...formData.value,
            guest_email: authStore.isAuthenticated ? undefined : formData.value.guest_email
        };

        await api.post('/orders', orderData);
        
        if (cartStore.buyNowItem) {
            cartStore.clearBuyNowItem();
        } else {
            cartStore.clearCart();
        }
        
        toastStore.addToast('¡Pedido realizado con éxito!', 'success');
        router.push({ name: 'orders' });

    } catch (error) {
        console.error('Error creating order:', error);
        errorMessage.value = 'Hubo un error al procesar tu pedido. Por favor, inténtalo de nuevo.';
        if (error.response && error.response.data && error.response.data.message) {
             errorMessage.value += ' ' + error.response.data.message;
        }
        toastStore.addToast('Error al procesar el pedido.', 'error');
    } finally {
        isSubmitting.value = false;
    }
};
</script>

<template>
    <div class="container py-5">
        <h1 class="mb-4 text-center">Finalizar Compra</h1>

        <div class="row g-5">
            <div class="col-md-5 col-lg-4 order-md-last">
                <h4 class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-primary">Resumen</span>
                    <span class="badge bg-primary rounded-pill">{{ cartStore.buyNowItem ? 1 : cartStore.count }}</span>
                </h4>
                <ul class="list-group mb-3 shadow-sm rounded-4">
                    <li v-for="(item, index) in checkoutItems" :key="index" class="list-group-item d-flex justify-content-between lh-sm">
                        <div>
                            <h6 class="my-0">{{ item.name }}</h6>
                            <small class="text-muted">Cant: {{ item.quantity }} | Talla: {{ item.size }}</small>
                        </div>
                        <span class="text-muted">{{ Number(item.subtotal).toFixed(2) }} €</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between bg-light fw-bold">
                        <span>Total (EUR)</span>
                        <strong>{{ Number(checkoutTotal).toFixed(2) }} €</strong>
                    </li>
                </ul>
                <div class="d-grid gap-2">
                    <router-link :to="{ name: 'cart' }" class="btn btn-outline-secondary">
                        <i class="bi bi-pencil-square me-2"></i> Editar Carrito
                    </router-link>
                </div>
            </div>
            
            <div class="col-md-7 col-lg-8">
                <h4 class="mb-3">Dirección de Envío</h4>
                <form @submit.prevent="handleSubmit" class="needs-validation">
                    <div class="row g-3">
                        <!-- Email only for guests -->
                        <div v-if="!authStore.isAuthenticated" class="col-12">
                            <label for="guest_email" class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" id="guest_email" v-model="formData.guest_email"
                                   placeholder="tu@email.com" required>
                            <small class="text-muted">Recibirás la confirmación del pedido en este correo</small>
                        </div>

                        <div class="col-12">
                            <label for="address" class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="address" v-model="formData.address" placeholder="Calle Ejemplo 123" required>
                        </div>

                        <div class="col-md-5">
                            <label for="city" class="form-label">Ciudad</label>
                            <input type="text" class="form-control" id="city" v-model="formData.city" placeholder="Madrid" required>
                        </div>

                        <div class="col-md-3">
                            <label for="zip" class="form-label">Código Postal</label>
                            <input type="text" class="form-control" id="zip" v-model="formData.postal_code" placeholder="28001" required>
                        </div>
                         <div class="col-md-4">
                            <label for="phone" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="phone" v-model="formData.phone" placeholder="600123456" required>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h4 class="mb-3">Pago</h4>
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <label for="cc-name" class="form-label">Nombre en la tarjeta</label>
                            <input type="text" class="form-control" id="cc-name" v-model="payment.cardName" required>
                            <small class="text-muted">Nombre completo como aparece en la tarjeta</small>
                        </div>
                        <div class="col-md-6">
                            <label for="cc-number" class="form-label">Número de tarjeta</label>
                            <input type="text" class="form-control" id="cc-number" v-model="payment.cardNumber" placeholder="0000 0000 0000 0000" required>
                        </div>
                        <div class="col-md-3">
                            <label for="cc-expiration" class="form-label">Vencimiento</label>
                            <input type="text" class="form-control" id="cc-expiration" v-model="payment.expiration" placeholder="MM/YY" required>
                        </div>
                        <div class="col-md-3">
                            <label for="cc-cvv" class="form-label">CVV</label>
                            <input type="text" class="form-control" id="cc-cvv" v-model="payment.cvv" placeholder="123" required>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div v-if="errorMessage" class="alert alert-danger">{{ errorMessage }}</div>

                    <button class="w-100 btn btn-success btn-lg shadow-sm" type="submit" :disabled="isSubmitting">
                        <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        <span v-else>Confirmar Pedido</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
