import { defineStore } from 'pinia';

export const useMessageStore = defineStore('messages', {
    state: () => ({
        messages: [],
    }),
    actions: {
        addMessage(message) {
            const id = Date.now() + Math.random();
            this.messages.push({ ...message, id });
        },

        removeMessage(id) {
            const index = this.messages.findIndex(m => m.id === id);
            if (index !== -1) {
                this.messages.splice(index, 1);
                return true;
            }
            return false;
        }
    }
})