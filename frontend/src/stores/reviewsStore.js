import { defineStore } from 'pinia'
import { fetchReviewsFromProduct, addReview } from '../services/api.js';
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
                console.log(this.reviews);
            } else {
                this.addMessageAction('error', response.message);
                return response
            }
        },
        async addReviewAction(data, productId) {
            const response = await addReview(data, productId);

            if(response.success) {
                this.addMessageAction('success', response.message);
            } else {
                this.addReviewAction('error', response.message);
                return response;
            }

            this.getReviewsFromProduct(productId);
        },
        async addMessageAction(type, message) {
            const messageStore = useMessageStore();
            messageStore.addMessage({ type, message });
        }
    }
})