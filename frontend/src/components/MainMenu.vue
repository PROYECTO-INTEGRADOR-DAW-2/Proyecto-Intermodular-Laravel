<script setup>
    import { RouterLink } from 'vue-router';
    import { useAuthStore } from '../stores/authStore.js';
    import { useCartStore } from '../stores/cartStore.js';

    const authStore = useAuthStore();
    const cartStore = useCartStore();


</script>

<template>
    <nav id="nav-section" class="navbar navbar-dark navbar-expand-lg fixed-top" style="background-color: #1F1F1F;">
        <div class="container-fluid">

            <!-- Mobile Logo and Toggler -->
            <div class="d-flex align-items-center justify-content-between w-100 d-lg-none">
                
                <router-link to="/">
                    <img src="/img/logo.png" alt="Logotipo de J&A Sports" class="logoPagina img-fluid"
                        style="height: 50px;">
                </router-link>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
                    aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation"
                    style="border: 1px solid #D72631;">
                    <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
                </button>

            </div>


            <div class="collapse navbar-collapse" id="navbarContent">
                <div class="row w-100 align-items-center g-0">

                    <!-- Desktop Logo -->
                    <div class="col-lg-1 d-none d-lg-block text-center">
                        <router-link to="/">
                            <img src="/img/logo.png" alt="Logotipo de J&A Sports" class="logoPagina img-fluid">
                        </router-link>
                    </div>

                    <!-- Navigation Links -->
                    <div class="col-lg-4 col-md-12 mt-3 mt-lg-0">
                            <ul id="main-nav" class="col d-flex flex-column flex-lg-row justify-content-around list-unstyled m-0 gx-0 gap-2 gap-lg-0">
                                <li><router-link to="/products?sexo=hombre" class="text-white text-decoration-none">Hombre</router-link></li>
                                <li><router-link to="/products?sexo=mujer" class="text-white text-decoration-none">Mujer</router-link></li>
                                <li><router-link to="/products?sexo=nino,nina" class="text-white text-decoration-none">Niños</router-link></li>
                                <li><router-link to="/products" class="text-white text-decoration-none">Productos</router-link></li>
                                
                                    <li v-if="authStore.isAuthenticated">
                                        <router-link to="/dashboard">Dashboard</router-link>
                                    </li>
                                
                            </ul>
                    </div>

                    <!-- Search Bar -->
                    <div class="col-lg-4 col-md-12 my-3 my-lg-0 px-lg-3">
                        <input type="text" placeholder="Buscar" aria-label="Buscar productos" class="form-control w-100 bg-dark text-white border-secondary">
                    </div>

                    <!-- Icons -->
                    <div class="col-lg-2 col-md-12 d-flex justify-content-center justify-content-lg-end">
                        <ul id="icon-nav" class="d-flex list-unstyled m-0 gap-3 align-items-center">
                            
                                <!-- User Dropdown -->
                                <li v-if="authStore.isAuthenticated" class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle text-white d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <img src="/img/user.png" alt="Icono de usuario" style="pointer-events: none;">
                                        <span class="ms-2 d-none d-lg-inline-block">{{ authStore.user.nombre_usuario }}</span>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark" aria-labelledby="userDropdown">
                                        <li><router-link to="/profile" class="dropdown-item">Perfil</router-link></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="POST" @submit.prevent="logout()">
                                                <button type="submit" class="dropdown-item">
                                                    Cerrar sesion
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </li>
                            
                                <li v-else>
                                    <router-link to="/login">
                                        <img src="/img/user.png" alt="Icono de usuario">
                                    </router-link>
                                </li>
                            <li>
                                <router-link to="/cart" class="cartIcon">
                                    <img  src="/img/carrito.png" alt="Icono del carrito">
                                    <div v-if="cartStore.countItems" class="cart-items-number">{{ cartStore.countItems }}</div>
                                </router-link>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

</template>

<style scoped>
    .cart-items-number {
        text-align: center;
        color: white;
        width: 25px;
        height: 25px;
        border-radius: 100%;
        background-color: red;
        position: absolute;
        top: 0;
        right: 0;
    }

    .cartIcon {
        position: relative;
    }

    

</style>

