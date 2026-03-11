<script setup>
    import { ref } from 'vue';
    import { useAuthStore } from '../stores/authStore';

    const authStore = useAuthStore();

    const userData = ref({
        nombre: "",
        apellidos: "",
        nombre_usuario: "",
        email: "",
        contraseña: "",
        confirm_contraseña: ""
    });


</script>

<template>
    <div class="form-container">
        <div class="register-container">
            <h1 style="justify-self: center;">Registrate</h1>
            <form @submit.prevent="authStore.registerAction(userData)">
                <div class="form-group">
                    <input type="text" name="nombre" id="nombre" placeholder="Nombre" v-model="userData.nombre">
                </div>

                <div class="form-group">
                    <input type="text" name="apellidos" id="apellidos" placeholder="Apellidos" v-model="userData.apellidos">
                </div>

                <div class="form-group">
                    <input type="text" name="nombre_usuario" id="nombre_usuario" placeholder="Usuario" v-model="userData.nombre_usuario">
                </div>

                <div class="form-group">
                    <input type="email" name="email" id="email" placeholder="Email" v-model="userData.email">
                </div>

                <div class="form-group">
                    <input type="password" name="contraseña" id="contraseña" placeholder="Contraseña" v-model="userData.contraseña">
                </div>

                <div class="form-group">
                    <input type="password" name="confirm_contraseña" id="confirm_contraseña" placeholder="Confirma contraseña" v-model="userData.confirm_contraseña">
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