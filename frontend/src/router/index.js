import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'



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
            path: '/products/import',
            name: 'product-import',
            component: () => import('../views/ProductImportView.vue'),
            meta: { requiresAuth: true }
        }
    ]
})

router.beforeEach((to, from) => {

    const token = localStorage.getItem('token');
    const isAuthenticated = !!token;

        // 1. Si la ruta pide auth y NO está logueado -> Al Login
    if (to.meta.requiresAuth && !isAuthenticated) {
        return { name: 'login' };
    } 

    // 2. Si es para invitados (login/register) y YA está logueado -> Al Home
    else if (to.meta.isGuest && isAuthenticated) {
        return { name: 'home' };
    } 

    // 3. Si todo está ok, que pase
    else {
        return true;
    }

})

export { router }
export default router
