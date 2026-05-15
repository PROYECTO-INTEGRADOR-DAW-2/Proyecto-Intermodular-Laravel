<script setup>
    import { useAuthStore } from '../stores/authStore';
    import { Form, Field, ErrorMessage } from 'vee-validate';
    import * as yup from 'yup';
    import { onMounted, ref, watch } from 'vue';
    import { useRoute } from 'vue-router';

    const authStore = useAuthStore();
    const route = useRoute();
    
    const reviews = ref([]);

    const initialData = {
        nombre: authStore.user?.nombre || '',
        apellidos: authStore.user?.apellidos || '',
        nombre_usuario: authStore.user?.nombre_usuario || '',
        email: authStore.user?.email || '',
    };

    const schemaProfile = yup.object({
        nombre: yup.string().required('El nombre es obligatorio'),
        apellidos: yup.string().max(255,'Maximo 255 caracteres'),
        nombre_usuario: yup.string().required('El nombre de usuario es obligatorio'),
        email: yup.string().email('Email no válido').required('El email es obligatorio'),
    });

    const schemaPassword = yup.object({
        contraseña_actual: yup.string().required('La contraseña antigua es obligatoria').min(8, 'Debe tener al menos 8 caracteres'),
        contraseña_nueva: yup.string().required('La contraseña nueva es obligatoria')
        .matches(/[A-Z]/, 'Debe tener al menos una letra mayuscula')
        .matches(/[a-z]/, 'Debe contener al menos una letra minúscula')
        .matches(/[!@#$%^&*(),.?":{}|<>]/, 'Debe contener al menos un símbolo (!@#$%^&...)')
    });

    const onSubmitProfile = async (values, actions) => {
        const response = await authStore.updateProfileAction(values);
        
        if (!response.success && response.info) {
            Object.entries(response.info).forEach(([field, messages]) => {
                actions.setFieldError(field, messages[0]);
            });
        }
    };

    const onSubmitPassword = async (values, { setFieldError }) => {
        const response = await authStore.updatePasswordAction(values);

        if (!response.success && response.info) {
            Object.entries(response.info).forEach(([field, messages]) => {
                setFieldError(field, messages[0]);
            });
        }

    }

    let currentTab = "#datos-personales";

    watch(() => route.hash, async (newHash) => {

        currentTab = newHash;

        switch (newHash) {
            case "#reviews":
                const response = await authStore.getReviewsAction();

                if (response.success) {
                    reviews.value = response.data;
                }         

                break;
        
            case "#pedidos":
                break;
        }

    })

  


</script>

<template>

    <div class="tabs">
        <router-link :to="{ hash: '#datos-personales' }" class="tab" :class="{ 'tab-active': currentTab === '#datos-personales' }">Datos personales</router-link>
        <router-link :to="{ hash: '#contraseña' }" class="tab" :class="{ 'tab-active': currentTab === '#contraseña' }">Contraseña</router-link>
        <router-link :to="{ hash: '#reviews' }" class="tab" :class="{ 'tab-active': currentTab === '#reviews' }">Mis reseñas</router-link>
    </div>

    <div class="main-container">
        <Form v-if="authStore.user && currentTab === '#datos-personales'" :validation-schema="schemaProfile" :initial-values="initialData" @submit="onSubmitProfile" class="profile-form">
            <div class="form-group">
                <label>Nombre:</label>
                <Field name="nombre" type="text" placeholder="Tu nombre"/>
                <ErrorMessage name="nombre" class="error-msg" />
            </div>

            <div class="form-group">
                <label>Apellidos:</label>
                <Field name="apellidos" type="text" placeholder="Tus apellidos"/>
                <ErrorMessage name="apellidos" class="error-msg" />
            </div>

            <div class="form-group">
                <label>Usuario:</label>
                <Field name="nombre_usuario" type="text" placeholder="Tu nombre de usuario"/>
                <ErrorMessage name="nombre_usuario" class="error-msg" />
            </div>

            <div class="form-group">
                <label>Email:</label>
                <Field name="email" type="email" placeholder="tu@email.com"/>
                <ErrorMessage name="email" class="error-msg" />
            </div>


            <button type="submit" class="button">Actualizar Perfil</button>
        </Form>

        <Form v-if="authStore.user && currentTab === '#contraseña'" :validation-schema="schemaPassword" @submit="onSubmitPassword" class="profile-form">
            <div class="form-group">
                <label>Contraseña actual:</label>
                <Field name="contraseña_actual" type="password"/>
                <ErrorMessage name="contraseña_actual" class="error-msg" />
            </div>

            <div class="form-group">
                <label>Contraseña nueva:</label>
                <Field name="contraseña_nueva" type="password"/>
                <ErrorMessage name="contraseña_nueva" class="error-msg" />
            </div>
            
            <router-link to="/reset-password" class="tab">No recuerdas tu contraseña?</router-link>
            <button type="submit" class="button">Actualizar Contraseña</button>
        </Form>

        <div v-if="authStore.user && currentTab === '#reviews'" class="reviews-container">
            <div v-if="reviews" v-for="(review, index) in reviews" :key="index" class="review">
                <h3>review.product.nombre</h3>
                <div class="stars-container">
                    <i v-for="star in 5" :class="['bi', star <= review.valoracion ? 'bi-star-fill' : 'bi-star']"></i>
                </div>
                <p class="review-coment">review.comentario</p>
                <router-link :to="{ name: 'product-details', params: { id: review.product_id } }">Ver producto</router-link>
            </div>

            <div v-else>
                No has añadido ninguna reseña
            </div>
        </div>
    </div>
    
</template>

<style scoped>

    .tabs {
        margin-top: 50px;
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        grid-template-rows: auto;
        justify-content: center;
        justify-self: center;
        width: 80%;
        height: 4em;
        border-bottom: 2px solid black;
    }

    .tab {
        height: 100%;
        width: 100%;
        
        text-decoration: none;
        color: black;

        align-content: center;
        text-align: center;

        transition: all 0.5s ease;
    }


    .tab-active {
        background-color: #D72631;
        color: white;
    }





    .main-container {
        display: grid;
        height: 100vh;
        padding: 50px;
    }

    .profile-form {
        height: 500px;
        width: 100%;
        display: grid;
    }

    .profile-form label {
        font-size: 30px;
    }



    .form-group input[type="text"], input[type="password"], input[type="email"] {
        height: 50px;
    }
    

    .form-group {
        display: grid;
        margin: 20px 0 20px 0; 
    }

    .reviews-container {
        display: grid;
        gap: 20px;
        width: 100%;
    }

   

    


</style>

