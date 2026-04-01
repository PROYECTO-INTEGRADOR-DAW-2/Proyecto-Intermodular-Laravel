<script setup>
    import { ref } from 'vue';
    import { useAuthStore } from '../stores/authStore';
    import { Field, ErrorMessage, Form} from 'vee-validate';
    import * as yup from 'yup';

    const authStore = useAuthStore();

    const schemaRegister = yup.object({
        nombre: yup.string().required("Nombre obligatorio"),
        apellidos: yup.string().required("Apellidos obligatorios"),
        nombre_usuario: yup.string().required("Nombre de usuario obligatorio"),
        email: yup.string().email("Debes introducir un email valido").required("Email obligatorio"),
        contraseña: yup.string().required("Contraseña obligatoria")
        .matches(/[A-Z]/, 'Debe tener al menos una letra mayuscula')
        .matches(/[a-z]/, 'Debe contener al menos una letra minúscula')
        .matches(/[!@#$%^&*(),.?":{}|<>]/, 'Debe contener al menos un símbolo (!@#$%^&...)'),
        confirm_contraseña: yup.string().required("Confirmacion obligatoria").oneOf([yup.ref('contraseña')], 'Las contraseñas no coinciden')
    })

    

    const onSumbitRegister = async (values, actions) =>  {
        const response = authStore.registerAction(values);

        if (!response.success && response.info) {
            Object.entries(response.info).forEach(([field, messages]) => {
                actions.setFieldError(field, messages[0]);
            }) 
        }
    }


</script>

<template>
    <div class="form-container">
        <div class="register-container">
            <h1 style="justify-self: center;">Registrate</h1>
            <Form :validation-schema="schemaRegister" @submit="onSumbitRegister">
                <div class="form-group">
                    <Field type="text" name="nombre" id="nombre" placeholder="Nombre" ></Field>
                    <ErrorMessage name="nombre" class="error-msg"></ErrorMessage>
                </div>

                <div class="form-group">
                    <Field type="text" name="apellidos" id="apellidos" placeholder="Apellidos" ></Field>
                    <ErrorMessage name="apellidos" class="error-msg"></ErrorMessage>
                </div>

                <div class="form-group">
                    <Field type="text" name="nombre_usuario" id="nombre_usuario" placeholder="Usuario" ></Field>
                    <ErrorMessage name="nombre_usuario" class="error-msg"></ErrorMessage>
                </div>

                <div class="form-group">
                    <Field type="email" name="email" id="email" placeholder="Email" ></Field>
                    <ErrorMessage name="email" class="error-msg"></ErrorMessage>
                </div>

                <div class="form-group">
                    <Field type="password" name="contraseña" id="contraseña" placeholder="Contraseña" ></Field>
                    <ErrorMessage name="contraseña" class="error-msg"></ErrorMessage>
                </div>

                <div class="form-group">
                    <Field type="password" name="confirm_contraseña" id="confirm_contraseña" placeholder="Confirma contraseña" ></Field>
                    <ErrorMessage name="confirm_contraseña" class="error-msg"></ErrorMessage>
                </div>

                <div class="form-group">
                    <button type="submit" class="button">Registrarse</button>
                </div>

                <RouterLink to="/login">¿Tienes una cuenta?</RouterLink>
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

    .register-container {
        height: auto;
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

    .form-group input[type="text"], input[type="password"], input[type="email"] {
        height: 50px;
    }
    

    .form-group {
        display: grid;
        margin: 20px 0 20px 0; 
    }

</style>