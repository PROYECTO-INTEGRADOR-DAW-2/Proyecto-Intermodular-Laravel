<script setup>
    import { computed, onMounted, defineProps, ref } from 'vue';
    import { useReviewsStore } from '../stores/reviewsStore.js'
    import { useAuthStore } from '../stores/authStore';
    import { Form, Field, ErrorMessage } from 'vee-validate'
    import * as yup from 'yup';

    const reviewsStore = useReviewsStore();
    const authStore = useAuthStore();

    const reviews = computed(() => reviewsStore.reviews);

    const props = defineProps({
        productId: Number,
    })

    const newReview = ref(null);
    let selectedReview = ref(null);


    const schemaReview = yup.object({
        comentario: yup.string().max(255,'Maximo 255 caracteres'),
        valoracion: yup.number().required('La valoracion es obligatoria'),
    });

    const onSubmitAddReview = async (values) => {
        await reviewsStore.addReviewAction(values, props.productId);
    }

    const onSubmitUpdateReview = async (values) => {
        console.log(values)
        await reviewsStore.updateReviewAction(values, values.product_id, values.id);
    }

    const onDeleteReview = async (values) => {
        console.log(values)
        if(confirm("Esta seguro de eliminar la valoracion")) await reviewsStore.deleteReviewAction(values.product_id, values.id);
    }


    const isReviewFromUser = (review) => {

        if (!authStore.isAuthenticated) return

        return review.user_id == authStore.user.id;
        
    }

    onMounted(async () => {
        if (props?.productId) await reviewsStore.getReviewsFromProduct(props.productId);
    })


    

</script>


<template>
    <div class="main-container">
        
        <div class="reviews-container">
            <h1 style="margin-left: 10px;">Valoraciones</h1>
            <div class="reviews">
                <div v-for="(review, index) in reviews" class="review">
                    <strong>{{ review.user }}</strong>

                    <button v-if="selectedReview?.id == review.id" style="position:absolute; left: 83%;" type="button" class="btn btn-danger">
                            <i class="bi bi-arrow-left" @click="selectedReview = null"> Cancelar</i>
                    </button>

                    <div v-if="selectedReview?.id !== review.id" class="review-content">
                        <div class="stars-container">
                            <i v-for="star in 5" :class="[ 'bi', star <= review.valoracion ? 'bi-star-fill' : 'bi-star' ]"></i>
                        </div>
                        
                        <p>{{ review.comentario }}</p> 
                    </div>

                    <div v-if="isReviewFromUser(review) && !selectedReview" class="buttons-container">
                        <button type="button" class="btn edit-button" @click="selectedReview = review">
                            <i class="bi bi-pencil"></i>
                        </button>
                        
                        <button type="button" class="btn btn-danger" @click="onDeleteReview(review)">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>

                    <div v-if="selectedReview && selectedReview.id == review.id" class="update-form-container">
                        
                        <Form v-slot="{ values, errors, setFieldValue }" :initial-values="selectedReview" :validation-schema="schemaReview" @submit="onSubmitUpdateReview" class="add-review-form">
                            <div class="form-group">
                                <label>Valoracion:</label>
                                <div class="stars-container">
                                    <i v-for="v in 5" @click="setFieldValue('valoracion', v)" style="cursor: pointer;" :class="[ 'bi', v <= values.valoracion ? 'bi-star-fill' : 'bi-star' ]" ></i>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Comentario:</label>
                                <Field name="comentario" type="text" placeholder="Tu comentario"/>
                                <ErrorMessage name="comentario" class="error-msg" />
                            </div>

                            <div class="form-group">
                                <input type="submit" value="Actualizar valoracion" class="button">
                            </div>
                        </Form>
                    </div>
                </div>
            </div>   
        </div>

        <div class="add-review-container">
            <h1 style=" margin-bottom: 20px;">¡Tu valoracion importa mucho!</h1>
            <Form v-if="authStore.isAuthenticated" v-slot="{ values, errors, setFieldValue }" :initial-values="{valoracion: 0, comentario: ''}"  :validation-schema="schemaReview" @submit="onSubmitAddReview" class="add-review-form">
                <div class="form-group">
                    <label>Valoracion:</label>
                    <div class="stars-container">
                        <i v-for="v in 5" @click="setFieldValue('valoracion', v)" style="cursor: pointer;" :class="[ 'bi', v <= values.valoracion ? 'bi-star-fill' : 'bi-star' ]" ></i>
                    </div>
                </div>

                <div class="form-group">
                    <label>Comentario:</label>
                    <Field name="comentario" type="text" placeholder="Tu comentario"/>
                    <ErrorMessage name="comentario" class="error-msg" />
                </div>

                <div class="form-group">
                    <input type="submit" value="Añadir valoracion" class="button">
                </div>
            </Form>

            <div v-else class="login-indicator">
                <p>Debes <router-link to="/login">iniciar sesion</router-link> para poder añadir tu opinion</p>
            </div>
        </div>
    </div>
    

</template>


<style scoped>
    .main-container {
        height: 50em;
        display: grid;
        column-gap: 20px;
        grid-template-areas: "reviews add-review";
        grid-template-columns: 1fr 1fr;
        width: 95%;
        justify-self: center;
    }




    .reviews-container {
        grid-area: reviews;
        width: 100%;
    }

    .reviews {
        display: grid;
        grid-template-columns: 1fr;
        grid-template-rows: auto;
        gap: 20px;
        overflow-y: auto;
        position: relative;
        justify-items: center;
    }

    .review {
        display: grid;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        padding: 10px 20px;
        color: black;
        width: 90%;
    }

    .review-content * {
        margin: 5px 0;
    }

    .buttons-container {
        justify-self: end;
    }

    .add-review-container {
        width: 100%;
        grid-area: add-review;
    }




    .login-indicator {
        border-radius: 10px;
        background-color: rgba(230, 203, 53, 0.596);
        border: 3px solid rgb(221, 191, 23);
        padding: 25px;
    }

    .login-indicator p{
        width: auto;
        margin: 0px;
    }

    .form-group {
        display: grid;
        margin: 20px 0 20px 0; 
    }

    .form-group input[type="text"], input[type="number"] {
        height: 50px;
    }



</style>