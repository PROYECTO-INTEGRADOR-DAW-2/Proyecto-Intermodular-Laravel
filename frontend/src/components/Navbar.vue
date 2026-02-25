<script setup>
import { computed, ref } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useCartStore } from '@/stores/cart';
import { useRouter } from 'vue-router';
import { useRole } from '@/composables/useRole';
import RoleBadge from '@/components/RoleBadge.vue';

const authStore = useAuthStore();
const cartStore = useCartStore();
const router = useRouter();
const { hasRole } = useRole();

const searchQuery = ref('');

const handleLogout = async () => {
    await authStore.logout();
    router.push({ name: 'login' });
};

const handleSearch = () => {
    if (searchQuery.value.trim()) {
        router.push({ name: 'products', query: { search: searchQuery.value } });
    }
};
</script>

<template>
  <nav class="navbar navbar-expand-lg user-select-none sticky-top navbar-dark bg-dark p-3">
    <div class="container-fluid">
        <!-- Brand/Logo -->
        <router-link :to="{ name: 'home' }" class="navbar-brand">
            <img src="/img/logo.png" alt="Logotipo de J&A Sports" class="logoPagina img-fluid" style="height: 100px;">
        </router-link>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Content -->
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Centered Links -->
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 fw-bold fs-5 gap-4">
                <li class="nav-item">
                    <router-link :to="{ name: 'home' }" class="nav-link" active-class="active">Inicio</router-link>
                </li>
                <li class="nav-item">
                    <router-link :to="{ name: 'products', query: { sexo: 'Hombre' } }" class="nav-link">Hombre</router-link>
                </li>
                <li class="nav-item">
                    <router-link :to="{ name: 'products', query: { sexo: 'Mujer' } }" class="nav-link">Mujer</router-link>
                </li>
                <li class="nav-item">
                    <router-link :to="{ name: 'products' }" class="nav-link" active-class="active">Productos</router-link>
                </li>
                <li class="nav-item">
                    <router-link :to="{ name: 'contact' }" class="nav-link" active-class="active">Contacto</router-link>
                </li>
            </ul>

            <!-- Right Actions (Search, Auth, Cart) -->
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-3 mt-3 mt-lg-0">
                <!-- Search Form -->
                <form @submit.prevent="handleSearch" class="d-flex w-100" role="search" style="max-width: 300px;">
                    <div class="input-group">
                        <input v-model="searchQuery" class="form-control" type="search" placeholder="Buscar..." aria-label="Search">
                        <button class="btn btn-danger" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>

                <div class="d-flex align-items-center justify-content-between w-100 w-lg-auto gap-3">
                    <!-- Auth Dropdown -->
                    <div v-if="authStore.isAuthenticated" class="dropdown">
                        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="/img/user.png" alt="Usuario" width="32" height="32" class="rounded-circle me-2">
                            <span>{{ authStore.user?.name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg-end text-small shadow" aria-labelledby="dropdownUser1">
                            <li class="px-3 py-1">
                                <span v-for="role in authStore.user?.roles" :key="role" class="me-1">
                                    <RoleBadge :role="role" />
                                </span>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li><router-link :to="{ name: 'profile' }" class="dropdown-item">Mi Perfil</router-link></li>
                            <li><router-link :to="{ name: 'orders' }" class="dropdown-item">Mis Pedidos</router-link></li>
                            <li v-if="hasRole('admin')"><router-link :to="{ name: 'admin' }" class="dropdown-item text-danger fw-bold"><i class="bi bi-shield-lock me-1"></i>Panel Admin</router-link></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><button class="dropdown-item text-danger" @click="handleLogout">Cerrar Sesión</button></li>
                        </ul>
                    </div>
                    <div v-else>
                        <router-link :to="{ name: 'login' }" class="btn btn-outline-light d-flex align-items-center gap-2">
                            <i class="bi bi-person-fill"></i> Acceder
                        </router-link>
                    </div>

                    <!-- Cart Icon -->
                    <router-link :to="{ name: 'cart' }" class="position-relative text-white ms-auto ms-lg-2">
                        <img src="/img/carrito.png" alt="Carrito" width="32">
                        <span v-if="cartStore.count > 0" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ cartStore.count }}
                            <span class="visually-hidden">items in cart</span>
                        </span>
                    </router-link>
                </div>
            </div>
        </div>
    </div>
  </nav>
</template>

<style scoped>
.logoPagina {
    max-height: 60px;
}
.nav-link {
    color: white !important;
    transition: color 0.3s;
}
.nav-link:hover {
    color: #dc3545 !important; /* Bootstrap Danger Red */
}
.nav-link.active {
    color: #dc3545 !important;
}
</style>
