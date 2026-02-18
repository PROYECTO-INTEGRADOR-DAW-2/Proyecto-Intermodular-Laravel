<script setup>
import { ref } from 'vue'
import axios from 'axios'

const isOpen = ref(false)
const isGreetingVisible = ref(true)
const messages = ref([
    { text: '¡Hola! 👋', isUser: false },
    { text: 'Estoy aquí para ayudarte con tus compras.', isUser: false }
])
const inputMessage = ref('')
const isLoading = ref(false)

const toggleChat = () => {
    isOpen.value = !isOpen.value
    if (isOpen.value) {
        isGreetingVisible.value = false
    }
}

const closeGreeting = (e) => {
    e.stopPropagation()
    isGreetingVisible.value = false
}

const sendMessage = async () => {
    const text = inputMessage.value.trim()
    if (!text) return

    messages.value.push({ text, isUser: true })
    inputMessage.value = ''
    isLoading.value = true

    try {
        // Assuming there is a chat endpoint or mock it for now
        // The original code posted to '/chat'
        const response = await axios.post('/api/chat', { message: text }) // Updated to API route if exists
        
        const data = response.data
        if (data.response || data.respuesta) {
            messages.value.push({ text: data.response || data.respuesta, isUser: false })
        } else {
            messages.value.push({ text: 'Error: No response from server.', isUser: false })
        }
    } catch (e) {
        console.error(e)
        // Fallback demo response if API fails
        messages.value.push({ text: 'Lo siento, el servicio de chat no está disponible en este momento.', isUser: false })
    } finally {
        isLoading.value = false
    }
}
</script>

<template>
    <div id="chat-widget">
        <!-- Custom Greeting Bubble -->
        <div v-if="isGreetingVisible" id="chat-greeting">
            <button id="close-greeting" title="Cerrar" @click="closeGreeting">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
            <div id="open-trough-greeting" class="greeting-text" @click="toggleChat">
                Hola. ¿Puedo ayudarte?
            </div>
        </div>

        <!-- Chat Window -->
        <div id="chat-window" :class="{ 'visible': isOpen, 'hidden': !isOpen }">
            <!-- Header -->
            <div class="chat-header">
                <div class="chat-status">
                        <div class="status-dot"></div>
                    <span style="font-weight: 600; font-size: 14px;">Asistente J&A Sports</span>
                </div>
                <button id="chat-close" @click="toggleChat" style="background: none; border: none; color: #9ca3af; cursor: pointer;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            
            <!-- Messages -->
            <div id="chat-messages">
                <div v-for="(msg, index) in messages" :key="index" :class="['message-row', msg.isUser ? 'user-msg' : 'bot-msg']">
                    <div :class="['message-bubble', msg.isUser ? 'user-bubble' : 'bot-bubble']">
                        <p style="margin: 0;" v-html="msg.text.replace(/\n/g, '<br>')"></p>
                    </div>
                </div>
                <div v-if="isLoading" class="message-row bot-msg">
                    <div class="message-bubble bot-bubble">
                        <span style="font-style: italic; color: #6b7280; font-size: 12px;">Escribiendo...</span>
                    </div>
                </div>
            </div>

            <!-- Input -->
            <div class="chat-footer">
                <form @submit.prevent="sendMessage" id="chat-form">
                    <input type="text" v-model="inputMessage" id="chat-input" placeholder="Escribe aquí..." autocomplete="off" :disabled="isLoading">
                    <button type="submit" id="chat-send" :disabled="isLoading">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                    </button>
                </form>
            </div>
        </div>

        <!-- Chat Trigger Button (Red FAB) -->
        <button id="chat-toggle" @click="toggleChat">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                <path fill-rule="evenodd" d="M4.804 21.644A6.707 6.707 0 006 21.75a6.721 6.721 0 003.583-1.029c.774.182 1.584.279 2.417.279 5.322 0 9.75-3.97 9.75-9 0-5.03-4.428-9-9.75-9s-9.75 3.97-9.75 9c0 2.409 1.025 4.587 2.674 6.192.232.226.277.428.254.543a3.73 3.73 0 01-.814 1.686.75.75 0 00.44 1.223zM8.25 10.875a1.125 1.125 0 100 2.25 1.125 1.125 0 000-2.25zM10.875 12a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0zm4.875-1.125a1.125 1.125 0 100 2.25 1.125 1.125 0 000-2.25z" clip-rule="evenodd" />
            </svg>
        </button>
    </div>
