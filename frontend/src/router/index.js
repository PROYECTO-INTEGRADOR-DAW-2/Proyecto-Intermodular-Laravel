import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useRole } from '../composables/useRole'
import HomeView from '../views/HomeView.vue'
import LoginView from '../views/LoginView.vue'
import ForbiddenView from '../views/ForbiddenView.vue'

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/',
            name: 'home',
            component: () => import('../views/HomeView.vue'),
        },
        {
            path: '/products',
            name: 'products',
            component: () => import('../views/ProductListView.vue'),
        },
        {
            path: '/products/:id',
            name: 'product-detail',
            component: () => import('../views/ProductDetailView.vue'),
            props: true
        },
        {
            path: '/cart',
            name: 'cart',
            component: () => import('../views/CartView.vue'),
        },
        {
            path: '/login',
            name: 'login',
            component: LoginView,
            meta: { guest: true }
        },
        {
            path: '/register',
            name: 'register',
            component: () => import('../views/RegisterView.vue'),
            meta: { guest: true }
        },
        {
            path: '/profile',
            name: 'profile',
            component: () => import('../views/ProfileView.vue'),
            meta: { requiresAuth: true }
        },
        {
            path: '/orders',
            name: 'orders',
            component: () => import('../views/OrdersView.vue'),
            meta: { requiresAuth: true }
        },
        {
            path: '/checkout',
            name: 'checkout',
            component: () => import('../views/CheckoutView.vue'),
            meta: { requiresAuth: true }
        },
        {
            path: '/admin',
            name: 'admin',
            component: () => import('../views/AdminView.vue'),
            meta: { requiresAuth: true, roles: ['admin'] }
        },
        {
            path: '/forbidden',
            name: 'forbidden',
            component: ForbiddenView
        }
    ]
})

// Track whether we've done the initial user fetch
let userFetched = false

router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore()

    // On first navigation, refresh user data from API to get up-to-date role info
    if (!userFetched && authStore.token) {
        await authStore.fetchUser()
        userFetched = true
    }

    const { hasRole } = useRole()

    // Redirect to login if auth is required and user is not authenticated
    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        return next('/login')
    }

    // Redirect to home if guest only (like login page) and user is authenticated
    if (to.meta.guest && authStore.isAuthenticated) {
        return next('/')
    }

    // Check for role requirements
    if (to.meta.roles) {
        const hasRequiredRole = to.meta.roles.some(role => hasRole(role))
        if (!hasRequiredRole) {
            return next('/forbidden')
        }
    }

    next()
})

export default router
