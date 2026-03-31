<script setup>
    import { computed, onMounted, defineProps, ref } from 'vue';
    import { useReviewsStore } from '../stores/reviewsStore.js'
    import { useAuthStore } from '../stores/authStore';
    import { Form, Field, ErrorMessage } from 'vee-validate'
    import * as yup from 'yup';

    const reviewsStore = useReviewsStore();
    const authStore = useAuthStore();

    const reviews = computed(() => {reviewsStore.reviews});

    const props = defineProps({
        productId: Number,
    })

    const newReview = ref(null);

    

    const schemaReview = yup.object({
        comentario: yup.string().max(255,'Maximo 255 caracteres'),
        valoracion: yup.number().required('La valoracion es obligatoria'),
    });

    const onSubmitReview = (values) => {
        reviewsStore.addReview(values);
    }

    onMounted(() => {
        if (props?.productId) reviewsStore.getReviewsFromProduct(props.productId)
    })


    

</script>


<template>
    <div class="main-container">
        <div class="reviews-container">
            <div v-for="(review, index) in reviews" class="review">
                <strong>{{ review.user }}</strong>
                <p>{{ review.comentario }}</p>
            </div>
        </div>

        <div v-if="authStore.isAuthenticated" class="add-review-container">
            <h3>¡Tu opinion importa mucho!</h3>
            <Form :validation-schema="schemaReview" @submit="onSubmitReview" class="add-review-form">
                <div class="form-group">
                    <label>Valoracion:</label>
                    <Field name="valoracion" type="number" placeholder="Tu nombre"/>
                    <ErrorMessage name="nombre" class="error-msg" />
                </div>

                <div class="form-group">
                    <label>Comentario:</label>
                    <Field name="comentario" type="textarea" placeholder="Tu comentario"/>
                    <ErrorMessage name="nombre" class="error-msg" />
                </div>
            </Form>
        </div>
    </div>
    

</template>


<style scoped>
    .main-container {
        height: 50em;
        display: grid;
        column-gap: 20px;
        grid-template-areas: "reviews add-review";
        grid-template-columns: auto;
    }

    .reviews-container {
        grid-area: reviews;
        width: 100%;
    }

    .review {
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);

    }

    .add-review-container {
        width: 100%;
        grid-area: add-review;
        display: grid;
        grid-template-columns: 1fr;
    }

    .form-group {
        display: grid;
        margin: 20px 0 20px 0; 
    }

    .form-group input[type="number"] {
        height: 50px;
    }



</style>