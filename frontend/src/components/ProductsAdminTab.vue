<script setup>
    import { onMounted, defineEmits, ref } from 'vue'
    import { useAuthStore } from '../stores/authStore';
    import { Form, Field, ErrorMessage } from 'vee-validate'
    import * as yup from 'yup';

    const authStore = useAuthStore();

    const props = defineProps({
        products: Array
    })
    
    //Emit evento para obtencion de usuarios despues de realizar acciones CRUD 
    const emit = defineEmits(['fetchProducts']);

    //Variables para mostrar o no popups o formularios
    const updateProductPopUpActive = ref(false);
    const deleteProductPopUpActive = ref(false);
    const addProductPopUpActive = ref(false);
    const importProductsPopUpActive = ref(false);
    const getLogsButtonActive = ref(false);
    const showLogsPopUpActive = ref(false);

    const addVariationFormActive = ref(false);
    
    const formDesplegables = ref({
        categoria: {
            isOpen: false
        },
        marca: {
            isOpen: false
        },
        sexo: {
            isOpen: false
        },
        deporte: {
            isOpen: false
        },
        altura: {
            isOpen: false
        }
    })

    //Informacion sobre producto seleccionado para eliminar, actualizar, obtencion de logs
    const productFormData = ref({});
    const productSelectedToDelete = ref({});
    const productVariations = ref([]);

    const logsData = ref(null);
    

    //Schemas de validacion para los diferentes tipos de formularios
    const schemaUpdateProduct = yup.object({
        marca: yup.string().required('Debes de seleccionar una marca'),
        categoria: yup.string().required('Debes de seleccionar una categoria'),
        nombre: yup.string().required('Debes de asignar un nombre'),
        precio: yup.decimal().required('Debes de especificar el precio'),
        ajuste: yup.string().required("Debes de seleccionar un ajuste"),
        sexo: yup.string().required("Sebes de seleccionar un sexo"),
        descripcion: yup.string().required('Debes de seleccionar un rol'),
        altura: yup.string().required('Debes de seleccionar una altura'),
        deporte: yup.string().required('Debes de seleccionar un deporte'),
        oferta: yup.boolean().required('Debes de confirmar si es oferta o no'),
        precio_anterior: Yup.string().when('oferta', {
            is: true,
            then: (schema) => schema.required('Este campo es obligatorio si el producto tiene oferta'),
            otherwise: (schema) => schema.notRequired(),
        }),
        novedad: yup.boolean().required('Debes de confirmar si es novedad o no'),
        img: yup.string().required('Debes de añadir una imagen')
    })

    const schemaAddProduct = yup.object().shape({
        marca: yup.string().required('Debes de seleccionar una marca'),
        categoria: yup.string().required('Debes de seleccionar una categoria'),
        nombre: yup.string().required('Debes de asignar un nombre'),
        precio: yup.decimal().required('Debes de especificar el precio'),
        ajuste: yup.string().required("Debes de seleccionar un ajuste"),
        sexo: yup.string().required("Sebes de seleccionar un sexo"),
        descripcion: yup.string().required('Debes de seleccionar un rol'),
        altura: yup.string().required('Debes de seleccionar una altura'),
        deporte: yup.string().required('Debes de seleccionar un deporte'),
        oferta: yup.boolean().required('Debes de confirmar si es oferta o no'),
        precio_anterior: Yup.string().when('oferta', {
            is: true,
            then: (schema) => schema.required('Este campo es obligatorio si el producto tiene oferta'),
            otherwise: (schema) => schema.notRequired(),
        }),
        novedad: yup.boolean().required('Debes de confirmar si es novedad o no'),
        img: yup.string().required('Debes de añadir una imagen')
    })

    const schemaImportProducts = yup.object().shape({
        fichero: yup.mixed()
        .required('El fichero es obligatorio')
        .test('is-correct-file-tipe', "El fichero debe de ser una de las siguientes extensiones (csv, xlsx, xls)", (value) => {
            if (!value) return false;

            // Opción A: Comprobar la extensión por el nombre
            const fileName = value.name || "";
            const isExtensionValid = fileName.toLowerCase().endsWith('.csv') || fileName.toLowerCase().endsWith('.xlsx') || fileName.toLowerCase().endsWith('.xls');

            return isExtensionValid;
        })
    })


    //Metodos relacionados con los eventos de click a botones y demas (mostrar popups, rellenar formularios)
    const handleEditProduct = (product) => {
        updateProductPopUpActive.value = true;
        productFormData.value = {...product}
    }

    const handleDeleteProduct = (product) => {
        deleteProductPopUpActive.value = true;
        productSelectedToDelete.value = product;
    }

    const handleAddProduct = () => {
        addProductPopUpActive.value = true;
    }

    const handleImportProducts = () => {
        importProductsPopUpActive.value = true;
    }


    //Metodos relacionados con el CRUD de usuarios e importacion
    const onSubmitProductUpdate = async (data, { setFieldError }) => {

        if (data?.id) {
            const response = await authStore.updateProductAction(data.id, data);

            if (!response.success && response.info) {

                Object.entries(response.info).forEach(([field, messages]) => {
                    setFieldError(field, messages[0]);
                })

            } else {

                emit('fetchProducts')

                productFormData.value = {};
                updateProductPopUpActive.value = false;
                

            }

        } else {
            messageStore.addMessage('error', 'No se ha proporcionado del id del usuario');
        }
    }

    const onSubmitProductDelete = async (productId) => {

        deleteProductPopUpActive.value = false;


        if (productId) {
            const response = await authStore.deleteProductAction(productId);

            if (response.success) {
                emit('fetchProducts')
            }

        } else {
            messageStore.addMessage('error', 'No se ha proporcionado del id del usuario');
        }
    }

    const onSubmitAddProduct = async (productData, { setFieldError }) => {
        const response = await authStore.addProductAction(productData);

        if (!response.success && response.info) {
            Object.entries(response.info).forEach(([field, messages]) => {
                setFieldError(field, messages[0]);
            })
        } else {
            emit('fetchProducts')

            addProductPopUpActive.value = false;
        }
    }

    const onSubmitImportProducts = async (values, { setFieldError }) => {
        const formData = new FormData();
        formData.append("fichero", values.fichero);

        const response = await authStore.importProductsAction(formData);

        if (!response.success && response.info) {
            Object.entries(response.info).forEach(([field, messages]) => {
                setFieldError(field, messages[0]);
            })
        } else {
            emit('fetchProducts');
            getLogsButtonActive.value = true;
        }
    }

    const onSubmitAddVariation = (variation) => {
        
    } 


    //Metodos relacionados con logs de importacion
    const onSubmitGetLogs = async (page = 1) => {
        if (typeof page !== 'number') {
            page = 1;
        }

        const response = await authStore.getLogDataFromImports(page);
        console.log(response.data);
        

        if (response.success) {
            logsData.value = response.data;
            importProductsPopUpActive.value = false;
            showLogsPopUpActive.value = true;
        }
    }

    const handlePreviousLogsPage = async () => {
        if (logsData.value?.previous_page) {
            const response = await authStore.getLogDataFromImports(logsData.value.previous_page);

            if (response.success) {
                logsData.value = response.data;
            }
        }
    }

    const handleNextLogsPage = async () => {
        if (logsData.value?.next_page) {
            const response = await authStore.getLogDataFromImports(logsData.value.next_page);

            if (response.success) {
                logsData.value = response.data;
            }
        }
    }

    
