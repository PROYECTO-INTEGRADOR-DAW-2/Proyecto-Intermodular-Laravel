<script setup>
    import { useMessageStore } from '../stores/messageStore.js';
    import { computed } from 'vue';
    import Message from './Message.vue';

    
    const messageStore = useMessageStore();
    const messages = computed(() => messageStore.messages);
    
    const removeMessage = (id) => {
        messageStore.removeMessage(id);
    }

</script>

<template>

    <div class="messages-container">
        
        <Message v-for="message in messages" :key="message.id" :info="message" @close="removeMessage"></Message>

    </div>

</template>

<style scoped>

    .messages-container {
        display: grid;
        grid-template-columns: 1fr;
        position: fixed;
        width: 350px;
    }

</style>