import { defineStore } from 'pinia';
import { login, register, updateProfile, updatePassword, fetchUser, fetchUsers, updateUser, fetchRoles } from '../services/api.js'
import { useMessageStore } from '../stores/messageStore.js';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        role: "",
        isAuthenticated: false,
        bearerToken: localStorage.getItem('token') || null,
        debug: true
    }),
    actions: {
        async loginAction(data) {
            const response = await login(data);

            console.log(response.data)

            if (response.success) {
                this.addMessageAction("success", response.message);
                const { token, user } = response.data.data;

                this.user = user;
                this.role = user.rol;
                this.isAuthenticated = true;
                this.bearerToken = token;

                localStorage.setItem("token", token);

                return response;
            } else {
                this.addMessageAction("error", response.message);
                return false;
            }
        },

        async registerAction(data) {
            const response = await register(data);

            if (response.success) {
                this.addMessageAction("success", response.message);
                const { token, user } = response.data.data;

                this.user = user;
                this.role = user.rol;
                this.isAuthenticated = true;
                this.bearerToken = token;

                localStorage.setItem("token", token);

                return response;
            } else {
                this.addMensajeAction("error", response.message)
                return false;
            }
        },

        async updateProfileAction(data) {
            const response = await updateProfile(data);

            if (response.success) {
                this.addMessageAction("success", response.message);
                const { user } = response.data.data;
                this.user = user;
            } else {
                this.addMessageAction("error", response.message)
            }
            return response;

        },

        async updatePasswordAction(data) {
            const response = await updatePassword(data);
            console.log(response)

            if (response.success) {
                this.addMessageAction("success", response.message);
                const { token } = response.data.data;
                this.bearerToken = token;
                localStorage.setItem('token', token);
            } else {
                this.addMensajeAction("error", response.message)
            }
            return response;
        },

        async fetchUserAction() {
            if (!this.bearerToken) return;

            const response = await fetchUser();

            if (response.success) {
                this.user = response.data;
                this.role = response.data.rol;
                this.isAuthenticated = true;
            } else {
                this.user = null;
                this.isAuthenticated = false;
                this.bearerToken = null;
                localStorage.removeItem('token');
            }
        },

        async logoutAction() {
            this.user = null;
            this.isAuthenticated = false;
            this.bearerToken = null;
            localStorage.removeItem('token');
        },

        //Admin methods
        async getUsers() {

            if (!this.isAuthenticated) {
                this.addMessageAction('error', "No estas logueado en el sistema");
                return;
            } else if (this.role.toLowerCase() !== 'admin') {
                this.addMessageAction('error', 'No estas autorizado para realizar esta accion')
                return;
            }


            const response = await fetchUsers();

            if (response.success) {
                this.addMessageAction('success', response?.message || "Se han obtenido correctamente los usuarios");
                return {
                    success: true,
                    data: response.data.data,
                    message: response.message
                };
            } else {
                this.addMessageAction('error', response?.message || "Error al intentar obtener los usuarios");
                return response;
            }

        },

        addMessageAction(type, message) {
            const messageStore = useMessageStore();
            messageStore.addMessage({ type, message });
        }
    }
})