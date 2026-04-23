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

    if (authStore.debug) {
        console.log(`[Router] Navigating to: ${to.path}`, {
            requiresAuth: to.meta.requiresAuth,
            requiresAdmin: to.meta.requiresAdmin,
            hasToken: !!token,
            storedUser: !!authStore.user
        });
    }

    // Si hay token pero no tenemos datos del usuario en el store, intentamos recuperarlos
    if (token && !authStore.user) {
        if (authStore.debug) console.log("[Router] Token detected without user data. Fetching...");
        await authStore.fetchUserAction();
    }

    const isAuthenticated = authStore.isAuthenticated;
    const userRole = (authStore.role || "").toLowerCase();

    if (authStore.debug) {
        console.log(`[Router] Auth state:`, {
            isAuthenticated,
            userRole
        });
    }

    // Redirección si requiere auth y no está autenticado
    if (to.meta.requiresAuth && !isAuthenticated) {
        if (authStore.debug) console.warn("[Router] Access denied: Requires Auth. Redirecting to Login.");
        return { name: 'login' };
    }

    // Redirección si requiere admin y no es admin
    if (to.meta.requiresAdmin && userRole !== 'admin') {
        if (authStore.debug) console.warn(`[Router] Access denied: Admin required. Current role: ${userRole}. Redirecting home.`);
        return { name: 'home' };
    }

    // Redirección si es invitado y ya está logueado
    if (to.meta.isGuest && isAuthenticated) {
        if (authStore.debug) console.log("[Router] Guest route accessed by authenticated user. Redirecting home.");
        return { name: 'home' };
    }

    if (authStore.debug) console.log("[Router] Navigation allowed.");
    return true;

})

export { router }
export default router
