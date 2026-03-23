<script setup>
    import { useAuthStore } from '../stores/authStore';
    import { Form, Field, ErrorMessage } from 'vee-validate';
    import * as yup from 'yup';
    import { onMounted } from 'vue';

    const authStore = useAuthStore();
    
    // 1. Usamos una constante reactiva o directamente el store
    // Importante: El nombre de las llaves debe coincidir con el 'name' de los <Field>
    const initialData = {
        nombre: authStore.user?.nombre || '',
        nombre_usuario: authStore.user?.nombre_usuario || '',
        email: authStore.user?.email || '',
    };

    const schemaProfile = yup.object({
        nombre: yup.string().required('El nombre es obligatorio'),
        nombre_usuario: yup.string().required('El nombre de usuario es obligatorio'),
        email: yup.string().email('Email no válido').required('El email es obligatorio'),
    });

    const onSubmit = (values) => {
        console.log('Datos finales (mezclados con los iniciales):', values);

        authStore.updateProfileAction(values);
        
    };

    onMounted(() => {
        console.log(initialData)
    })
</script>

<template>
    <div class="form-container">
        <Form v-if="authStore.user" :validation-schema="schemaProfile" :initial-values="initialData" @submit="onSubmit" class="profile-form">
            <div class="form-group">
                <label>Nombre:</label>
                <Field name="nombre" type="text" placeholder="Tu nombre"/>
                <ErrorMessage name="nombre" class="error-msg" />
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


            <button type="submit">Actualizar Perfil</button>
        </Form>

    </div>
    
</template>

<style scoped>

    .form-container {
        display: grid;
        height: 100vh;
    }

    .profile-form {
        height: 500px;
        width: 100%;
        display: grid;
        
        padding: 50px;
        border-radius: 20px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
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

   

    


</style>

