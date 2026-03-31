import { defineStore } from 'pinia'
import { fetchReviewsFromProduct } from '../services/api.js';
import { useMessageStore} from '../stores/messageStore.js';



export const useReviewsStore = defineStore('reviews', {
    state : () => ({
        reviews: []
    }),
    actions: {
        async getReviewsFromProduct(id) {
            const response = await fetchReviewsFromProduct(id);

            if (response.success) {
                this.addMessageAction("success", response.message)
                this.reviews = response.data.data;
            } else {
                this.addMessageAction('error', response.message);
                return response
            }
        },
        async addReviewAction(data, productid) {
            // const response = addReview()
        },
        async addMessageAction(type, message) {
            const messageStore = useMessageStore();
            messageStore.addMessage({ type, message });
        }
    }
})