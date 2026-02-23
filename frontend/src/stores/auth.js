import { defineStore } from 'pinia'
import api from '../services/api'
import router from '../router'

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: JSON.parse(localStorage.getItem('user')) || null,
        token: localStorage.getItem('token') || null,
        isAuthenticated: !!localStorage.getItem('token')
    }),

    actions: {
        async register(credentials) {
            try {
                const response = await api.post('/register', credentials);
                const { token, name } = response.data.data;

                this.token = token;
                this.isAuthenticated = true;
                this.user = { name, roles: [] }; // Default empty roles for new users

                localStorage.setItem('token', token);
                localStorage.setItem('user', JSON.stringify(this.user));

                router.push('/');
            } catch (error) {
                console.error('Registration failed', error);
                throw error;
            }
        },

        async login(credentials) {
            try {
                const response = await api.post('/login', credentials)
                const { token, name, email, role, roles } = response.data.data

                this.token = token
                this.isAuthenticated = true
                // Construct user object with both direct role and roles array
                this.user = { name, email, role, roles }

                localStorage.setItem('token', token)
                localStorage.setItem('user', JSON.stringify(this.user))

                router.push('/')
            } catch (error) {
                console.error('Login failed', error)
                throw error
            }
        },

        async logout() {
            try {
                if (this.isAuthenticated) {
                    await api.post('/logout')
                }
            } catch (error) {
                console.error('Logout failed', error)
            } finally {
                this.token = null
                this.user = null
                this.isAuthenticated = false
                localStorage.removeItem('token')
                localStorage.removeItem('user')
                router.push('/login')
            }
        },

        async fetchUser() {
            if (!this.token) return
            try {
                const response = await api.get('/user')
                this.user = response.data
                localStorage.setItem('user', JSON.stringify(this.user))
            } catch (error) {
                // Token invalid - clear session
                this.token = null
                this.user = null
                this.isAuthenticated = false
                localStorage.removeItem('token')
                localStorage.removeItem('user')
            }
        },

        // Used by OAuth callback to store session data directly
        setSession({ token, name, email, role, roles }) {
            this.token = token
            this.isAuthenticated = true
            this.user = { name, email, role, roles }
            localStorage.setItem('token', token)
            localStorage.setItem('user', JSON.stringify(this.user))
        }
    }
})
