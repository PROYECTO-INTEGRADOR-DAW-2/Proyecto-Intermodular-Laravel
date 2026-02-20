import { defineStore } from 'pinia';
import { getProducts, getProduct } from './services/api.js'

export const useProductsStore = defineStore('products', {
    state: () => ({
        products: [],
        messages: []
    }),
    actions: () => ({
        fetchProducts(query = {}) {
            const response = getProducts(query);

            if (response.success) {
                this.addMensajeAction("success", response.message)
                return response;
                
            } else {
                this.addMensajeAction("error", response.message)
                return false;
            }
        },

        addMensajeAction(type, message) {
            type === "success"
        }
    })
})