<script setup>
    import { ref } from 'vue';
    import { useAuthStore } from '../stores/authStore';
    import { Form, ErrorMessage, Field } from 'vee-validate';
    import * as yup from 'yup';

    const authStore = useAuthStore();

    const schemaLogin = yup.object({
        nombre_usuario: yup.string().required("Nombre de usuario obligatorio"),
        contraseña: yup.string().required("Contraseña obligatoria"),
    })

    const onSumbitLogin = async (values, actions) => {
        const response = await authStore.loginAction(values);

        if (!response.success && response.info) {
            Object.entries(response.info).forEach(([field, messages]) => {
                actions.setFieldError(field, messages[0]);
            })
        }
    } 


</script>

<template>
    <div class="form-container">
        <div class="login-container">
            <h1 style="justify-self: center;">Inicia sesion</h1>
            <Form :validation-schema="schemaLogin" @submit="onSumbitLogin">
                <div class="form-group">
                    <Field type="text" name="nombre_usuario" id="nombre_usuario" placeholder="Usuario"></Field>
                    <ErrorMessage name="nombre_usuario" class="error-msg"></ErrorMessage>
                </div>

                <div class="form-group">
                    <Field type="password" name="contraseña" id="contraseña" placeholder="Contraseña"></Field>
                    <ErrorMessage name="contraseña" class="error-msg"></ErrorMessage>
                </div>

                <div class="form-group">
                    <button type="submit" class="button">Iniciar sesion</button>
                </div>
                <RouterLink to="/register">¿No tienes una cuenta?</RouterLink>
            </form>
        </div>
    </div>
</template>

<style scoped>
    .form-container {
        display: grid;
        justify-items: center;
        align-items: center;
        height: 100vh;
    }

    .login-container {
        height: 500px;
        width: 500px;
        display: grid;
        grid-template-rows: 0.5fr 2fr;
        padding: 50px;
        border-radius: 20px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    }

    .login-container label {
        font-size: 30px;
    }

    .login-container form {
        width: 100%;
    }


    .form-group input[type="text"], input[type="password"] {
        height: 50px;
    }
    

    .form-group {
        display: grid;
        margin: 20px 0 20px 0; 
    }

</style>