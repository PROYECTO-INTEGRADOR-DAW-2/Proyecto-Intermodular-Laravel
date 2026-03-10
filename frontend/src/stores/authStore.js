import { defineStore } from 'pinia';
import { login, register } from '../services/api.js'

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        isAuthenticated: false,
        messages: [],
        bearerToken: localStorage.getItem('token') || null
    }),
    actions: {
        async loginAction(nombre_usuario, contraseña) {
            const response = await login({nombre_usuario, contraseña});

            if (response.success) {
                this.addMensajeAction("success", response.message);
                this.user = response.data;
                this.isAuthenticated = true;
                localStorage.setItem("token", response.token);
                return response;
            } else {
                this.addMensajeAction("error", response.message);
                return false;
            }
        },

        async registerAction(name, username, email, password) {
            const response = await register(name, username, email, password);

            if (response.success) {
                this.addMensajeAction("success", response.message);
                this.user = response.data;
                this.isAuthenticated = true;
                localStorage.setItem("token", response.token);
                
                return response
            } else {
                this.addMensajeAction("error", response.message)
                return false;
            }
        },

        addMensajeAction(type, message) {
            switch (type) {
                case "success":
                    this.messages.push({type, message})
                    break;
            
                case "error":
                    this.messages.push({type, message})
                    break;
            }
        }
    }
})