</script>

<template>
    <div style="margin-top: 2em;">
        <div class="buttons-container">
            <button class="create-product-button" @click="handleAddProduct">Nuevo <i class="bi bi-plus"></i></button>
            <button class="import-products-button" @click="handleImportProducts">Importar <i class="bi bi-plus"></i></button>
            <button class="show-logs-button" @click="onSubmitGetLogs">Obtener logs</button>
        </div>
        
        <div class="cart-table-wrapper">
            <div v-if="products.length">
                <div class="updateTableButton"></div>
                <table class="products-table">
                    
                    <tr>
                        <th>Id</th>
                        <th>Nombre</th>
                        <th>Apellidos</th>
                        <th>Nombre usuario</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                    
                    <tr v-for="(product, index) in products">
                        <td>{{ product.id }}</td>
                        <td>{{ product.nombre }}</td>
                        <td>{{ product.apellidos }}</td>
                        <td>{{ product.nombre_usuario }}</td>
                        <td>{{ product.email }}</td>
                        <td>{{ product.rol }}</td>
                        <td class="actions">
                            <button @click="handleDeleteProduct(product)"><i class="bi bi-trash"></i></button>
                            <button @click="handleEditProduct(product)"><i class="bi bi-pencil"></i></button>
                        </td>
                    </tr>

                </table>
            </div>
        </div>

        <div class="popup-backdrop" v-if="updateProductPopUpActive" @click.self="updateProductPopUpActive = false">
            <div class="pop-up-edit-product-container">
                <div class="popup-header">
                    <h3>Editar Usuario</h3>
                    <button class="close-btn" @click="updateProductPopUpActive = false">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <Form :validation-schema="schemaUpdateProduct" :initial-values="productFormData" @submit="onSubmitProductUpdate">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Nombre</label>
                            <Field type="text" name="nombre" placeholder="Nombre"></Field>
                            <ErrorMessage name="nombre" class="error-msg" />
                        </div>

                        <div class="form-group">
                            <label>Apellidos</label>
                            <Field type="text" name="apellidos" placeholder="Apellidos"></Field>
                            <ErrorMessage name="apellidos" class="error-msg" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Nombre de usuario</label>
                        <Field type="text" name="nombre_usuario" placeholder="Usuario"></Field>
                        <ErrorMessage name="nombre_usuario" class="error-msg" />
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <Field type="text" name="email" placeholder="correo@ejemplo.com"></Field>
                        <ErrorMessage name="email" class="error-msg" />
                    </div>

                    <div class="form-group">
                        <label>Rol de usuario</label>
                        <Field as="select" name="rol">
                            <option value="admin">Admin</option>
                            <option value="client">Cliente</option>
                        </Field>
                        <ErrorMessage name="rol" class="error-msg" />
                    </div>

                    <div class="form-actions">
                        <button type="button" class="cancel-btn" @click="updateProductPopUpActive = false">Cancelar</button>
                        <button type="submit" class="save-btn">Guardar Cambios</button>
                    </div>
                </Form>
            </div>
        </div>

        <div class="popup-backdrop" v-if="deleteProductPopUpActive" @click.self="deleteProductPopUpActive = false">
            <div class="pop-up-edit-product-container">
                <div class="popup-header">
                    <h3>Eliminar Usuario</h3>
                    <button class="close-btn" @click="deleteProductPopUpActive = false">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <h2 style="margin-bottom:30px">Estas seguro de querer eliminar el usuario siguiente?</h2>

                <div class="product-data">

                    <div style="display: grid;">
                        <strong>Id</strong>
                        <p >{{ productSelectedToDelete.id }}</p>
                    </div>
                    

                    <div style="display: grid; grid-template-columns: 1fr 1fr;">
                        <div style="display: grid;">
                            <strong>Nombre</strong>
                            <p >{{ productSelectedToDelete.nombre }}</p>
                        </div>

                        <div style="display: grid;">
                            <strong>Apellidos</strong>
                            <p>{{ productSelectedToDelete.apellidos }}</p>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr;">
                        <div style="display: grid;">
                            <strong>Nombre usuario</strong>
                            <p>{{ productSelectedToDelete.nombre_usuario }}</p>
                        </div>

                        <div style="display: grid;">
                            <strong>Email</strong>
                            <p>{{ productSelectedToDelete.email }}</p>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="cancel-btn" @click="deleteProductPopUpActive = false">Cancelar</button>
                        <button type="submit" class="delete-btn" @click="onSubmitProductDelete(productSelectedToDelete.id)">Eliminar usuario</button>
                    </div>
                    
                </div>

                
            </div>
        </div>

        <div class="popup-backdrop" v-if="addProductPopUpActive" @click.self="addProductPopUpActive = false">
            <div class="pop-up-edit-product-container">
                <div class="popup-header">
                    <h3>Añadir Usuario</h3>
                    <button class="close-btn" @click="addProductPopUpActive = false">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <Form :validation-schema="schemaAddProduct" @submit="onSubmitAddProduct" v-slot="{ values, setFieldValue }">
                
                    <div class="form-group">
                        <label>Nombre</label>
                        <Field type="text" name="nombre" placeholder="Nombre del producto"></Field>
                        <ErrorMessage name="nombre" class="error-msg" />
                    </div>

                    <div class="form-group">
                        <label>Precio</label>
                        <Field type="number" step="0.01" name="precio" placeholder="Precio del producto"></Field>
                        <ErrorMessage name="precio" class="error-msg" />
                    </div>

                    <div class="form-group">
                        <label for="oferta" class="checkbox-item-toggler">
                            <strong style="font-size: 20px;">Oferta </strong>
                            <Field type="checkbox" name="oferta"></Field>
                            <span class="custom-toggler"></span>
                        </label>
                    </div>

                    <!-- <div class="form-group" v-if="values.oferta">
                        <label>Precio Anterior</label>
                        <Field type="text" name="precio-anterior" placeholder="Precio anterior"></Field>
                        <ErrorMessage name="nombre" class="error-msg" />
                    </div>   -->

                    

                    <!-- <div class="form-group">
                        <label>Stock</label>
                        <Field type="number" name="stock" placeholder="Stock del producto"></Field>
                        <ErrorMessage name="stock" class="error-msg" />
                    </div> -->

                    

                    <div class="form-group">
                        <label for="novedad" class="checkbox-item-toggler">
                            <strong style="font-size: 20px;">Novedad </strong>
                            <Field type="checkbox" name="novedades"></Field>
                            <span class="custom-toggler"></span>
                        </label>
                    </div>


                    <div class="form-group">
                        <label>Categoria</label>

                        <div class="custom-select">
                            <div class="selected-option" @click="formDesplegables.categoria.isOpen = !formDesplegables.categoria.isOpen">
                                <p> {{ cleanFormValue(values.categoria  || 'Selecciona la categoria') }}</p> <i :class="['bi bi-arrow-down', {'bi-arrow-active': formDesplegables.categoria.isOpen}]"></i> 
                            </div>
                            
                            <div :class="['options-container', {'active': formDesplegables.categoria.isOpen}]">
                                <ul class="options-list">
                                    <li @click="setFieldValue('categoria', 'zapatillas')" :class="{'option-active': values.categoria === 'zapatillas'}">
                                        Zapatillas
                                    </li>

                                    <li @click="setFieldValue('categoria', 'camisetas')" :class="{'option-active': values.categoria === 'camisetas'}">
                                        Camisetas
                                    </li>

                                    <li @click="setFieldValue('categoria', 'pantalones')" :class="{'option-active': values.categoria === 'pantalones'}">
                                        Pantalones
                                    </li>
                                </ul>
                            </div>
                        </div>

                        
                        <ErrorMessage name="categoria" class="error-msg" />
                    </div>

                    <div class="form-group">
                        <label>Marca</label>
                        
                        <div class="custom-select">
                            <div class="selected-option" @click="formDesplegables.marca.isOpen = !formDesplegables.marca.isOpen">
                                <p> {{ cleanFormValue(values.marca  || 'Selecciona la marca') }}</p> <i :class="['bi bi-arrow-down', {'bi-arrow-active': formDesplegables.marca.isOpen}]"></i> 
                            </div>
                            
                            <div :class="['options-container', {'active': formDesplegables.marca.isOpen}]">
                                <ul class="options-list">
                                    <li @click="setFieldValue('marca', 'nike')" :class="{'option-active': values.marca === 'nike'}">
                                        Nike
                                    </li>

                                    <li @click="setFieldValue('marca', 'adidas')" :class="{'option-active': values.marca === 'adidas'}">
                                        Adidas
                                    </li>

                                    <li @click="setFieldValue('marca', 'asics')" :class="{'option-active': values.marca === 'asics'}">
                                        Asics
                                    </li>
                                </ul>
                            </div>
                        </div>

                        
                        <ErrorMessage name="marca" class="error-msg" />
                    </div>

                    <div class="form-group">
                        <label>Ajuste</label>
                        
                        <div class="custom-select">
                            <div class="selected-option" @click="formDesplegables.ajuste.isOpen = !formDesplegables.ajuste.isOpen">
                                <p> {{ cleanFormValue(values.ajuste  || 'Selecciona el ajuste') }}</p> <i :class="['bi bi-arrow-down', {'bi-arrow-active': formDesplegables.ajuste.isOpen}]"></i> 
                            </div>
                            
                            <div :class="['options-container', {'active': formDesplegables.ajuste.isOpen}]">
                                <ul class="options-list">
                                    <li @click="setFieldValue('ajuste', 'ajustado')" :class="{'option-active': values.ajuste === 'ajustado'}">
                                        Ajustado
                                    </li>

                                    <li @click="setFieldValue('ajuste', 'holgado')" :class="{'option-active': values.ajuste === 'holgado'}">
                                        Holgado
                                    </li>

                                    <li @click="setFieldValue('ajuste', 'normal')" :class="{'option-active': values.ajuste === 'normal'}">
                                        Normal
                                    </li>
                                </ul>
                            </div>
                        </div>

                        
                        <ErrorMessage name="ajuste" class="error-msg" />
                    </div>

                    <div class="form-group">
                        <label>Altura</label>
                        
                        <div class="custom-select">
                            <div class="selected-option" @click="formDesplegables.altura.isOpen = !formDesplegables.altura.isOpen">
                                <p> {{ cleanFormValue(values.altura  || 'Selecciona la altura') }}</p> <i :class="['bi bi-arrow-down', {'bi-arrow-active': formDesplegables.altura.isOpen}]"></i> 
                            </div>
                            
                            <div :class="['options-container', {'active': formDesplegables.sexo.isOpen}]">
                                <ul class="options-list">
                                    <li @click="setFieldValue('altura', 'alto')" :class="{'option-active': values.sexo === 'alto'}">
                                        Alto
                                    </li>

                                    <li @click="setFieldValue('altura', 'bajo')" :class="{'option-active': values.sexo === 'bajo'}">
                                        Bajo
                                    </li>

                                    <li @click="setFieldValue('altura', 'normal')" :class="{'option-active': values.sexo === 'normal'}">
                                        Normal
                                    </li>
                                </ul>
                            </div>
                        </div>

                        
                        <ErrorMessage name="altura" class="error-msg" />
                    </div>
                    
                    <div class="form-group">
                        <label>Sexo</label>
                        
                        <div class="custom-select">
                            <div class="selected-option" @click="formDesplegables.sexo.isOpen = !formDesplegables.sexo.isOpen">
                                <p> {{ cleanFormValue(values.sexo  || 'Selecciona el sexo') }}</p> <i :class="['bi bi-arrow-down', {'bi-arrow-active': formDesplegables.sexo.isOpen}]"></i> 
                            </div>
                            
                            <div :class="['options-container', {'active': formDesplegables.sexo.isOpen}]">
                                <ul class="options-list">
                                    <li @click="setFieldValue('sexo', 'hombre')" :class="{'option-active': values.sexo === 'hombre'}">
                                        Hombre
                                    </li>

                                    <li @click="setFieldValue('sexo', 'mujer')" :class="{'option-active': values.sexo === 'mujer'}">
                                        Mujer
                                    </li>

                                    <li @click="setFieldValue('sexo', 'niño')" :class="{'option-active': values.sexo === 'niño'}">
                                        Niño
                                    </li>

                                    <li @click="setFieldValue('sexo', 'niña')" :class="{'option-active': values.sexo === 'niña'}">
                                        Niña
                                    </li>
                                </ul>
                            </div>
                        </div>

                        
                        <ErrorMessage name="sexo" class="error-msg" />
                    </div>

                    <div class="form-group">
                        <label>Deporte</label>
                        
                        <div class="custom-select">
                            <div class="selected-option" @click="formDesplegables.deporte.isOpen = !formDesplegables.deporte.isOpen">
                                <p> {{ cleanFormValue(values.deporte  || 'Selecciona el deporte') }}</p> <i :class="['bi bi-arrow-down', {'bi-arrow-active': formDesplegables.deporte.isOpen}]"></i> 
                            </div>
                            
                            <div :class="['options-container', {'active': formDesplegables.deporte.isOpen}]">
                                <ul class="options-list">

                                    <li @click="setFieldValue('deporte', 'general')" :class="{'option-active': values.deporte === 'general'}">
                                        General
                                    </li>
                                    
                                    <li @click="setFieldValue('deporte', 'trail')" :class="{'option-active': values.deporte === 'trail'}">
                                        Trail
                                    </li>

                                    <li @click="setFieldValue('deporte', 'futbol')" :class="{'option-active': values.deporte === 'futbol'}">
                                        Futbol
                                    </li>

                                    <li @click="setFieldValue('deporte', 'tenis')" :class="{'option-active': values.deporte === 'tenis'}">
                                        Tenis
                                    </li>

                                    <li @click="setFieldValue('deporte', 'padel')" :class="{'option-active': values.deporte === 'padel'}">
                                        Padel
                                    </li>

                                    <li @click="setFieldValue('deporte', 'baloncesto')" :class="{'option-active': values.deporte === 'baloncesto'}">
                                        Baloncesto
                                    </li>
                                    
                                </ul>
                            </div>
                        </div>

                        
                        <ErrorMessage name="deporte" class="error-msg" />
                    </div>

                    <div class="form-group">
                        <label>Descripcion</label>
                        <Field type="textarea" name="descripcion" placeholder="Descripcion del producto"></Field>
                        <ErrorMessage name="descripcion" class="error-msg" />
                    </div>
                    

                    <div class="form-actions">
                        <button type="button" class="cancel-btn" @click="addProductPopUpActive = false">Cancelar</button>
                        <button type="submit" class="add-btn">Añadir usuario</button>
                    </div>


                    <div class="variations-section">
                        <h3>Variaciones</h3>

                        <button class="add-variation-btn">
                            <i class="bi bi-plus"></i>
                        </button>

                        <Form v-if="addVariationFormActive" :validation-schema="schemaAddVariation" @submit="onSubmitAddVariation" v-slot="{ values, setFieldValue }"></Form>
                    </div>

                </Form>
            </div>
        </div>

        <div class="popup-backdrop" v-if="importProductsPopUpActive" @click.self="importProductsPopUpActive = false">
            <div class="pop-up-edit-product-container">
                <div class="popup-header">
                    <h3>Importar Productos</h3>
                    <button class="close-btn" @click="importProductsPopUpActive = false">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <Form :validation-schema="schemaImportProducts" @submit="onSubmitImportProducts">
                    
                    <div class="form-group">
                        <label>Fichero (csv,xlsx,xls)</label>
                        <Field name="fichero" placeholder="Selecciona fichero" v-slot="{ handleChange, field }">
                            <input type="file" @change="handleChange"/>
                        </Field>
                        <ErrorMessage name="fichero" class="error-msg" />
                    </div>

                    <div class="form-actions">
                        <button type="button" class="cancel-btn" @click="importProductsPopUpActive = false">Cancelar</button>
                        <button type="submit" class="add-btn">Importar usuarios</button>
                        <button type="button" class="show-logs-btn" @click="onSubmitGetLogs"></button>
                    </div>
                </Form>
            </div>
        </div>

        <div class="popup-backdrop" v-if="showLogsPopUpActive" @click.self="showLogsPopUpActive = false">
            <div class="pop-up-logs-container">
                <div class="popup-header">
                    <h3>Logs de importacion</h3>
                    <button class="close-btn" @click="showLogsPopUpActive = false">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="logs-container">
                        <div class="navigation-button-left" @click="handlePreviousLogsPage"><i class="bi bi-arrow-left"></i></div>
                        <div v-for="(log, index) in logsData.data" class="log">
                            <p>{{ log }}</p>
                        </div>
                        <div class="navigation-button-right" @click="handleNextLogsPage"><i class="bi bi-arrow-right"></i></div>
                </div>
            </div>
        </div>

    </div>
        

