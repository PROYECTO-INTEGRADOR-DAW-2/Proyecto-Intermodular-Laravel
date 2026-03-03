import { defineStore } from 'pinia';
import { fetchProducts, fetchMostPurchasedProducts } from '../services/api.js'

export const useProductsStore = defineStore('products', {
    state: () => ({
        mostPurchasedProducts: [],
        products: [],
        messages: [],
        debug: true
    }),
    actions: () => ({
        getProducts(query = {}) {
            const response = fetchProducts(query);

            if (response.success) {
                this.addMensajeAction("success", response.message)
                this.products = response.data;
                return response;
            } else {
                this.addMensajeAction("error", response.message)
                return false;
            }
        },

        getMostPurchasedProducts() {
            const response = getMostPurchasedProducts();

            if (response.success) {
                debug && this.addMensajeAction("success", response.message) 
                return response
            } else {
                debug && this.addMensajeAction("")
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
    })
})