import axios from "axios";


// Instancia para invitados (sin token)
export const publicApi = axios.create({
    baseURL: '/api/',
    timeout: 30000,
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
    }});

// Instancia para usuarios logueados (con interceptor de token)
export const privateApi = axios.create({
    baseURL: '/api/',
    timeout: 30000,
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
    }});

privateApi.interceptors.request.use(config => {
    config.headers.Authorization = `Bearer ${localStorage.getItem('token')}`;
    return config;
});

const fetchProducts = async (query = {}) => {
    try {
        console.log("Axios: Fetching products with query:", query);
        const response = await publicApi.get('products', {
            params: {
                nombre: query?.nombre,
                categoria: query?.categoria,
                marca: query?.marca,
                deporte: query?.deporte,
                altura: query?.altura, 
                sexo: query?.sexo,
                precio_max: query?.precio_max
            }
        });
        console.log("Axios: Response received:", response.data);

        return {
            success: true,
            data: response.data,
            message: "Productos cargados correctamente"
        }
    } catch(error) {
        console.error("Axios: Error in fetchProducts:", error);
        if (error.response || error.statusText) {
            return {
                success: false,
                data: `Error ${error.response?.status || 'Tipo sin especificar'} : ${error.statusText || 'Descripcion sin especificar'}`,
                message: "Ha habido un error al cargar los productos"
            }
        } else {
            return {
                success: false,
                data: error.message || 'Sin descripcion de error',
                message: "Ha habido un error al cargar los productos"
            }
        }
    }
}

const fetchMostPurchasedProducts = async () => {
    try {
        const response = await publicApi.get('/most-purchased');

        return {
            success: true,
            data: response.data,
            message: "Productos mas comprados cargados correctamente"
        }
        
    } catch (error) {
        if (error.response || error.statusText) {
            return {
                success: false,
                data: `Error ${error.response.status || 'Tipo sin especificar'} : ${error.statusText || 'Descripcion sin especificar'}`,
                message: "Ha habido un error al cargar los productos"
            }
        } else {
            return {
                success: false,
                data: error.message || 'Sin descripcion de error',
                message: "Ha habido un error al cargar los productos"
            }
        }
    }
}

// AUTH METHODS

const login = async (data) => {

    try {

        const response = await publicApi.post('/login', data);

        return {
            success: true,
            data: response.data,
            message: "Se ha logueado"
        }

        
    } catch (error) {
        if (error.response || error.statusText) {
            return {
                success: false,
                data: `Error ${error.response.status || 'Tipo sin especificar'} : ${error.statusText || 'Descripcion sin especificar'}`,
                message: "Ha habido un error al cargar los productos"
            }
        } else {
            return {
                success: false,
                data: error.message || 'Sin descripcion de error',
                message: "Ha habido un error al intentar iniciar sesion"
            }
        }
    }

}

const register = async (data) => {
    try {
        const response = await publicApi.post('/register', data);
        return {
            success: true,
            data: response.data,
            message: "Se ha registrado correctamente"
        }
    } catch (error) {
        if (error.response || error.statusText) {
            return {
                success: false,
                data: `Error ${error.response.status || 'Tipo sin especificar'} : ${error.statusText || 'Descripcion sin especificar'}`,
                message: "Ha habido un error al registrarse"
            }
        } else {
            return {
                success: false,
                data: error.message || 'Sin descripcion de error',
                message: "Ha habido un error al intentar registrarse"
            }
        }
    }
}

export {
    fetchProducts,
    fetchMostPurchasedProducts,
    login,
    register
}