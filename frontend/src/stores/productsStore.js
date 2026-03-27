import { defineStore } from 'pinia';
import { fetchProducts, fetchProduct, fetchMostPurchasedProducts } from '../services/api.js';
import { useMessageStore} from '../stores/messageStore.js';


export const useProductsStore = defineStore('products', {
    state: () => ({
        mostPurchasedProducts: [],
        products: [],
        meta: null,
        debug: true
    }),
    actions: {
        async getProducts(query = {}) {
            const response = await fetchProducts(query);

            if (response.success) {
                this.addMessageAction("success", response.message)
                const payload = response.data.data;
                this.products = payload.data || payload;
                this.meta = response.data.meta || payload.meta || null;
                return response;
            } else {
                this.addMessageAction("error", response.message)
                return false;
            }
        },

        async getProduct(id) {
            const response = await fetchProduct(id);

            if (response.success) {
                return response.data;
            } else {
                this.addMessageAction('error', response.data);
                return response;
            }
        },

        async getMostPurchasedProducts() {
            const response = await fetchMostPurchasedProducts();

            if (response.success) {
                this.debug && this.addMessageAction("success", response.message) 
                return response
            } else {
                this.debug && this.addMessageAction("error", response.message)
            }
        },

        addMessageAction(type, message) {
            const messageStore = useMessageStore();
            messageStore.addMessage({ type, message });
        }
    }
})