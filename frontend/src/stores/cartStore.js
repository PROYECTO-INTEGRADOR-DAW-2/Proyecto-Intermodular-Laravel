import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useCartStore = defineStore('cart', () => {
    // 1. Estado: La lista de productos en el carrito
    const items = ref(JSON.parse(localStorage.getItem('cart')) || []);

    // 2. Getters: Cálculos derivados (totales, cantidad de items)
    const totalCart = computed(() => {
        return items.value.reduce((acc, item) => acc + (item.precio * item.quantity), 0);
    });

    const countItems = computed(() => {
        return items.value.reduce((acc, item) => acc + item.quantity, 0);
    });

    // 3. Acciones: Lógica para modificar el carrito
    const addToCart = (product, quantity, size) => {
        const index = items.value.findIndex(p => p.id === product.id);
        
        if (index !== -1) {
            items.value[index].quantity = quantity;
            items.value[index].subtotal = items.value[index].precio * quantity;
        } else {
            let subtotal = parseFloat(product.precio) * quantity;
            let newProductCart = { ...product, quantity, size, subtotal}
            items.value.push(newProductCart);
            
        }
        saveToLocalStorage();
    };

    const removeFromCart = (productId) => {
        items.value = items.value.filter(p => p.id !== productId);
        saveToLocalStorage();
    };

    const updateCart = (productId, quantity) => {
        let index = items.value.findIndex( p => p.id === productId );

        if (index !== -1) {
            items.value[index].quantity = quantity;
            items.value[index].subtotal = items.value[index].precio * quantity
        }

        saveToLocalStorage()
    }

    const emptyCart = () => {
        items.value.splice(0);
    } 

    // Función auxiliar para no perder los datos al refrescar
    const saveToLocalStorage = () => {
        localStorage.setItem('cart', JSON.stringify(items.value));
    };

    return { items, totalCart, countItems, addToCart, removeFromCart, emptyCart, updateCart};
});