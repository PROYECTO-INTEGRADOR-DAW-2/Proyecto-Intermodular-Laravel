import axios from "axios";
const api = axios.create({
  baseURL: 'https://app.projectegrupb.es/api/',
  timeout: 5000,
  headers: {'Authorization': 'Bearer TOKEN_AQUÍ'}
});

const fetchProducts = async (query = {}) => {

    try {
        const response = await api.get('/products', {
            params: {
                nombre: query?.nombre,
                categoria: query?.categoria,
                marca: query?.marca,
                sexo: query?.sexo
            }
        });

        return {
            success: true,
            data: response.data,
            message: "Productos cargados correctamente"
        }
    } catch(error) {
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

export {
    fetchProducts

}