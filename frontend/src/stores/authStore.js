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
        async loginAction(data) {
            const response = await login(data);

            console.log(response.data)

            if (response.success) {
                this.addMensajeAction("success", response.message);
                // La respuesta estandarizada es { success: true, data: { token: ..., user: ... }, message: ... }
                const { token, user } = response.data.data;
                this.user = user;
                this.isAuthenticated = true;
                this.bearerToken = token;
                localStorage.setItem("token", token);
                return response;
            } else {
                this.addMensajeAction("error", response.message);
                return false;
            }
        },

        async registerAction(data) {
            const response = await register(data);

            if (response.success) {
                this.addMensajeAction("success", response.message);
                const { token, user } = response.data.data;
                this.user = user;
                this.isAuthenticated = true;
                this.bearerToken = token;
                localStorage.setItem("token", token);
                
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