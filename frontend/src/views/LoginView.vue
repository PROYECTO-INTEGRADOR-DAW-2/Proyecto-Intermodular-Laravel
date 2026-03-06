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
            <form @submit.prevent="login">
                <div class="input-group">
                    <label for="username">Usuario</label>
                    <input type="text" name="username" id="username" v-model="userData.userName">
                </div>

                <div class="input-group">
                    <label for="password">Contraseña</label>
                    <input type="password" name="password" id="password" v-model="userData.password">
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
        justify-items: center;
        align-items: center;
        padding: 20px;
        border-radius: 20px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    }
    

    .input-group {
        display: grid;
        margin: 5px;
    }

</style>