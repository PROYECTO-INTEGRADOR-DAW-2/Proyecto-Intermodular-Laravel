<script setup>
    import { ref } from 'vue';
    import { useAuthStore } from '../stores/authStore';

    const authStore = useAuthStore();

    const userData = ref({
        userName: "",
        password: ""
    });


</script>

<template>
    <div class="form-container">
        <div class="login-container">
            <h1 style="justify-self: center;">Inicia sesion</h1>
            <form @submit.prevent="authStore.loginAction(userData)">
                <div class="form-group">
                    <input type="text" name="nombre_usuario" id="nombre_usuario" placeholder="Usuario" v-model="userData.userName">
                </div>

                <div class="form-group">
                    <input type="password" name="contraseña" id="contraseña" placeholder="Contraseña" v-model="userData.password">
                </div>

                <div class="form-group">
                    <button type="submit" class="button">Iniciar sesion</button>
                </div>
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