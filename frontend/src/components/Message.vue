<script setup>
    import { onMounted } from 'vue';

    const props = defineProps({
        id: Number,
        type: String,
        message: String
    })

    const getMessageClass = (type) => {
            
        switch (type.toLowerCase()) {
            case "success":
                return "success-message";        
            case "error":
                return "error-message";
            case "info":
                return "info-message";
        }
    }



    onMounted(() => {
        if (props.type.toLowerCase === 'success') 
    });

</script>

<template>
    <div class="message"></div>

</template>

<style scoped>

    .error-message {
        background-color: red;
        color: white;
    }

    .success-message {
        background-color: rgb(123, 255, 47);
        color: white;
    }

    .info-message {
        background-color: rgb(68, 123, 224);
        color: white;
    }

    .message {
        display: grid;
        grid-template-columns: auto 1fr;
        width: 100%;
        padding: 20px;
        animation: slide-in 0.5s ease;
    }

</style>