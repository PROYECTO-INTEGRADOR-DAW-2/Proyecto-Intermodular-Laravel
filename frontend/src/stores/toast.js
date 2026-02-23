import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useToastStore = defineStore('toast', () => {
    const toasts = ref([]);
    // toast structure: { id: Date.now(), message: string, type: 'success' | 'error' | 'info' }

    const addToast = (message, type = 'info') => {
        const id = Date.now();
        toasts.value.push({ id, message, type });

        // Auto remove after 3 seconds
        setTimeout(() => {
            removeToast(id);
        }, 3000);
    };

    const removeToast = (id) => {
        toasts.value = toasts.value.filter(t => t.id !== id);
    };

    return {
        toasts,
        addToast,
        removeToast
    };
});
