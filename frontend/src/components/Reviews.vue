<script setup>
    import { computed, onMounted } from 'vue';
    import { useReviewsStore } from '../stores/reviewsStore.js'
    import { useAuthStore } from '../stores/authStore';
    import { Form, Field, ErrorMessage } from 'vee-validate'
    import * as yup from 'yup';

    const reviewsStore = useReviewsStore();
    const authStore = useAuthStore();

    const reviews = computed(() => {reviewsStore.reviews});
    

    const schemaReview = yup.object({
        comentario: yup.string().max(255,'Maximo 255 caracteres'),
        valoracion: yup.string().required('La es obligatorio'),
    });

    const onSubmitReview = (values) => {
        
    }

    onMounted(() => {

        const response = reviewsStore.getReviewsFromProduct()
    })


    

</script>


<template>
    <div class="reviews-container">
        
    </div>

    <div v-if="authStore.isAuthenticated">
        <Form :validation-schema="schemaReview" @submit="onSubmitReview">
            <div class="form-group">
                <label>Valoracion:</label>
                <Field name="valoracion" type="text" placeholder="Tu nombre"/>
                <ErrorMessage name="nombre" class="error-msg" />
            </div>

            <div class="form-group">
                <label>Comentario:</label>
                <Field name="comentario" type="text" placeholder="Tu comentario"/>
                <ErrorMessage name="nombre" class="error-msg" />
            </div>
        </Form>
    </div>

</template>


<style scoped>

</style>