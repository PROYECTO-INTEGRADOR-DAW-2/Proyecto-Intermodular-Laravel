import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import { useAuthStore } from '../stores/authStore';



const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/',
            name: 'home',
            component: HomeView
        },
        {
            path: '/login',
            name: 'login',
            component: () => import('../views/LoginView.vue'),
            meta: { isGuest: true }
        },
        {
            path: '/profile',
            name: 'profile',
            component: () => import('../views/ProfileView.vue'),
            meta: { requiresAuth: true }
        },
        {
            path: '/register',
            name: 'register',
            component: () => import('../views/RegisterView.vue'),
            meta: { isGuest: true }
        },
        {
            path: '/about',
            name: 'about',
            component: () => import('../views/AboutView.vue')
        },
        {
            path: '/products',
            name: 'products',
            component: () => import('../views/ProductsView.vue')
        },
        {
            path: '/products/:id',
            name: 'product-details',
            component: () => import('../views/ProductView.vue')
        },
        {
            path: '/products/import',
            name: 'product-import',
            component: () => import('../views/ProductImportView.vue'),
            meta: { requiresAuth: true }
        },
        {
            path: '/cart',
            name: 'cart',
            component: () => import('../views/CartView.vue'),
        },
        {
            path: '/checkout',
            name: 'checkout',
            component: () => import('../views/CheckoutView.vue'),
        },
        {
            path: '/admin',
            name: 'admin',
            component: () => import('../views/AdminView.vue'),
            meta: {
                requiresAuth: true,
                requiresAdmin: true
            }
        }
    ]
})

router.beforeEach(async (to, from) => {

    const authStore = useAuthStore();
    const token = localStorage.getItem('token');

    // Si hay token pero no tenemos datos del usuario en el store, los recuperamos una sola vez
    if (token && !authStore.user) {
        await authStore.fetchUserAction();
    }

    const isAuthenticated = authStore.isAuthenticated;
    const userRole = authStore.role;

    // 1. Si la ruta pide auth y NO está logueado -> Al Login
    if (to.meta.requiresAuth && !isAuthenticated) {
        return { name: 'login' };
    } 

    // 2. Si la ruta requiere admin y el rol NO es admin -> Al home
    if (to.meta.requiresAdmin && userRole !== 'admin') {
        return { name: 'home' };
    }

    // 3. Si es para invitados (login/register) y YA está logueado -> Al Home
    if (to.meta.isGuest && isAuthenticated) {
        return { name: 'home' };
    } 

    // 4. Si todo está ok, que pase
    return true;

})

export { router }
export default router