</template>

<style scoped>

    .buttons-container {
        display: grid;
        grid-template-columns: auto auto auto;
        width: auto;
        justify-self: end;
        gap: 20px;
    }

    .create-product-button {
        cursor: pointer;
        width: auto;
        height: 3em;
        border: none;
        background-color: #D72631;
        color: white;
        font-weight: 700;
        font-size: 20px;
        border-radius: 8px;
    }

    .import-products-button {
        cursor: pointer;
        width: auto;
        height: 3em;
        border: none;
        background-color: rgb(128, 226, 128);
        color: white;
        font-weight: 700;
        font-size: 20px;
        border-radius: 8px;
    }

    .show-logs-button {
        cursor: pointer;
        width: auto;
        height: 3em;
        border: none;
        background-color: rgb(0, 78, 245);
        color: white;
        font-weight: 700;
        font-size: 20px;
        border-radius: 8px;
    }

    .cart-table-wrapper {
        width: 100%;
        justify-self: center;
        max-height: 60vh;
        overflow-y: auto;
        margin: 20px 0;
        align-self: start;
        position: relative;
        box-shadow: inset 0 2px 2px 10px rgba(0, 0, 0, 0.5);
        
    }

    .products-table {
        width: 100%;
        background-color: #1F1F1F;
        color: white;
        border-collapse: collapse;
        table-layout: fixed;
        display: table;
    }

    .products-table tr {
        width: 100%;
        display: table-row; 
    }

    .products-table td, .products-table th {
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid #333;
    }

    .products-table th {
        background-color: #2D2D2D;
        position: sticky;
        top: 0;
        z-index: 1;
    }

    .actions {
        display: grid;
        grid-template-columns: 50px 50px;
        gap: 10px;
    }

    .actions button{
        border: none;
        background-color: #D72631;
        border-radius: 8px;
        width: 100%;
        height: 40px;
    }

    .actions button i{
        color: white;
    }




    /* Popup Backdrop */
    .popup-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background-color: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(5px);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        animation: fadeIn 0.3s ease;
    }

    /* Centered Popup Container */
    .pop-up-edit-product-container {
        border-radius: 24px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        background-color: #ffffff;
        padding: 30px;
        width: 90%;
        max-width: 500px;
        animation: slideUp 0.3s ease;
        position: relative;
    }

    /* Popup Animations */
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .popup-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        border-bottom: 1px solid #eee;
        padding-bottom: 15px;
    }

    .popup-header h3 {
        margin: 0;
        font-size: 1.5rem;
        color: #1a1a1a;
        font-weight: 700;
    }

    .close-btn {
        background: none;
        border: none;
        font-size: 1.25rem;
        color: #666;
        cursor: pointer;
        transition: color 0.2s;
        padding: 5px;
        line-height: 1;
    }

    .close-btn:hover {
        color: #D72631;
    }

    /* Form Layout */
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        margin-bottom: 18px;
    }

    .form-group label {
        font-weight: 600;
        font-size: 0.9rem;
        color: #444;
        margin-bottom: 6px;
    }

    .form-group input, 
    .form-group select {
        padding: 12px 16px;
        border: 1.5px solid #e0e0e0;
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.2s;
        background-color: #f9f9f9;
    }

    .form-group input:focus, 
    .form-group select:focus {
        border-color: #D72631;
        outline: none;
        background-color: #fff;
        box-shadow: 0 0 0 4px rgba(215, 38, 49, 0.1);
    }

    .error-msg {
        color: #d32f2f;
        font-size: 0.75rem;
        margin-top: 4px;
        font-weight: 500;
    }

    .checkbox-item-toggler {
        cursor: pointer;
        padding: 10px;
        display: grid;
        align-items: center;
        grid-template-columns: 1fr 1fr;
    }

    .checkbox-item-toggler input{
        display: none;
    }

    .checkbox-item-toggler input:checked + .custom-toggler {
        background-color: #ffdcdc;
    }

    .checkbox-item-toggler input:checked + .custom-toggler::after {
        transform: translateX(100%);
    }

    .custom-toggler {
        width: 40px;
        height: 20px;
        border: 2px solid #D72631;
        border-radius: 20px;
        display: inline-block;
        position: relative;
        justify-self: center;
        transition: all 0.5s ease;
    }

    .custom-toggler::after {
        content: "";
        left: 3px;
        top: 1px;
        position: absolute;
        width: 15px;
        height: 90%;
        background-color: black;
        border-radius: 100%;
        transition: all 0.5s ease;  
    }


    .custom-toggler-active {
        background-color: #cf8489;
    }

    /* Action Buttons */
    .form-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-top: 30px;
    }

    .save-btn {
        background-color: #D72631;
        color: white;
        border: none;
        padding: 14px;
        border-radius: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: transform 0.2s, background-color 0.2s;
    }

    .save-btn:hover {
        background-color: #b01f28;
        transform: translateY(-2px);
    }

    .save-btn:active {
        transform: translateY(0);
    }

    .cancel-btn {
        background-color: #f0f0f0;
        color: #444;
        border: none;
        padding: 14px;
        border-radius: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .cancel-btn:hover {
        background-color: #e5e5e5;
    }

    .product-data {
        display: grid;
        grid-template-rows: 1fr 1fr 1fr;
        row-gap: 20px;
    }

    .product-data p{
        margin: 0;
    }

    .delete-btn {
        background-color: #D72631;
        color: white;
        border: none;
        padding: 14px;
        border-radius: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: transform 0.2s, background-color 0.2s;
    }

    .delete-btn:hover {
        background-color: #b01f28;
        transform: translateY(-2px);
    }

    .delete-btn:active {
        transform: translateY(0);
    }

    .add-btn {
        background-color: #D72631;
        color: white;
        border: none;
        padding: 14px;
        border-radius: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: transform 0.2s, background-color 0.2s;
    }

    .add-btn:hover {
        background-color: #b01f28;
        transform: translateY(-2px);
    }

    .add-btn:active {
        transform: translateY(0);
    }

    .add-variation-btn {
        background-color: #bebebe;
        color: #666;
        border: none;
        outline: none;
        cursor: pointer;
        transition: transform 0.2s, background-color 0.2s;
    }

    .add-variation-btn:hover {
        background-color: #b01f28;
        transform: translateY(-2px);
    }





    .navigation-button-right, .navigation-button-left {
        background-color: #1F1F1F;
        color: white;
        width: 30px;
        height: 30px;
        border-radius: 100%;
        align-content: center;
        text-align: center;
        transition: all 0.3s ease-in-out;
        cursor: pointer;
        position: absolute;
    }

    .navigation-button-right {
        right: 0;
        top: 50%;
    }

    .navigation-button-left {
        left: 0;
        top: 50%;
    }

    .navigation-button:hover {
        transform: scale(1.1);
    }

    .navigation-button i{
        display: inline-block;
        width: auto;
    }




    .pop-up-logs-container {
        padding: 30px;
        width: 90%;
        max-width: 500px;
        border-radius: 24px;
        background-color: white;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        border-radius: 24px;        
        animation: slideUp 0.3s ease;
        position: relative;
    }

    .logs-container {
        display: flex;
        flex-direction: column;
        overflow-y: auto;
        height: 300px;
        gap: 8px;
    }

    .log {
        color: black;
        width: 100%;
        height: auto;
    }

</style>