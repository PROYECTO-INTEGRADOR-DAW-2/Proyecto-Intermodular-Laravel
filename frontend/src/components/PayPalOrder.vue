<script setup>
    import { useCartStore } from '../stores/cartStore';
    const cartStore = useCartStore();
    const cart = computed(() => cartStore.totalCart);


    const renderPayPalButtons = () => {
        window.paypal.Buttons({
            // Se dispara cuando el usuario hace clic en el botón amarillo
            createOrder: async () => {
                // Llamamos a nuestro controlador de Laravel
                const response = await axios.post('/api/checkout', { metodo_pago: 'paypal' });
                return response.data.id; // El ID que generó nuestro PayPalService
            },

            // Se dispara cuando el usuario acepta el pago en la ventana de PayPal
            onApprove: async (data) => {
                // Aquí llamarías a otra ruta de Laravel para "Capturar" el dinero finalmente
                alert('¡Pago autorizado! ID de orden: ' + data.orderID);
            }

    }).render('#paypal-button-container');

  
    onMounted(() => {
        // Cargamos el SDK dinámicamente
        const script = document.createElement("script");
        script.src = `https://www.paypal.com/sdk/js?client-id=TU_CLIENT_ID&currency=EUR`;
        script.addEventListener("load", renderPayPalButtons);

        document.body.appendChild(script);
    });
};


</script>


<template>

    <div id="paypal-button-container"></div>

</template>


<style scoped>

</style>