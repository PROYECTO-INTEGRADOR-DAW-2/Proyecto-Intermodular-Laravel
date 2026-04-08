import axios from "axios";


// Instancia para invitados (sin token)
export const publicApi = axios.create({
    baseURL: '/api/',
    timeout: 30000,
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
    }
});

// Instancia para usuarios logueados (con interceptor de token)
export const privateApi = axios.create({
    baseURL: '/api/',
    timeout: 30000,
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
    }
});

privateApi.interceptors.request.use(config => {
    config.headers.Authorization = `Bearer ${localStorage.getItem('token')}`;
    return config;
});

// PRODUCT METHODS

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
                precio_max: query?.precio_max,
                novedades: query?.novedades,
                ofertas: query?.ofertas,
                page: query?.page
            }
        });
        
        console.log("Axios: Response received:", response.data);

        return {
            success: true,
            data: response.data,
            message: "Productos cargados correctamente"
        }
    } catch (error) {
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

const fetchProduct = async (id) => {
    try {
        console.log(`Axios: Fetching product with id: ${id}`);
        const response = await publicApi.get(`products/${id}`)

        
        console.log("Axios: Response received:", response.data);

        return {
            success: true,
            data: response.data,
            message: "Producto cargado correctamente"
        }

    } catch (error) {
        console.error("Axios: Error in fetchProduct:", error);
        if (error.response) {
            return {
                success: false,
                data: `Error ${error.response?.status || 'Tipo sin especificar'} : ${error.response?.message || 'Descripcion sin especificar'}`,
                info: error.response?.info || 'Sin informacion del error',
                message: error.response?.message || "Ha habido un error al cargar los productos"
            }
        } else {
            return {
                success: false,
                data: error.message || 'Sin descripcion de error',
                message: error.message
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

// REVIEW METHODS

const fetchReviewsFromProduct = async (product) => {
    try {
        const response = await publicApi.get(`/products/${product}/reviews`)
        console.log("Axios: Response received:", response.data)

        return {
            success: true,
            data: response.data,
            message: response.message
        }

    } catch (error) {
        console.error("Axios: Error in fetchReviewsFromProduct:", error);
        if (error.response) {
            return {
                success: false,
                data: `Error ${error.response?.status || 'Tipo sin especificar'} : ${error.response?.message || 'Descripcion sin especificar'}`,
                info: error.response?.info || 'Sin informacion del error',
                message: error.response?.message || "Ha habido un error al cargar los productos"
            }
        } else {
            return {
                success: false,
                data: error.message || 'Sin descripcion de error',
                message: error.message
            }
        }
    }
}

const addReview = async (data, product) => {
    try {
        const response = await privateApi.post(`/products/${product}/reviews`, data);
        console.log("Axios: Response received:", response.data)

        return {
            success: true,
            data: response.data,
            message: response.message
        }
    } catch (error) {
        if (error.response) {
            return {
                success: false,
                data: `Error ${error.response?.status || 'Tipo sin especificar'} : ${error.response?.message || 'Descripcion sin especificar'}`,
                info: error.response?.info || 'Sin informacion del error',
                message: error.response?.message || "Ha habido un error al añadir la valoracion"
            }
        } else {
            return {
                success: false,
                data: error.message || 'Sin descripcion de error',
                message: error.message
            }
        }
    }
}

const updateReview = async (data, product, review) => {
    try {
        const response = await privateApi.put(`/products/${product}/reviews/${review}`, data);
        console.log("Axios: Response received:", response.data)

        return {
            success: true,
            data: response.data,
            message: response.message
        }
    } catch (error) {
        if (error.response) {
            return {
                success: false,
                data: `Error ${error.response?.status || 'Tipo sin especificar'} : ${error.response?.message || 'Descripcion sin especificar'}`,
                info: error.response?.info || 'Sin informacion del error',
                message: error.response?.message || "Ha habido un error al actualizar la valoracion"
            }
        } else {
            return {
                success: false,
                data: error.message || 'Sin descripcion de error',
                message: error.message
            }
        }
    }
}

const deleteReview = async (product, review) => {
    try {
        const response = await privateApi.delete(`/products/${product}/reviews/${review}`);
        console.log("Axios: Response received:", response.data)

        return {
            success: true,
            data: response.data,
            message: response.message
        }
    } catch (error) {
        if (error.response) {
            return {
                success: false,
                data: `Error ${error.response?.status || 'Tipo sin especificar'} : ${error.response?.message || 'Descripcion sin especificar'}`,
                info: error.response?.info || 'Sin informacion del error',
                message: error.response?.message || "Ha habido un error al eliminar la valoracion"
            }
        } else {
            return {
                success: false,
                data: error.message || 'Sin descripcion de error',
                message: error.message
            }
        }
    }
}


// WISHLIST METHODS

const fetchWishlist = async () => {

    try {
        const response = privateApi.get('/wishlist');
        console.log("Axios: Response received:", response.data)

        return {
            success: true,
            data: response.data,
            message: response.message,
        }


    } catch (error) {
        if (error.response) {
            return {
                success: false,
                data: `Error ${error.response?.status || 'Tipo sin especificar'} : ${error.response?.message || 'Descripcion sin especificar'}`,
                info: error.response?.info || 'Sin informacion del error',
                message: error.response?.message || "Ha habido un error al obtener la lista de deseos"
            }
        } else {
            return {
                success: false,
                data: error.message || 'Sin descripcion de error',
                message: error.message
            }
        }
    }

}

const toggleWishlistItem = async (idItem) => {

    try {
        const response = privateApi.post(`/wishlist`, {product_id: idItem});
        console.log("Axios: Response received:", response.data)

        return {
            success: true,
            data: response.data,
            message: response.message
        }

    } catch (error) {
        if (error.response) {
            return {
                success: false,
                data: `Error ${error.response?.status || 'Tipo sin especificar'} : ${error.response?.message || 'Descripcion sin especificar'}`,
                info: error.response?.info || 'Sin informacion del error',
                message: error.response?.message || "Ha habido un error al eliminar el producto de la lista de deseos" 
            }
        } else {
            return {
                success: false,
                data: error.message || 'Sin descripcion de error',
                message: error.message
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
        console.error("Axios: Error in login:", error);
        let errorData = "Sin descripcion de error";

        if (error.response && error.response.data) {
            errorData = error.response.data.message || error.response.data.error || JSON.stringify(error.response.data);
            if (error.response.data.info && error.response.data.info.error) {
                errorData += ": " + error.response.data.info.error;
            }
        } else if (error.statusText) {
            errorData = error.statusText;
        } else {
            errorData = error.message;
        }

        return {
            success: false,
            data: `Error ${error.response?.status || '500'} : ${errorData}`,
            message: "Ha habido un error al intentar iniciar sesion"
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
        if (error.response) {
            return {
                success: false,
                data: error.response.data,
                info: error.response.data.info || error.response.data.errors,
                message: error.response.data.message || "Ha habido un error al actualizar el perfil"
            }
        } else {
            return {
                success: false,
                data: error.message || 'Sin descripcion de error',
                message: "Ha habido un error al intentar actualizar el perfil"
            }
        }
    }
}

const updateProfile = async (data) => {
    try {
        const response = await privateApi.put('/update-profile', data);

        return {
            success: true,
            data: response.data,
            message: "Se ha actualizado el perfil correctamente"
        }

    } catch (error) {
        if (error.response) {
            return {
                success: false,
                data: `Error ${error.response.status || 'Tipo sin especificar'}`,
                info: error.response.data.info || 'Sin informacion',
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

const updatePassword = async (data) => {
    try {
        const response = await privateApi.put('/update-password', data);

        return {
            success: true,
            data: response.data,
            message: response.message
        }

    } catch (error) {
        if (error.response) {
            return {
                success: false,
                data: error.response.data,
                info: error.response.data.info || error.response.data.errors,
                message: error.response.data.message || "Ha habido un error al cambiar la contraseña"
            }
        } else {
            return {
                success: false,
                data: error.message || 'Sin descripcion de error',
                message: "Ha habido un error al intentar cambiar la contraseña"
            }
        }
    }

}

const fetchUser = async () => {
    try {
        const response = await privateApi.get('/user');
        return {
            success: true,
            data: response.data,
            message: "Usuario cargado correctamente"
        }
    } catch (error) {
        return {
            success: false,
            data: error.response?.data || error.message,
            message: "Error al cargar el usuario"
        }
    }
}

export {
    fetchProducts,
    fetchProduct,
    fetchMostPurchasedProducts,
    fetchReviewsFromProduct,
    addReview,
    updateReview,
    deleteReview,
    fetchWishlist,
    toggleWishlistItem,
    login,
    register,
    updateProfile,
    updatePassword,
    fetchUser
}