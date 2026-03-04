import { defineStore } from 'pinia';
import { fetchProducts, fetchMostPurchasedProducts } from '../services/api.js'

export const useProductsStore = defineStore('products', {
    state: () => ({
        mostPurchasedProducts: [],
        products: [],
        messages: [],
        debug: true
    }),
    actions: {
        async getProducts(query = {}) {
            const response = await fetchProducts(query);

            if (response.success) {
                this.addMensajeAction("success", response.message)
                this.products = response.data.data || response.data;
                return response;
            } else {
                this.addMensajeAction("error", response.message)
                return false;
            }
        },

        async getMostPurchasedProducts() {
            const response = await fetchMostPurchasedProducts();

            if (response.success) {
                this.debug && this.addMensajeAction("success", response.message) 
                return response
            } else {
                this.debug && this.addMensajeAction("")
            }
        },

        addMensajeAction(type, message) {
            switch (type) {
                case "success":
                    this.messages.push({type, message})
                    break;
            
                case "error":
                    this.messages.push({type, message})
                    break;
            }
        }
    }
})