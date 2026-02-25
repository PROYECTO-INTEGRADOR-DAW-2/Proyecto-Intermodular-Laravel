import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

export const useCartStore = defineStore('cart', () => {
    // State: Array of { product_id, quantity, size }
    // We sync this with LocalStorage for persistence
    const items = ref(JSON.parse(localStorage.getItem('cart_items')) || [])

    // Detailed items (fetched from API) for display
    const details = ref([])
    const total = ref(0)
    const isLoading = ref(false)

    // Buy Now bypass state
    const buyNowItem = ref(null)

    // Getters
    const count = computed(() => items.value.reduce((acc, item) => acc + item.quantity, 0))

    // Actions
    const saveToLocalStorage = () => {
        localStorage.setItem('cart_items', JSON.stringify(items.value))
    }

    const addToCart = (product, size, quantity = 1) => {
        const existingItem = items.value.find(i => i.product_id === product.id && i.size === size)

        if (existingItem) {
            existingItem.quantity += quantity
        } else {
            items.value.push({
                product_id: product.id,
                quantity: quantity,
                size: size
            })
        }
        saveToLocalStorage()
        fetchDetails() // Refresh details
    }

    const removeFromCart = (index) => {
        items.value.splice(index, 1)
        saveToLocalStorage()
        fetchDetails()
    }

    const updateQuantity = (index, quantity) => {
        if (quantity < 1) return
        items.value[index].quantity = quantity
        saveToLocalStorage()
        fetchDetails()
    }

    const updateSize = (index, size) => {
        items.value[index].size = size
        saveToLocalStorage()
        fetchDetails()
    }

    const clearCart = () => {
        items.value = []
        details.value = []
        total.value = 0
        saveToLocalStorage()
    }

    const setBuyNowItem = (product, size, quantity = 1) => {
        buyNowItem.value = {
            product_id: product.id,
            name: product.nombre,
            size: size,
            quantity: quantity,
            price: product.precio,
            subtotal: product.precio * quantity
        }
    }

    const clearBuyNowItem = () => {
        buyNowItem.value = null
    }

    const fetchDetails = async () => {
        if (items.value.length === 0) {
            details.value = []
            total.value = 0
            return
        }

        isLoading.value = true
        try {
            // Map items to send minimal data needed for API to reconstruct details
            const payload = items.value.map(item => ({
                id: item.product_id,
                quantity: item.quantity,
                size: item.size
            }))

            const response = await axios.post('/api/cart/details', { items: payload })
            details.value = response.data.items
            total.value = response.data.total
        } catch (error) {
            console.error('Error fetching cart details:', error)
        } finally {
            isLoading.value = false
        }
    }

    return {
        items,
        details,
        total,
        isLoading,
        count,
        addToCart,
        removeFromCart,
        updateQuantity,
        updateSize,
        clearCart,
        fetchDetails,
        buyNowItem,
        setBuyNowItem,
        clearBuyNowItem
    }
})
