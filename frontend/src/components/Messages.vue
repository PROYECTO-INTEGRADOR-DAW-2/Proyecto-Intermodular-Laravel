<script setup>
    import { useProductsStore } from '../stores/productsStore';
    import { useAuthStore } from '../stores/authStore';
    import { computed } from 'vue';

    const productsStore = useProductsStore();
    const authStore = useAuthStore();

    const messages = computed(() => [...productsStore.messages, ...authStore.messages]);

    

    const autoRemoveMessage = (index) => {
        setTimeout(() => {
            if (index > -1) messages.splice(index, 1);
        }, 500);
    }


</script>

<template>

    <div class="messages-container">
        <div v-for="(mensaje, index) in messages" class="message" :class="getMessageClass(mensaje.type)" >
            <p>{{ mensaje.message }}</p>
        </div>
    </div>

</template>

<style scoped>

    

    .messages-container {
        display: grid;
        grid-template-columns: 1fr;
        position: fixed;
        width: 350px;
    }

    

    .message p{
        margin: 0;
    }

    @keyframes slide-in {
        from {
            opacity: 0;
            transform: translateX(-100%);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }



</style>