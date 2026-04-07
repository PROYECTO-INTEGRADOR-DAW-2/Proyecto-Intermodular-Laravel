import { defineStore } from 'pinia'
import { fetchWishlist, deleteWishlistItem, addWishlistItem } from '../services/api.js';
import { useMessageStore} from '../stores/messageStore.js';

export const useWishlistStore = defineStore('wishlist', {
    state: () => ({
        wishlistItems: []
    }), 
    actions: {
        async getWishlistAction() {
            const response = await fetchWishlist();

            if (response.success) {
                this.addMessageAction("success", response.message);
                this.wishlistItems = response.data;
            } else {
                this.addMessageAction("error", response.message)
                return response;
            }
        },

        async deleteWishlistItemAction(idItem) {
            const response = await deleteWishlistItem(idItem);

            if (response.success) {
                this.addMessageAction("success", response.message);
                this.getWishlistAction();
            } else {
                this.addMessageAction("error", response.message);
                return response
            }
        },

        async addWishlistItemAction(product) {
            const response = await addWishlistItem(product);

            if (response.success) {
                this.addMessageAction("success", response.message);
                this.getWishlistAction();
            } else {
                this.addMessageAction("error", response.message);
                return response
            }
        },

        addMessageAction(type, message) {
            const messageStore = useMessageStore();
            messageStore.addMessage({ type, message });
        }
    }
});