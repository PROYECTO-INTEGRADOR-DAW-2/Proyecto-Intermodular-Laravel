<script setup>
    import { onMounted } from 'vue';

    const props = defineProps({
        info: Object
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

    let timeoutId = null;

    const removeMessageDelay = () => {
        timeoutId = setTimeout(() => emit('close', props.info?.id), 3000);
    }

    const removeMessageTriggered = () => {
        if (timeoutId) clearTimeout(timeoutId);
        emit('close', props.info?.id);
    }


    const emit = defineEmits(['close']);

    onMounted(() => {
        if (props.info?.type.toLowerCase() === "success") removeMessageDelay();
    });
    

</script>

<template>
    <div class="message" :class="getMessageClass(props.info?.type)">
        <p>{{ props.info?.message }}</p>
        <button class="remove-button" @click="removeMessageTriggered">X</button>
    </div>

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
        grid-template-columns: auto auto;
        width: 100%;
        padding: 20px;
        animation: slide-in 0.5s ease;
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

    .remove-button {
        color: white;
        background-color: transparent;
        padding: 0;
        width: 50%;
    }




  

</style>