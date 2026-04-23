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
            if (!this.bearerToken) {
                this.isAuthenticated = false;
                return;
            }

            // Evitar llamadas concurrentes si ya tenemos el usuario
            if (this.user) {
                this.isAuthenticated = true;
                return;
            }

            try {
                const response = await fetchUser();

                if (response.success && response.data) {
                    this.user = response.data;
                    this.role = response.data.rol || "";
                    this.isAuthenticated = true;
                } else {
                    this.logoutAction();
                }
            } catch (error) {
                console.error("Error fetching user:", error);
                this.logoutAction();
            }
        },

        async logoutAction() {
            this.user = null;
            this.isAuthenticated = false;
            this.bearerToken = null;
            localStorage.removeItem('token');
        },

        //ADMIN METHODS
        async getUsersAction() {

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

        async updateUserAction(user, data) {
            
            if (!this.isAuthenticated) {
                this.addMessageAction('error', "No estas logueado en el sistema");
                return;
            } else if (this.role.toLowerCase() !== 'admin') {
                this.addMessageAction('error', 'No estas autorizado para realizar esta accion')
                return;
            }

            const response = await updateUser(user, data);

            if (response.success) {
                this.addMessageAction('success', response?.message || "Se ha actualizado correctamente el usuario");
                
                return {
                    success: true,
                    data: response.data,
                    info : response?.info,
                    message: response.message
                };
            } else {
                this.addMessageAction('error', response?.message || "Error al intentar actualizar el usuario");
                return response;
            }
        },

        async deleteUserAction(userId) {
            if (!this.isAuthenticated) {
                this.addMessageAction('error', 'No estas logueado en el sistema');
                return;
            } else if (this.role.toLowerCase() !== 'admin') {
                this.addMessageAction('error', 'No estas autorizado para realizar esta accion');
                return;
            }

            const response = await deleteUser(userId);

            if (response.success) {
                this.addMessageAction('success', response.message || 'Se ha eliminado correctamente el usuario');
                return response;
            } else {
                this.addMessageAction('error', response.message || 'Error al intentar eliminar el usuario');
            }
        },

        addMessageAction(type, message) {
            const messageStore = useMessageStore();
            messageStore.addMessage({ type, message });
        }
    }
})