</template>

<style scoped>
/* Copied styles from ecommerce.blade.php */
#chat-widget {
    font-family: 'Figtree', sans-serif;
    position: fixed;
    bottom: 20px;
    right: 20px;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    z-index: 9999;
}

#chat-greeting {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
    margin-right: 4px;
    transition: opacity 0.3s ease;
}
.greeting-text {
    background-color: white;
    color: #1f2937;
    padding: 10px 16px;
    border-radius: 9999px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    font-weight: 500;
    font-size: 14px;
    cursor: pointer;
    transition: transform 0.2s;
}
.greeting-text:hover {
    transform: scale(1.05);
}
#close-greeting {
    background-color: white;
    color: #6b7280;
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    cursor: pointer;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
#close-greeting:hover {
    background-color: #f3f4f6;
}

/* Chat Toggle Button (FAB) */
#chat-toggle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background-color: #E53935; /* Red */
    border: none;
    cursor: pointer;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2), 0 4px 6px -2px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.3s, background-color 0.3s;
}
#chat-toggle:hover {
    background-color: #C62828; /* Darker Red */
    transform: scale(1.1);
}
#chat-toggle svg {
    width: 30px;
    height: 30px;
    color: white; /* White icon */
    fill: currentColor;
}

/* Chat Window */
#chat-window {
    position: fixed;
    bottom: 90px;
    right: 20px;
    width: 350px;
    max-width: 90vw;
    height: 500px;
    max-height: 80vh;
    background-color: white;
    border-radius: 12px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    border: 1px solid #e5e7eb;
    transform-origin: bottom right;
    transition: all 0.3s ease;
}
#chat-window.hidden {
    opacity: 0;
    transform: scale(0.9);
    pointer-events: none;
    visibility: hidden;
}
#chat-window.visible {
    opacity: 1;
    transform: scale(1);
    pointer-events: auto;
    visibility: visible;
}

/* Header */
.chat-header {
    background-color: #111827; /* Dark */
    color: white;
    padding: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.chat-status {
    display: flex;
    align-items: center;
    gap: 8px;
}
.status-dot {
    width: 10px;
    height: 10px;
    background-color: #22c55e;
    border-radius: 50%;
    border: 2px solid white;
}

/* Messages Area */
#chat-messages {
    flex: 1;
    padding: 16px;
    overflow-y: auto;
    background-color: #f9fafb;
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.message-row {
    display: flex;
    width: 100%;
}
.message-row.user-msg {
    justify-content: flex-end;
}
.message-row.bot-msg {
    justify-content: flex-start;
}
.message-bubble {
    max-width: 85%;
    padding: 10px 16px;
    font-size: 14px;
    line-height: 1.5;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}
.user-bubble {
    background-color: #E53935; /* Red */
    color: white; /* White text */
    border-radius: 16px 16px 0 16px;
    font-weight: 500;
}
.bot-bubble {
    background-color: white;
    color: #374151;
    border: 1px solid #e5e7eb;
    border-radius: 16px 16px 16px 0;
}

/* Footer/Input */
.chat-footer {
    padding: 12px;
    background-color: white;
    border-top: 1px solid #e5e7eb;
}
#chat-form {
    display: flex;
    gap: 8px;
}
#chat-input {
    flex: 1;
    background-color: #f3f4f6;
    border: none;
    border-radius: 9999px;
    padding: 10px 16px;
    font-size: 14px;
    outline: none;
}
#chat-input:focus {
    background-color: white;
    box-shadow: 0 0 0 2px #E53935;
}
#chat-send {
    background-color: #E53935; /* Red */
    color: white;
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background-color 0.2s;
}
#chat-send:hover {
    background-color: #C62828;
}
</style>
