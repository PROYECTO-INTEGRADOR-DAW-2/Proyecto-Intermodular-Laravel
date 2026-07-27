<script setup>
    import { onMounted, defineEmits, ref, watch, computed } from 'vue'
    import { useAuthStore } from '../stores/authStore';
    import { Form, Field, ErrorMessage, useForm } from 'vee-validate'
    import { useField } from 'vee-validate'; // Si no está importado ya
    import * as yup from 'yup';
    import { useMessageStore } from '../stores/messageStore';
    import { nextTick } from 'vue'; // Asegúrate de tener esto arriba en tu script

    import UpdateVariationModal from './UpdateVariationModal.vue';

    const authStore = useAuthStore();
    const messageStore = useMessageStore();

    // Ref para acceder directamente al input de ficheros de imágenes
    const imagenesMainRef = ref(null);
    const imagenMainRef = ref(null);

    const mainImageFile = ref(null);
    const mainImageGallery = ref(null);

    const variationImageFile = ref(null);
    const variationImageGallery = ref(null);

    const fakeMainImgPreview = computed( () => {

        if (!mainImageFile.value) return '';

        // Como mainImageFile es un archivo real, creamos la URL directo
        return URL.createObjectURL(mainImageFile.value);

    })

    const fakeVariationImgPreview = computed( () => {

        if (!variationImageFile.value) return '';

        // Como mainImageFile es un archivo real, creamos la URL directo
        return URL.createObjectURL(variationImageFile.value);

    })

    const imagenesVariationRef = ref(null);
    const imagenMainVariationRef = ref(null);
    

    const props = defineProps({
        products: Array,
        metaData: Object,
    })

    const categorias = ref(["Zapatillas Infantil", "Prendas Infantil", "Zapatillas Adulto", "Prendas Adulto"]);
    const tallasDisponibles = ref([]);
    const variations = ref([]);

    const uniqueVariations = computed(() => {
        return [
            ...new Map(
                variations.value.map(variation => [
                    JSON.stringify([variation.color, variation.talla]), 
                    variation
                ])
            ).values()
        ];
    });
    
    
    //Emit evento para obtencion de usuarios despues de realizar acciones CRUD 
    const emit = defineEmits(['fetchProducts']);

    //Variables para mostrar o no popups o formularios
    const updateProductPopUpActive = ref(false);
    const deleteProductPopUpActive = ref(false);
    const addProductPopUpActive = ref(false);
    const importProductsPopUpActive = ref(false);
    
    const getLogsButtonActive = ref(false);
    const showLogsPopUpActive = ref(false);

    const currentImagePopUp = ref(null);
    const imagePopUpActive = ref(false);

    const addVariationFormActive = ref(false);
    const updateVariationFormActive = ref(false);
    
    
    const formDesplegables = ref({
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
        },
        ajuste: {
            isOpen: false
        },
        categoria: {
            isOpen: false
        },
    
    })

    //Informacion sobre producto seleccionado para eliminar, actualizar, obtencion de logs
    const productFormData = ref({});
    const productSelectedToDelete = ref({});
    const productVariations = ref([]);
    const logsData = ref(null);
    const variationInitialValues = ref({});
    const currentVariationToEdit = ref(null);

    

    //Schemas de validacion para los diferentes tipos de formularios
    const schemaUpdateProduct = yup.object({
        marca: yup.string().required('Debes de seleccionar una marca'),
        categoria: yup.string().required('Debes de seleccionar una categoria'),
        nombre: yup.string().required('Debes de asignar un nombre'),
        precio: yup.number().test(
            'is-decimal',
            'El campo debe tener máximo 2 decimales',
            (value) => value === undefined || value === null || (value + "").match(/^\d+(\.\d{1,2})?$/),
        ).required('El precio es obligatorio'),
        stock: yup.number().max(500),
        ajuste: yup.string().required("Debes de seleccionar un ajuste"),
        sexo: yup.string().required("Sebes de seleccionar un sexo"),
        descripcion: yup.string().required('Debes de añadir una descripción'),
        altura: yup.string().required('Debes de seleccionar una altura'),
        deporte: yup.string().required('Debes de seleccionar un deporte'),
        oferta: yup.boolean().default(false),
        /* precio_anterior: yup.string().when('oferta', {
            is: true,
            then: (schema) => schema.required('Este campo es obligatorio si el producto tiene oferta'),
            otherwise: (schema) => schema.notRequired(),
        }), */
        novedad: yup.boolean().default(false),
        isSimple: yup.boolean().default(false),
        'imagenes_secundarias': yup.mixed().required(),
        'imagen_main': yup.mixed().required()
    })

    const schemaAddProduct = yup.object().shape({
        marca: yup.string().required('Debes de seleccionar una marca'),
        categoria: yup.string().required('Debes de seleccionar una categoria'),
        nombre: yup.string().required('Debes de asignar un nombre'),
        precio: yup.number().test(
            'is-decimal',
            'El campo debe tener máximo 2 decimales',
            (value) => value === undefined || value === null || (value + "").match(/^\d+(\.\d{1,2})?$/),
        ).required('El precio es obligatorio'),
        stock: yup.number().max(500),
        ajuste: yup.string().required("Debes de seleccionar un ajuste"),
        sexo: yup.string().required("Sebes de seleccionar un sexo"),
        descripcion: yup.string().required('Debes de añadir una descripción'),
        altura: yup.string().required('Debes de seleccionar una altura'),
        deporte: yup.string().required('Debes de seleccionar un deporte'),
        oferta: yup.boolean().default(false),
        isSimple: yup.boolean().default(false),
        /* precio_anterior: yup.string().when('oferta', {
            is: true,
            then: (schema) => schema.required('Este campo es obligatorio si el producto tiene oferta'),
            otherwise: (schema) => schema.notRequired(),
        }), */
        novedad: yup.boolean().default(false),
        'imagenes-secundarias': yup.mixed().required(),
        'imagen-main': yup.mixed().required()
    })

    const schemaAddVariation = yup.object().shape({
        nombre: yup.string().required("Debes de añadir un nombre a la variacion"),
        color: yup.mixed().required("Debes de seleccionar el color de la variacion"),
        talla: yup.mixed().required("Debes de seleccionar la talla de la variacion"),
        stock: yup.number().required("Debes de introducir el stock de la variacion"),
        'imagenes-secundarias-variacion': yup.mixed().required(),
        'imagen-main-variacion': yup.mixed().required()
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

    // Inicialización del formulario de añadir producto con useForm para sincronización total
    const { 
        values: addProductValues, 
        errors: addProductErrors,
        setFieldValue: setAddProductFieldValue,
        setFieldError: setAddProductFieldError,
        handleSubmit: handleAddProductSubmit,
        resetForm: resetAddProductForm 
    } = useForm({
        validationSchema: schemaAddProduct,
        initialValues: {
            oferta: false,
            novedad: false,
            isSimple: false,
            nombre: '',
            precio: 0
        }
    });

    const { 
        values: updateProductValues, 
        errors: updateProductErrors,
        setFieldValue: setUpdateProductFieldValue,
        setFieldError: setUpdateProductFieldError,
        handleSubmit: handleUpdateProductSubmit,
        resetForm: resetUpdateProductForm
    } = useForm({
        validationSchema: schemaUpdateProduct,
        initialValues: productFormData,
        enableReinitialize: true
    });
    



    const addVariationInitialValues = async () => {

        const initialData = {
            categoria_talla: undefined
        };

        const response = await determineSizeCategory(addProductValues.sexo, addProductValues.categoria);

        if (response) initialData.categoria_talla = response;

        return initialData;
    }

    const determineSizeCategory = async (sexo, categoriaProducto, ) => {
        if (sexo && categoriaProducto) {
            const genderLower = sexo.toLowerCase();
            const productCategoryLower = categoriaProducto.toLowerCase();

            let sizeCategory = undefined;

            switch (genderLower) {
                case 'mujer':
                case 'hombre':
                    if (productCategoryLower === 'zapatillas') sizeCategory = 'Zapatillas Adulto'
                    if (productCategoryLower === 'camisetas' || productCategoryLower === 'pantalones') sizeCategory = 'Prendas Adulto'

                    break;
            
                case 'niño':
                case 'niña':
                    if (productCategoryLower === 'zapatillas') sizeCategory = 'Zapatillas Infantil'
                    if (productCategoryLower === 'camisetas' || productCategoryLower === 'pantalones') sizeCategory = 'Prendas Infantil'
            
                default:
                    break;
            }

            const response = await handleSelectVariationCategory(sizeCategory);

            if (response) return sizeCategory;
            else return false;

        }
    }

    //Metodos relacionados con los eventos de click a botones y demas (mostrar popups, rellenar formularios, cambio de valores en selects)
    const handleEditProduct = async (product) => {
        // 1. Abrimos el popup
        updateProductPopUpActive.value = true;

        // 2. Esperamos a que Vue renderice el modal en el DOM para que existan los inputs
        await nextTick();

        // 3. Mapeamos los datos correctos en tu variable reactiva
        productFormData.value = {
            ...product
        };

        // 4. Reseteamos el formulario pasándole la estructura que tus campos esperan
        resetUpdateProductForm({
            values: {
                ...product
            }
        });

        const productBrand = product.marca.charAt(0).toUpperCase() + product.marca.slice(1).toLowerCase();
        const productCategory = product.categoria.toLowerCase();
        const productImgName = product.img;

        if (!productBrand || !productCategory) return;
        
        if (productImgName) {
            const finalUrl = `/api/productos/imagen/${productBrand}/${productCategory}/${productImgName}`;

            //Hacemos un fetch de la imagen
            const respuesta = await fetch(finalUrl);
            const blob = await respuesta.blob();

            //Creamos un objeto File con la imagen del producto a editar
            const file = new File([blob], productImgName, { type: blob.type });
            
            //Con datatransfer creamos una lista de ficheros y lo asignamos al input de imagen main
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            const fileList = dataTransfer.files;

            if (imagenMainRef.value) {
                imagenMainRef.value.files = fileList;
                setUpdateProductFieldValue('imagen_main', fileList);
                imagenMainRef.value.dispatchEvent(new Event('change', { bubbles: true }));
            }
        } 
        

        // Procesamiento y carga de imagenes de la galeria de imagenes del producto
        if (product.imgGallery && product.imgGallery.length > 0) {
            const imgGalleryList = product.imgGallery
            const dataTransfer = new DataTransfer();

            for (const img of imgGalleryList) {
                const imgName = img.nombre;
                const finalUrl = `/api/productos/imagen/${productBrand}/${productCategory}/${imgName}`;

                try {
                    const respuesta = await fetch(finalUrl);
                    const blob = await respuesta.blob();
                    const file = new File([blob], imgName, { type: blob.type });

                    // Ahora sí, se añade en orden y esperando la descarga
                    dataTransfer.items.add(file);
                } catch (error) {
                    console.error(`Error descargando imagen de la galería (${imgName}):`, error);
                }
            }

            const fileGalleryList = dataTransfer.files;

            if (imagenesMainRef.value) {
                imagenesMainRef.value.files = fileGalleryList;
                setUpdateProductFieldValue('imagenes_secundarias', fileGalleryList);
                imagenesMainRef.value.dispatchEvent(new Event('change', { bubbles: true}));
            }

        }


        if (product?.variaciones && product.variaciones.length > 0) {
            variations.value = product.variaciones;
        }


        
    }

    const handleDeleteProduct = (product) => {
        deleteProductPopUpActive.value = true;
        productSelectedToDelete.value = product;
    }

    const handleAddProduct = () => {
        addProductPopUpActive.value = true;
        resetAddProductForm();
    }

    const handleImportProducts = () => {
        importProductsPopUpActive.value = true;
    }


    const handleDeleteVariation = (variation) => {
        if (!variation) {
            return;
        }

        variations.value = variations.value.filter((v) => { return v.id !== variation.id });
    };

    const handleEditVariation = async (variation) => {
        updateVariationFormActive.value = true;
        
        // 1. Guardamos la variación clonada inmediatamente
        currentVariationToEdit.value = JSON.parse(JSON.stringify(variation));

    }

    const handleSelectVariationCategory = async (category) => {

            /* if (!addProductValues.sexo) {
                setFieldError('categoria_talla', "No has seleccionado el sexo del producto principal");
                return;
            } else if (!addProductValues.categoria) {
                setFieldError('categoria_talla', "No has seleccionado la categoría del producto principal");
                return;
            }

            setFieldValue('categoria_talla', category); */

        tallasDisponibles.value = [];

        let cleanedGenderValue = "";
        let cleanedCategoryProductValue = "";

        //Si estamos editando leemos datos de los initial values del ref de productFormData si no de addProductValues
        cleanedGenderValue = addProductValues.sexo.charAt(0).toUpperCase() + addProductValues.sexo.slice(1);
        cleanedCategoryProductValue = addProductValues.categoria.charAt(0).toUpperCase() + addProductValues.categoria.slice(1);
        

        const response = await authStore.getSizesAction(category, cleanedGenderValue, cleanedCategoryProductValue);

        if (response.success) {
                tallasDisponibles.value = response.data;
                return true;
        }
        else return false;

    }

    const handleToggleVariationForm = async () => {

        if (addVariationFormActive.value) {
            addVariationFormActive.value = false;
            return;
        }

        if (!addProductValues.sexo) {
            setAddProductFieldError('sexo', "Debes de seleccionar un sexo");
            return;
        } else if (!addProductValues.categoria) {
            setAddProductFieldError('categoria', "Debes de seleccionar una categoria");
            return;
        } else if (addProductValues.isSimple) {
            messageStore.addMessage({type: 'error', message: 'El producto consta como simple'})
            return;
        }

        // Cargamos los valores iniciales de forma asíncrona antes de mostrar el formulario
        variationInitialValues.value = await addVariationInitialValues();

        addVariationFormActive.value = true;

    }


    const handleAddMainImage = (event, gallery) => {
        const archivos = event.target.files;

        if (archivos && archivos.length > 0) {

            Array.from(archivos).forEach(archivo => {
                console.log("Imagen:", archivo.name);

                if (!archivo.type.startsWith("image")) {
                    console.log("Fichero:", archivo.name, "no es una imagen, ignorando todas las imagenes");
                    return;
                }
            }); 

            if (gallery) {
                mainImageGallery.value = archivos;
            } else {
                const file = event.target.files[0];

                if (file) {
                    mainImageFile.value = file; // 🎯 Guardamos el archivo aquí
                } else {
                    mainImageFile.value = null;
                }
            }



        } else {
            console.log("No se ha subido ninguna imagen");  
        }
    }

    const handleAddVariationImage = (event, gallery) => {
        const archivos = event.target.files;

        if (archivos && archivos.length > 0) {

            Array.from(archivos).forEach(archivo => {
                console.log("Imagen:", archivo.name);

                if (!archivo.type.startsWith("image")) {
                    console.log("Fichero:", archivo.name, "no es una imagen, ignorando todas las imagenes");
                    return;
                }
            }); 

            if (gallery) {
                variationImageGallery.value = archivos;
            } else {
                const file = event.target.files[0];

                if (file) {
                    variationImageFile.value = file; // 🎯 Guardamos el archivo aquí
                } else {
                    variationImageFile.value = null;
                }
            }

        } else {
            console.log("No se ha subido ninguna imagen");  
        }
    }

    const generateFakeImgUrl = (img) => {
        if (!img) {
            return;
        }

        const fakeUrl = URL.createObjectURL(img);

        return fakeUrl;
    }

    const getImgUrl = (img) => {
        if (!img) {
            return;
        }

        const imgName = img;
        const productBrand = productFormData.value?.marca.charAt(0).toUpperCase() + productFormData.value?.marca.slice(1);
        const productCategory = productFormData.value?.categoria;

        if (!imgName || !productBrand || !productCategory) {
            console.log("He fallado en validacion")
            return;
        };

        const finalUrl = `${import.meta.env.VITE_IMG_URL}/img/img${productBrand}/${productCategory}/${imgName}`;

        
        return finalUrl;
    }

    const handleChangeProductSelect = async () => {
        if (!addVariationFormActive.value) {
            return;
        }

        addVariationFormActive.value = false;
        variations.value = [];

        if (!addProductValues.sexo) {
            setAddProductFieldError('sexo', "Debes de seleccionar un sexo");
            return;
        } else if (!addProductValues.categoria) {
            setAddProductFieldError('categoria', "Debes de seleccionar una categoria");
            return;
        }

        variationInitialValues.value = await addVariationInitialValues();

        addVariationFormActive.value = true;
    }

    const handleOpenImage = (event) => {
        if (!event) {
            return;
        }

        currentImagePopUp.value = event.target.src;

        imagePopUpActive.value = true;

    }


    //Metodos relacionados con el CRUD de usuarios e importacion
    const onSubmitProductUpdate = async (data, { setFieldError }) => {

        if (data?.id) {
            const formData = new FormData();
            formData.append('_method', 'PUT');

            const specialFields = ['imagen_main', 'imagenes_secundarias', 'imgGallery', 'variaciones'];

            //1. Formateo de campos booleans a enteros y añadir datos menos las imagenes
            Object.entries(data).forEach(([key, value]) => {

                if (specialFields.includes(key.toLocaleLowerCase())) return;

                if (key === 'novedad' || key === 'oferta' || key === 'isSimple') {
                    formData.append(key, value ? 1 : 0);
                } else if (value !== undefined && value !== null) {
                    if (typeof value === 'string') formData.append(key, value.toLocaleLowerCase());
                    else formData.append(key, value);
                }
            });

            // 2. Leer los ficheros directamente desde el elemento del DOM via ref
            if (imagenesMainRef.value && imagenesMainRef.value.files && imagenesMainRef.value.files.length > 0) {
                Array.from(imagenesMainRef.value.files).forEach(file => {
                    formData.append('imagenes_secundarias[]', file);
                });
            } else {
                console.warn('No se encontraron imágenes secundarias en el ref');
            }

            // 3. Procesamiento de imagen principal de producto
            if (imagenMainRef.value && imagenMainRef.value.files && imagenMainRef.value.files.length > 0) {
                const imagenPrincipal = imagenMainRef.value.files[0];
                console.log(imagenPrincipal);
                formData.append('imagen_main', imagenPrincipal);
            } else {
                console.warn('No se ha encontrado la imagen principal en el ref');
            }

            //PROCESAMIENTO DE VARIACIONES

            variations.value.forEach((variacion, index) => {

                const rawKeys = ['nombre', 'stock', 'precio_especifico', 'id', 'sku', 'product_id', 'oferta', 'precio_oferta']
                const unnecesaryKeys = ['size', 'color', 'img_gallery'];

                //Recorremos los valores de la variacion sin tener en cuenta la estructura compleja de imagenes secundarias
                Object.entries(variacion).forEach(([key, value]) => {

                    if (unnecesaryKeys.includes(key.toLocaleLowerCase())) return;
                    else if (key !== 'imagenes-secundarias-variacion' && key !== 'imagen-main-variacion') { console.log(key, ":", value); formData.append(`variaciones[${index}][${key}]`, value)};
                
                });

                if (variacion['imagen-main-variacion']) {
                    formData.append(`variaciones[${index}][imagen-main-variacion]`, variacion['imagen-main-variacion'][0])
                }
                
                // Recorremos el FileList de las imágenes secundarias y las metemos con otro corchete []
                if (variacion['imagenes-secundarias-variacion'] && variacion['imagenes-secundarias-variacion'].length > 0) {
                    for (let i = 0; i < variacion['imagenes-secundarias-variacion'].length; i++) {
                        formData.append(
                            `variaciones[${index}][imagenes-secundarias-variacion][${i}]`, 
                            variacion['imagenes-secundarias-variacion'][i]
                        );
                    }
                }
            });

            const response = await authStore.updateProductAction(data.id, formData);

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

    const onSubmitAddProduct = async (values) => {

        const formData = new FormData();

        // 1. Recorrer todos los valores validados y agregarlos al FormData
        Object.entries(values).forEach(([key, value]) => {
            if (key === 'novedad') {
                formData.append('novedad', value ? 1 : 0);
            } else if (key === 'oferta') {
                formData.append('oferta', value ? 1 : 0);
            } else if (key === 'is-simple') {
                formData.append('is-simple', value ? 1 : 0);
            } else if (key === 'imagenes-secundarias' || key === 'imagen-main') {
                // Los ficheros los leemos del ref directamente (más fiable que vee-validate)
            } else if (value !== undefined && value !== null) {
                formData.append(key, value);
            }
        });

        // 2. Leer los ficheros directamente desde el elemento del DOM via ref
        if (imagenesMainRef.value && imagenesMainRef.value.files && imagenesMainRef.value.files.length > 0) {
            Array.from(imagenesMainRef.value.files).forEach(file => {
                formData.append('imagenes-secundarias[]', file);
            });
        } else {
            console.warn('No se encontraron imágenes secundarias en el ref');
        }

        if (imagenMainRef.value && imagenMainRef.value.files && imagenMainRef.value.files.length > 0) {
            const imagenPrincipal = imagenMainRef.value.files[0];
            formData.append('imagen-main', imagenPrincipal);
        } else {
            console.warn('No se ha encontrado la imagen principal en el ref');
        }

        //PROCESAMIENTO DE VARIACIONES

        variations.value.forEach((variacion, index) => {

            //Recorremos los valores de la variacion sin tener en cuenta la estructura compleja de imagenes secundarias
            Object.entries(variacion).forEach(([key, value]) => {
                if (key === 'nombre' || key === 'stock') { 
                    formData.append(`variaciones[${index}][${key}]`, value)
                }
                else if (key !== 'imagenes-secundarias-variacion' || key !== 'imagen-main-variacion') formData.append(`variaciones[${index}][${key}]`, value.id);
            })

            if (variacion['imagen-main-variacion']) {
                formData.append(`variaciones[${index}][imagen-main-variacion]`, variacion['imagen-main-variacion'][0])
            }
            
            // Recorremos el FileList de las imágenes secundarias y las metemos con otro corchete []
            if (variacion['imagenes-secundarias-variacion'] && variacion['imagenes-secundarias-variacion'].length > 0) {
                for (let i = 0; i < variacion['imagenes-secundarias-variacion'].length; i++) {
                    formData.append(
                        `variaciones[${index}][imagenes-secundarias-variacion][${i}]`, 
                        variacion['imagenes-secundarias-variacion'][i]
                    );
                }
            }
        });

        const response = await authStore.addProductAction(formData);

        if (!response.success && response.info) {
            Object.entries(response.info).forEach(([field, messages]) => {
                setAddProductFieldError(field, messages[0]);
            });
        } else {
            emit('fetchProducts');
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
        variations.value.push(variation);
    }

    const onSubmitUpdateVariation = (variation) => {

        const index = variations.value.findIndex(v => v.id === variation.id);

        if (index !== -1) {
            variations.value[index] = {...variation};
            messageStore.addMessage({type: 'success', message: 'Variacion actualizada correctamente'})

        } else {
            messageStore.addMessage({type: 'error', message: 'No se ha podido actualizar la variacion'})
        }

        updateVariationFormActive.value = false;

    }
    


    //Metodos relacionados con logs de importacion
    const onSubmitGetLogs = async (page = 1) => {
        if (typeof page !== 'number') {
            page = 1;
        }

        const response = await authStore.getLogDataFromImportsProducts(page);
        console.log(response.data);
        

        if (response.success) {
            logsData.value = response.data;
            importProductsPopUpActive.value = false;
            showLogsPopUpActive.value = true;
        }
    }

    const handlePreviousLogsPage = async () => {
        if (logsData.value?.previous_page) {
            const response = await authStore.getLogDataFromImportsProducts(logsData.value.previous_page);

            if (response.success) {
                logsData.value = response.data;
            }
        }
    }

    const handleNextLogsPage = async () => {
        if (logsData.value?.next_page) {
            const response = await authStore.getLogDataFromImportsProducts(logsData.value.next_page);

            if (response.success) {
                logsData.value = response.data;
            }
        }
    }

    const cleanFormValue = (value) => {
        return (value.charAt(0).toUpperCase() + value.slice(1)).replace('_', ' ');
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
                        <th>Precio</th>
                        <th>Categoria</th>
                        <th>Deporte</th>
                        <th>Sexo</th>
                        <th>Ajuste</th>
                        <th>Stock</th>
                        <th>Acciones</th>
                    </tr>
                    
                    <tr v-for="(product, index) in products">
                        <td>{{ product.id }}</td>
                        <td>{{ product.nombre }}</td>
                        <td>{{ product.precio }}</td>
                        <td>{{ product.categoria }}</td>
                        <td>{{ product.deporte }}</td>
                        <td>{{ product.sexo }}</td>
                        <td>{{ product.ajuste }}</td>
                        <td>{{ product.stock }}</td>
                        <td class="actions">
                            <button @click="handleDeleteProduct(product)"><i class="bi bi-trash"></i></button>
                            <button @click="handleEditProduct(product)"><i class="bi bi-pencil"></i></button>
                        </td>
                    </tr>

                </table>
            </div>
        </div>

        <div v-if="updateProductPopUpActive" class="main-add-product-container">
            <div class="pop-up-edit-product-container">
                <div class="popup-header">
                    <h3>Editar Producto</h3>
                    <button class="close-btn" @click="updateProductPopUpActive = false">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <form @submit.prevent="() => { handleUpdateProductSubmit(onSubmitProductUpdate)(); }">
                
                    <div class="form-group">
                        <label>Nombre</label>
                        <Field type="text" name="nombre" v-model="productFormData.nombre" placeholder="Nombre del producto"></Field>
                        <ErrorMessage name="nombre" class="error-msg" />
                    </div>

                    <div class="form-group">
                        <label>Precio</label>
                        <Field type="number" min="0" step="0.01" name="precio" v-model="productFormData.precio" placeholder="Precio del producto"></Field>
                        <ErrorMessage name="precio" class="error-msg" />
                    </div>

                    <div class="form-group">
                        <label>Stock</label>
                        <Field type="number" min="0" name="stock" v-model="productFormData.stock" placeholder="Stock del producto"></Field>
                        <ErrorMessage name="stock" class="error-msg" />
                    </div>

                    <!-- Campo de Oferta -->
                    <div class="form-group">
                        <label for="oferta" class="checkbox-item-toggler">
                            <strong style="font-size: 20px;">Oferta </strong>
                            <input 
                                type="checkbox" 
                                id="oferta"
                                :checked="updateProductValues.oferta" 
                                @change="setUpdateProductFieldValue('oferta', $event.target.checked)"
                            />
                            <span class="custom-toggler"></span>
                        </label>
                    </div>

                    <!-- Campo de Novedad -->
                    <div class="form-group">
                        <label for="novedad" class="checkbox-item-toggler">
                            <strong style="font-size: 20px;">Novedad </strong>
                            <input 
                                type="checkbox" 
                                id="novedad"
                                :checked="updateProductValues.novedad" 
                                @change="setUpdateProductFieldValue('novedad', $event.target.checked)"
                            />
                            <span class="custom-toggler"></span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label for="isSimple" class="checkbox-item-toggler">
                            <strong style="font-size: 20px;">Producto simple </strong>
                            <input 
                                type="checkbox" 
                                id="isSimple"
                                :checked="updateProductValues.isSimple" 
                                @change="setUpdateProductFieldValue('isSimple', $event.target.checked)"
                            />
                            <span class="custom-toggler"></span>
                        </label>

                        <ErrorMessage name="isSimple" class="error-msg" />
                    </div>

                    <!-- <div class="form-group" v-if="values.oferta">
                        <label>Precio Anterior</label>
                        <Field type="text" name="precio-anterior" placeholder="Precio anterior"></Field>
                        <ErrorMessage name="nombre" class="error-msg" />
                    </div> -->

                    

                    <!-- <div class="form-group">
                        <label>Stock</label>
                        <Field type="number" name="stock" placeholder="Stock del producto"></Field>
                        <ErrorMessage name="stock" class="error-msg" />
                    </div> -->


                    <div class="form-grid">
                        <div>
                            <label>Categoria</label>

                            <div class="custom-select">
                                <div class="selected-option" @click="formDesplegables.categoria.isOpen = !formDesplegables.categoria.isOpen">
                                    <p> {{ cleanFormValue(updateProductValues.categoria  || 'Selecciona la categoria') }}</p> <i :class="['bi bi-arrow-down', {'bi-arrow-active': formDesplegables.categoria.isOpen}]"></i> 
                                </div>
                                
                                <div :class="['options-container', {'active': formDesplegables.categoria.isOpen}]">
                                    <ul class="options-list">
                                        <li @click="setUpdateProductFieldValue('categoria', 'zapatillas')" :class="{'option-active': updateProductValues.categoria === 'zapatillas'}">
                                            Zapatillas
                                        </li>

                                        <li @click="setUpdateProductFieldValue('categoria', 'camisetas')" :class="{'option-active': updateProductValues.categoria === 'camisetas'}">
                                            Camisetas
                                        </li>

                                        <li @click="setUpdateProductFieldValue('categoria', 'pantalones')" :class="{'option-active': updateProductValues.categoria === 'pantalones'}">
                                            Pantalones
                                        </li>
                                    </ul>
                                </div>
                            </div>


                            <ErrorMessage name="categoria" class="error-msg" />
                        </div>

                        <div>
                            <label>Marca</label>
                        
                            <div class="custom-select">
                                <div class="selected-option" @click="formDesplegables.marca.isOpen = !formDesplegables.marca.isOpen">
                                    <p> {{ cleanFormValue(updateProductValues.marca  || 'Selecciona la marca') }}</p> <i :class="['bi bi-arrow-down', {'bi-arrow-active': formDesplegables.marca.isOpen}]"></i> 
                                </div>
                                
                                <div :class="['options-container', {'active': formDesplegables.marca.isOpen}]">
                                    <ul class="options-list">
                                        <li @click="setUpdateProductFieldValue('marca', 'nike')" :class="{'option-active': updateProductValues.marca === 'nike'}">
                                            Nike
                                        </li>

                                        <li @click="setUpdateProductFieldValue('marca', 'adidas')" :class="{'option-active': updateProductValues.marca === 'adidas'}">
                                            Adidas
                                        </li>

                                        <li @click="setUpdateProductFieldValue('marca', 'asics')" :class="{'option-active': updateProductValues.marca === 'asics'}">
                                            Asics
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <ErrorMessage name="marca" class="error-msg" />
                        </div>
                    </div>

                    <div class="form-grid ">
                        
                        <div>
                            <label>Ajuste</label>
                        
                            <div class="custom-select">
                                <div class="selected-option" @click="formDesplegables.ajuste.isOpen = !formDesplegables.ajuste.isOpen">
                                    <p> {{ cleanFormValue(updateProductValues.ajuste  || 'Selecciona el ajuste') }}</p> <i :class="['bi bi-arrow-down', {'bi-arrow-active': formDesplegables.ajuste.isOpen}]"></i> 
                                </div>
                                
                                <div :class="['options-container', {'active': formDesplegables.ajuste.isOpen}]">
                                    <ul class="options-list">
                                        <li @click="setUpdateProductFieldValue('ajuste', 'ajustado')" :class="{'option-active': updateProductValues.ajuste === 'ajustado'}">
                                            Ajustado
                                        </li>

                                        <li @click="setUpdateProductFieldValue('ajuste', 'holgado')" :class="{'option-active': updateProductValues.ajuste === 'holgado'}">
                                            Holgado
                                        </li>

                                        <li @click="setUpdateProductFieldValue('ajuste', 'normal')" :class="{'option-active': updateProductValues.ajuste === 'normal'}">
                                            Normal
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <ErrorMessage name="ajuste" class="error-msg" />
                        </div>

                        <div>
                            <label>Altura</label>
                        
                            <div class="custom-select">
                                <div class="selected-option" @click="formDesplegables.altura.isOpen = !formDesplegables.altura.isOpen">
                                    <p> {{ cleanFormValue(updateProductValues.altura  || 'Selecciona la altura') }}</p> <i :class="['bi bi-arrow-down', {'bi-arrow-active': formDesplegables.altura.isOpen}]"></i> 
                                </div>
                                
                                <div :class="['options-container', {'active': formDesplegables.altura.isOpen}]">
                                    <ul class="options-list">
                                        <li @click="setUpdateProductFieldValue('altura', 'alto')" :class="{'option-active': updateProductValues.altura === 'alto'}">
                                            Alto
                                        </li>

                                        <li @click="setUpdateProductFieldValue('altura', 'bajo')" :class="{'option-active': updateProductValues.altura === 'bajo'}">
                                            Bajo
                                        </li>

                                        <li @click="setUpdateProductFieldValue('altura', 'normal')" :class="{'option-active': updateProductValues.altura === 'normal'}">  
                                            Normal
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <ErrorMessage name="altura" class="error-msg" />
                        </div>
                        
                    </div>

                    <div class="form-grid ">
                        <div>
                            <label>Sexo</label>
                        
                            <div class="custom-select">
                                <div class="selected-option" @click="formDesplegables.sexo.isOpen = !formDesplegables.sexo.isOpen">
                                    <p> {{ cleanFormValue(updateProductValues.sexo  || 'Selecciona el sexo') }}</p> <i :class="['bi bi-arrow-down', {'bi-arrow-active': formDesplegables.sexo.isOpen}]"></i> 
                                </div>
                                
                                <div :class="['options-container', {'active': formDesplegables.sexo.isOpen}]">
                                    <ul class="options-list">
                                        <li @click="setUpdateProductFieldValue('sexo', 'hombre')" :class="{'option-active': updateProductValues.sexo === 'hombre'}">
                                            Hombre
                                        </li>

                                        <li @click="setUpdateProductFieldValue('sexo', 'mujer')" :class="{'option-active': updateProductValues.sexo === 'mujer'}">
                                            Mujer
                                        </li>

                                        <li @click="setUpdateProductFieldValue('sexo', 'niño')" :class="{'option-active': updateProductValues.sexo === 'niño'}">
                                            Niño
                                        </li>

                                        <li @click="setUpdateProductFieldValue('sexo', 'niña')" :class="{'option-active': updateProductValues.sexo === 'niña'}">
                                            Niña
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <ErrorMessage name="sexo" class="error-msg" />
                        </div>

                        <div>
                            <label>Deporte</label>
                        
                            <div class="custom-select">
                                <div class="selected-option" @click="formDesplegables.deporte.isOpen = !formDesplegables.deporte.isOpen">
                                    <p> {{ cleanFormValue(updateProductValues.deporte  || 'Selecciona el deporte') }}</p> <i :class="['bi bi-arrow-down', {'bi-arrow-active': formDesplegables.deporte.isOpen}]"></i> 
                                </div>
                                
                                <div :class="['options-container', {'active': formDesplegables.deporte.isOpen}]">
                                    <ul class="options-list">

                                        <li @click="setUpdateProductFieldValue('deporte', 'general')" :class="{'option-active': updateProductValues.deporte === 'general'}">
                                            General
                                        </li>
                                        
                                        <li @click="setUpdateProductFieldValue('deporte', 'trail')" :class="{'option-active': updateProductValues.deporte === 'trail'}">
                                            Trail
                                        </li>

                                        <li @click="setUpdateProductFieldValue('deporte', 'futbol')" :class="{'option-active': updateProductValues.deporte === 'futbol'}">
                                            Futbol
                                        </li>

                                        <li @click="setUpdateProductFieldValue('deporte', 'tenis')" :class="{'option-active': updateProductValues.deporte === 'tenis'}">
                                            Tenis
                                        </li>

                                        <li @click="setUpdateProductFieldValue('deporte', 'padel')" :class="{'option-active': updateProductValues.deporte === 'padel'}">
                                            Padel
                                        </li>

                                        <li @click="setUpdateProductFieldValue('deporte', 'baloncesto')" :class="{'option-active': updateProductValues.deporte === 'baloncesto'}">
                                            Baloncesto
                                        </li>
                                        
                                    </ul>
                                </div>
                            </div>
                            <ErrorMessage name="deporte" class="error-msg" />
                        </div>
                    </div>
                    
                    <div class="form-group ">
                        <label>Descripcion</label>
                        <Field as="textarea" name="descripcion" class="textarea-fijo" placeholder="Descripcion del producto"></Field>
                        <ErrorMessage name="descripcion" class="error-msg" />
                    </div>

                    <div class="form-group">
                        <h2>Imagen principal</h2>
                        <Field name="imagen_main" v-slot="{ handleChange, handleBlur }">
                            <input 
                                ref="imagenMainRef"
                                type="file"  
                                @change="(e) => { 
                                    handleChange(e.target.files);
                                    handleAddMainImage(e);
                                }"
                                @blur="handleBlur"
                            />
                        </Field>
                        <ErrorMessage name="imagen_main" class="error-msg"></ErrorMessage>
                        <div v-if="fakeMainImgPreview" class="images-preview">
                            <div class="img-container">
                                <img :src="fakeMainImgPreview" @click="handleOpenImage">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <h2>Imagenes</h2>
                        <Field name="imagenes_secundarias" v-slot="{ handleChange, handleBlur }">
                            <input 
                                ref="imagenesMainRef"
                                type="file" 
                                multiple 
                                @change="(e) => { 
                                    handleChange(e.target.files);
                                    handleAddMainImage(e, true);
                                }"
                                @blur="handleBlur"
                            />
                        </Field>
                        <ErrorMessage name="imagenes_secundarias" class="error-msg"></ErrorMessage>
                        <div v-if="mainImageGallery" class="images-preview">
                            
                            <div v-for="img in mainImageGallery" class="img-container">
                                <img :src="generateFakeImgUrl(img)" @click="handleOpenImage">
                            </div>
                            
                        </div>
                    </div>

                    <div class="variations-section">
                        <h2>Variaciones</h2>

                        <button type="button" class="add-variation-btn" @click="handleToggleVariationForm()">
                            <i class="bi bi-plus"></i>
                        </button>

                        <div class="variations-container">

                            <template v-if="uniqueVariations.length > 0">
                                <div 
                                    v-for="variation in uniqueVariations" 
                                    :key="variation.id || JSON.stringify([variation.color, variation.talla])" 
                                    class="variation-container"
                                >
                                    <div class="variation-header">
                                        <img :src="getImgUrl(variation.img)" alt="img-variacion">
                                    </div>
                                    <div class="variation-body">
                                        <h3>{{ variation.nombre }}</h3>
                                        <p>Color: {{ variation.color?.nombre }}</p>
                                        <p>Talla: {{ variation.size?.nombre }}</p>
                                    </div> 
                                    <div class="variation-buttons">
                                        <button class="add-btn" @click="handleDeleteVariation(variation)">Eliminar</button>
                                        <button class="add-btn" @click="handleEditVariation(variation)">Editar</button>
                                    </div>
                                </div>
                            </template>
                            

                            <div v-else>
                                <p>Este producto aun no tiene variaciones</p>
                            </div>
                        </div>

                    </div>
                    

                    <div class="form-actions">
                        <button type="button" class="cancel-btn" @click="addProductPopUpActive = false">Cancelar</button>
                        <button type="submit" class="add-btn">Actualizar producto</button>
                    </div>

                </form>


            </div>
            <UpdateVariationModal 
                :isOpen="updateVariationFormActive"
                :metaData="props.metaData"
                :variationData="currentVariationToEdit"
                :productContext="{ sexo: productFormData?.sexo, categoria: productFormData?.categoria, marca: productFormData?.marca }"
                @close="updateVariationFormActive = false"
                @submit="onSubmitUpdateVariation"
            />
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

        <div v-if="addProductPopUpActive" class="main-add-product-container">
            <div class="pop-up-add-product-container">
                <div class="popup-header">
                    <h3>Añadir Producto</h3>
                    <button class="close-btn" @click="addProductPopUpActive = false">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <form @submit.prevent="() => { handleAddProductSubmit(onSubmitAddProduct)(); }">
                
                    <div class="form-group">
                        <label>Nombre</label>
                        <Field type="text" name="nombre" placeholder="Nombre del producto"></Field>
                        <ErrorMessage name="nombre" class="error-msg" />
                    </div>

                    <div class="form-group">
                        <label>Precio</label>
                        <Field type="number" min="0" step="0.01" name="precio" placeholder="Precio del producto"></Field>
                        <ErrorMessage name="precio" class="error-msg" />
                    </div>

                    <div class="form-group">
                        <label>Stock</label>
                        <Field type="number" min="0" name="stock" placeholder="Stock del producto"></Field>
                        <ErrorMessage name="stock" class="error-msg" />
                    </div>

                    <!-- Campo de Oferta -->
                    <div class="form-group">
                        <label for="oferta" class="checkbox-item-toggler">
                            <strong style="font-size: 20px;">Oferta </strong>
                            <input 
                                type="checkbox" 
                                id="oferta"
                                :checked="addProductValues.oferta" 
                                @change="setAddProductFieldValue('oferta', $event.target.checked)"
                            />
                            <span class="custom-toggler"></span>
                        </label>
                    </div>

                    <!-- Campo de Novedad -->
                    <div class="form-group">
                        <label for="novedad" class="checkbox-item-toggler">
                            <strong style="font-size: 20px;">Novedad </strong>
                            <input 
                                type="checkbox" 
                                id="novedad"
                                :checked="addProductValues.novedad" 
                                @change="setAddProductFieldValue('novedad', $event.target.checked)"
                            />
                            <span class="custom-toggler"></span>
                        </label>
                        
                    </div>

                    <div class="form-group">
                        <label for="is-simple" class="checkbox-item-toggler">
                            <strong style="font-size: 20px;">Producto simple </strong>
                            <!-- El Field solo sirve de envoltura para registrar el campo en Vee-Validate -->
                            
                            <input 
                                type="checkbox" 
                                id="is-simple"
                                :checked="addProductValues.isSimple" 
                                @change="setAddProductFieldValue('isSimple', $event.target.checked); mostrarErrorSimple = false"
                            />
                            <span class="custom-toggler"></span>
                        </label>
                        <ErrorMessage name="isSimple" class="error-msg" />
                    </div>

                    <!-- <div class="form-group" v-if="values.oferta">
                        <label>Precio Anterior</label>
                        <Field type="text" name="precio-anterior" placeholder="Precio anterior"></Field>
                        <ErrorMessage name="nombre" class="error-msg" />
                    </div> -->

                    

                    <!-- <div class="form-group">
                        <label>Stock</label>
                        <Field type="number" name="stock" placeholder="Stock del producto"></Field>
                        <ErrorMessage name="stock" class="error-msg" />
                    </div> -->


                    <div class="form-grid">
                        <div>
                            <label>Categoria</label>

                            <div class="custom-select">
                                <div class="selected-option" @click="formDesplegables.categoria.isOpen = !formDesplegables.categoria.isOpen">
                                    <p> {{ cleanFormValue(addProductValues.categoria  || 'Selecciona la categoria') }}</p> <i :class="['bi bi-arrow-down', {'bi-arrow-active': formDesplegables.categoria.isOpen}]"></i> 
                                </div>
                                
                                <div :class="['options-container', {'active': formDesplegables.categoria.isOpen}]">
                                    <ul class="options-list">
                                        <li @click="setAddProductFieldValue('categoria', 'zapatillas'); handleChangeProductSelect()" :class="{'option-active': addProductValues.categoria === 'zapatillas'}">
                                            Zapatillas
                                        </li>

                                        <li @click="setAddProductFieldValue('categoria', 'camisetas'); handleChangeProductSelect()" :class="{'option-active': addProductValues.categoria === 'camisetas'}">
                                            Camisetas
                                        </li>

                                        <li @click="setAddProductFieldValue('categoria', 'pantalones'); handleChangeProductSelect()" :class="{'option-active': addProductValues.categoria === 'pantalones'}">
                                            Pantalones
                                        </li>
                                    </ul>
                                </div>
                            </div>


                            <ErrorMessage name="categoria" class="error-msg" />
                        </div>

                        <div>
                            <label>Marca</label>
                        
                            <div class="custom-select">
                                <div class="selected-option" @click="formDesplegables.marca.isOpen = !formDesplegables.marca.isOpen">
                                    <p> {{ cleanFormValue(addProductValues.marca  || 'Selecciona la marca') }}</p> <i :class="['bi bi-arrow-down', {'bi-arrow-active': formDesplegables.marca.isOpen}]"></i> 
                                </div>
                                
                                <div :class="['options-container', {'active': formDesplegables.marca.isOpen}]">
                                    <ul class="options-list">
                                        <li @click="setAddProductFieldValue('marca', 'nike')" :class="{'option-active': addProductValues.marca === 'nike'}">
                                            Nike
                                        </li>

                                        <li @click="setAddProductFieldValue('marca', 'adidas')" :class="{'option-active': addProductValues.marca === 'adidas'}">
                                            Adidas
                                        </li>

                                        <li @click="setAddProductFieldValue('marca', 'asics')" :class="{'option-active': addProductValues.marca === 'asics'}">
                                            Asics
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <ErrorMessage name="marca" class="error-msg" />
                        </div>
                    </div>

                    <div class="form-grid ">
                        
                        <div>
                            <label>Ajuste</label>
                        
                            <div class="custom-select">
                                <div class="selected-option" @click="formDesplegables.ajuste.isOpen = !formDesplegables.ajuste.isOpen">
                                    <p> {{ cleanFormValue(addProductValues.ajuste  || 'Selecciona el ajuste') }}</p> <i :class="['bi bi-arrow-down', {'bi-arrow-active': formDesplegables.ajuste.isOpen}]"></i> 
                                </div>
                                
                                <div :class="['options-container', {'active': formDesplegables.ajuste.isOpen}]">
                                    <ul class="options-list">
                                        <li @click="setAddProductFieldValue('ajuste', 'ajustado')" :class="{'option-active': addProductValues.ajuste === 'ajustado'}">
                                            Ajustado
                                        </li>

                                        <li @click="setAddProductFieldValue('ajuste', 'holgado')" :class="{'option-active': addProductValues.ajuste === 'holgado'}">
                                            Holgado
                                        </li>

                                        <li @click="setAddProductFieldValue('ajuste', 'normal')" :class="{'option-active': addProductValues.ajuste === 'normal'}">
                                            Normal
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <ErrorMessage name="ajuste" class="error-msg" />
                        </div>

                        <div>
                            <label>Altura</label>
                        
                            <div class="custom-select">
                                <div class="selected-option" @click="formDesplegables.altura.isOpen = !formDesplegables.altura.isOpen">
                                    <p> {{ cleanFormValue(addProductValues.altura  || 'Selecciona la altura') }}</p> <i :class="['bi bi-arrow-down', {'bi-arrow-active': formDesplegables.altura.isOpen}]"></i> 
                                </div>
                                
                                <div :class="['options-container', {'active': formDesplegables.altura.isOpen}]">
                                    <ul class="options-list">
                                        <li @click="setAddProductFieldValue('altura', 'alto')" :class="{'option-active': addProductValues.altura === 'alto'}">
                                            Alto
                                        </li>

                                        <li @click="setAddProductFieldValue('altura', 'bajo')" :class="{'option-active': addProductValues.altura === 'bajo'}">
                                            Bajo
                                        </li>

                                        <li @click="setAddProductFieldValue('altura', 'normal')" :class="{'option-active': addProductValues.altura === 'normal'}">  
                                            Normal
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <ErrorMessage name="altura" class="error-msg" />
                        </div>
                        
                       

                        
                        
                    </div>

                    <div class="form-grid ">
                        <div>
                            <label>Sexo</label>
                        
                            <div class="custom-select">
                                <div class="selected-option" @click="formDesplegables.sexo.isOpen = !formDesplegables.sexo.isOpen">
                                    <p> {{ cleanFormValue(addProductValues.sexo  || 'Selecciona el sexo') }}</p> <i :class="['bi bi-arrow-down', {'bi-arrow-active': formDesplegables.sexo.isOpen}]"></i> 
                                </div>
                                
                                <div :class="['options-container', {'active': formDesplegables.sexo.isOpen}]">
                                    <ul class="options-list">
                                        <li @click="setAddProductFieldValue('sexo', 'hombre');  handleChangeProductSelect()" :class="{'option-active': addProductValues.sexo === 'hombre'}">
                                            Hombre
                                        </li>

                                        <li @click="setAddProductFieldValue('sexo', 'mujer'); handleChangeProductSelect()" :class="{'option-active': addProductValues.sexo === 'mujer'}">
                                            Mujer
                                        </li>

                                        <li @click="setAddProductFieldValue('sexo', 'niño'); handleChangeProductSelect()" :class="{'option-active': addProductValues.sexo === 'niño'}">
                                            Niño
                                        </li>

                                        <li @click="setAddProductFieldValue('sexo', 'niña'); handleChangeProductSelect()" :class="{'option-active': addProductValues.sexo === 'niña'}">
                                            Niña
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <ErrorMessage name="sexo" class="error-msg" />
                        </div>

                        <div>
                            <label>Deporte</label>
                        
                            <div class="custom-select">
                                <div class="selected-option" @click="formDesplegables.deporte.isOpen = !formDesplegables.deporte.isOpen">
                                    <p> {{ cleanFormValue(addProductValues.deporte  || 'Selecciona el deporte') }}</p> <i :class="['bi bi-arrow-down', {'bi-arrow-active': formDesplegables.deporte.isOpen}]"></i> 
                                </div>
                                
                                <div :class="['options-container', {'active': formDesplegables.deporte.isOpen}]">
                                    <ul class="options-list">

                                        <li @click="setAddProductFieldValue('deporte', 'general')" :class="{'option-active': addProductValues.deporte === 'general'}">
                                            General
                                        </li>
                                        
                                        <li @click="setAddProductFieldValue('deporte', 'trail')" :class="{'option-active': addProductValues.deporte === 'trail'}">
                                            Trail
                                        </li>

                                        <li @click="setAddProductFieldValue('deporte', 'futbol')" :class="{'option-active': addProductValues.deporte === 'futbol'}">
                                            Futbol
                                        </li>

                                        <li @click="setAddProductFieldValue('deporte', 'tenis')" :class="{'option-active': addProductValues.deporte === 'tenis'}">
                                            Tenis
                                        </li>

                                        <li @click="setAddProductFieldValue('deporte', 'padel')" :class="{'option-active': addProductValues.deporte === 'padel'}">
                                            Padel
                                        </li>

                                        <li @click="setAddProductFieldValue('deporte', 'baloncesto')" :class="{'option-active': addProductValues.deporte === 'baloncesto'}">
                                            Baloncesto
                                        </li>
                                        
                                    </ul>
                                </div>
                            </div>
                            <ErrorMessage name="deporte" class="error-msg" />
                        </div>
                    </div>
                    
                    <div class="form-group ">
                        <label>Descripcion</label>
                        <Field as="textarea" name="descripcion" class="textarea-fijo" placeholder="Descripcion del producto"></Field>
                        <ErrorMessage name="descripcion" class="error-msg" />
                    </div>

                    <div class="form-group">
                        <h2>Imagen principal</h2>
                        <Field name="imagen-main" v-slot="{ handleChange, handleBlur }">
                            <input 
                                ref="imagenMainRef"
                                type="file"  
                                @change="(e) => { 
                                    handleChange(e.target.files);
                                    handleAddMainImage(e);
                                }"
                                @blur="handleBlur"
                            />
                        </Field>
                        <ErrorMessage name="imagen-main" class="error-msg"></ErrorMessage>
                        <div v-if="fakeMainImgPreview" class="images-preview">
                            <div class="img-container">
                                <img :src="fakeMainImgPreview" @click="handleOpenImage">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <h2>Imagenes</h2>
                        <Field name="imagenes-secundarias" v-slot="{ handleChange, handleBlur }">
                            <input 
                                ref="imagenesMainRef"
                                type="file" 
                                multiple 
                                @change="(e) => { 
                                    handleChange(e.target.files);
                                    handleAddMainImage(e, true);
                                }"
                                @blur="handleBlur"
                            />
                        </Field>
                        <ErrorMessage name="imagenes-secundarias" class="error-msg"></ErrorMessage>
                        <div v-if="mainImageGallery" class="images-preview">
                            
                            <div v-for="img in mainImageGallery" class="img-container">
                                <img :src="generateFakeImgUrl(img)" @click="handleOpenImage">
                            </div>
                            
                        </div>
                    </div>

                    <div class="variations-section">
                        <h2>Variaciones</h2>

                        <button type="button" class="add-variation-btn" @click="handleToggleVariationForm()">
                            <i class="bi bi-plus"></i>
                        </button>

                        <div class="variations-container">

                            <template v-if="uniqueVariations.length > 0">
                                <div 
                                    v-for="variation in uniqueVariations" 
                                    :key="variation.id || JSON.stringify([variation.color, variation.talla])" 
                                    class="variation-container"
                                >
                                    <div class="variation-header">
                                        <img src="/img/Adidas/zapatillas/AdidasMasComprado.png" alt="img-variacion">
                                    </div>
                                    <div class="variation-body">
                                        <h3>{{ variation.nombre }}</h3>
                                        <p>Color: {{ variation.color?.nombre }}</p>
                                        <p>Talla: {{ variation.talla?.nombre }}</p>
                                    </div> 
                                </div>
                            </template>
                            

                            <div v-else>
                                <p>Este producto aun no tiene variaciones</p>
                            </div>
                        </div>

                    </div>
                    

                    <div class="form-actions">
                        <button type="button" class="cancel-btn" @click="addProductPopUpActive = false">Cancelar</button>
                        <button type="submit" class="add-btn">Añadir producto</button>
                    </div>

                </form>
            </div>
            <div v-if="addVariationFormActive" class="pop-up-add-variation-container" >
                <div class="popup-header">
                    <h3>Añadir Variacion</h3>
                    <button class="close-btn" @click="addVariationFormActive = false">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <Form :validation-schema="schemaAddVariation" :initial-values="variationInitialValues" @submit="onSubmitAddVariation" v-slot="{ values, setFieldValue, setFieldError }" >
                    
                    <div class="form-group">
                        <label>Nombre</label>
                        <Field type="text" name="nombre" placeholder="Nombre de variacion"></Field>
                        <ErrorMessage name="nombre" class="error-msg" />
                    </div>

                    <div class="form-group">
                        <label>Stock</label>
                        <Field type="number" min="0" name="stock" placeholder="Stock del producto"></Field>
                        <ErrorMessage name="stock" class="error-msg" />
                    </div>

                    <div class="form-grid">
                        <div>
                            <label>Color</label>
                                
                            <div class="custom-select">
                                <div class="selected-option" @click="formDesplegables.color.isOpen = !formDesplegables.color.isOpen">
                                    <p> {{ cleanFormValue(values.color?.nombre  || 'Selecciona el color') }}</p> <i :class="['bi bi-arrow-down', {'bi-arrow-active': formDesplegables.color.isOpen}]"></i> 
                                </div>
                                            
                                <div :class="['options-container', {'active': formDesplegables.color.isOpen}]">
                                    <ul class="options-list">
                                        <li v-for="color in metaData.variations.colors" @click="setFieldValue('color', color); formDesplegables.color.isOpen = false" :class="{'option-active': values.color?.nombre === color.nombre}">
                                            {{ color.nombre }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <ErrorMessage name="color" class="error-msg" />
                        </div>
                    </div>

                    <div class="form-grid">
                        <div>
                            <label>Categoria</label>
                                
                            <div class="custom-select">
                                <div class="selected-option" @click="formDesplegables.categoria_talla.isOpen = !formDesplegables.categoria_talla.isOpen">
                                    <p> {{ cleanFormValue(values.categoria_talla  || 'Selecciona la categoria de talla') }}</p> <i :class="['bi bi-arrow-down', {'bi-arrow-active': formDesplegables.categoria_talla.isOpen}]"></i> 
                                </div>
                                            
                                <div :class="['options-container', {'active': formDesplegables.categoria_talla.isOpen}]">
                                    <ul class="options-list">
                                        <li v-for="categoria in categorias" 
                                            @click="handleSelectVariationCategory(categoria); setFieldValue('categoria_talla', categoria); formDesplegables.categoria_talla.isOpen = false" 
                                            :class="{'option-active': values.categoria_talla === categoria, 'option-disabled': values.categoria_talla && values.categoria_talla !== categoria}">
                                            {{ categoria }}
                                        </li>
                                    </ul>                                  
                                </div>
                            </div>
                            <ErrorMessage name="categoria_talla" class="error-msg" />
                        </div>
                    </div>

                    <div class="form-grid">
                        <div>
                            <label>Tallas disponibles</label>
                                
                            <div class="custom-select">
                                <div class="selected-option" @click="formDesplegables.talla.isOpen = !formDesplegables.talla.isOpen">
                                    <p> {{ values.talla?.nombre  || 'Selecciona la talla' }}</p> <i :class="['bi bi-arrow-down', {'bi-arrow-active': formDesplegables.talla.isOpen}]"></i> 
                                </div>
                                            
                                <div :class="['options-container', {'active': formDesplegables.talla.isOpen}]">
                                    <ul class="options-list">
                                        <li v-for="talla in tallasDisponibles" @click="setFieldValue('talla', talla); formDesplegables.talla.isOpen = false" :class="{'option-active': values.talla?.nombre === talla.nombre}">
                                            {{ talla.nombre }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <ErrorMessage name="talla" class="error-msg" />
                        </div>
                    </div>

                    <div class="form-group">
                        <h2>Imagen principal</h2>
                        <Field name="imagen-main-variacion" v-slot="{ handleChange, handleBlur }">
                            <input 
                                type="file"  
                                @change="(e) => { 
                                    handleChange(e.target.files);
                                    handleAddVariationImage(e);
                                }"
                                @blur="handleBlur"
                            />
                        </Field>
                        <ErrorMessage name="imagen-main-variacion" class="error-msg"></ErrorMessage>
                        <div v-if="fakeVariationImgPreview" class="images-preview">
                            <div class="img-container">
                                <img :src="fakeVariationImgPreview">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <h2>Imagenes</h2>
                        <Field name="imagenes-secundarias-variacion" v-slot="{ handleChange, handleBlur }">
                            <input 
                                type="file" 
                                multiple 
                                @change="(e) => { 
                                    handleChange(e.target.files);
                                    handleAddVariationImage(e, true);
                                }"
                                @blur="handleBlur"
                            />
                        </Field>
                        <ErrorMessage name="imagenes-secundarias" class="error-msg"></ErrorMessage>
                        <div v-if="variationImageGallery" class="images-preview">
                            
                            <div v-for="img in variationImageGallery" class="img-container">
                                <img :src="generateFakeImgUrl(img)">
                            </div>
                            
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="cancel-btn" @click="addVariationFormActive = false">Cancelar</button>
                        <button type="submit" class="add-btn">Añadir variacion</button>
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

    <div v-if="imagePopUpActive" class="pop-up-container">
        <div class="buttons-container">
            <button class="close-btn" @click="currentImagePopUp = null; imagePopUpActive = false">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        
        <div class="pop-up-content">
            <img :src="currentImagePopUp" alt="image">
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

    .main-add-product-container {
        display: grid;
        grid-template-columns: auto auto;
        width: 70%;
        column-gap: 0px;
        align-items: start;
    }

    .pop-up-add-product-container, .pop-up-edit-product-container, .pop-up-add-variation-container {
        border-radius: 24px;
        background-color: #ffffff;
        padding: 30px;
        width: 90%;
        max-width: 500px;
        animation: slideRight 0.3s ease;
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

    @keyframes slideRight {
        from { transform: translateX(-50px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    .pop-up-container {
        background-color: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(5px);
        width: 100vw;
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        display: grid;
        grid-template-columns: 1fr;
        grid-template-rows: 50px auto;
        justify-items: center;
        align-items: center;
        z-index: 1000;
        animation: fadeIn 0.3s ease;
    }

    .buttons-container {
        width: 95%;
        height: auto;
        display: grid;
        justify-content: end;
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

    .pop-up-content {
        display: grid;
        animation: fadeIn;
        width: 80%;
        height: 80%;
    }

    .pop-up-content img {
        width: 100%;
        border-radius: 20px;
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

    .textarea-fijo {
        width: 400px;
        height: 200px;
        resize: none; /* Bloquea el redimensionado */
        box-sizing: border-box; /* Evita que el padding sume tamaño extra al ancho */
        border-radius: 8px;
        padding: 10px;
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

    .custom-select {
        margin: 15px 0;
        display: grid;
        grid-template-columns: 1fr;
        border: 2px solid #D72631;
        border-radius: 8px;
        padding: 12px 0px;

    }

    .selected-option {
        align-content: center;
        padding: 0 5px;
        display: grid;
        grid-template-columns: 1fr auto;
        align-items: center;
        cursor: pointer;
        border-bottom: 1px solid grey;
        width: 90%;
        justify-self: center;
        padding: 10px 0;
    }

    .selected-option p{
        margin: 0;
        -webkit-user-select: none; 
        -ms-user-select: none;    
        user-select: none;
    }


    .options-container {
        display: grid;
        grid-template-rows: 0fr;
        transition: grid-template-rows 0.3s ease-in-out, visibility 0.3s, margin-top 0.3s; /* Añadimos visibility a la transición */
        overflow: hidden;
        visibility: hidden; /* Oculto por defecto */
    }

    .options-container.active {
        grid-template-rows: 1fr;
        margin-top: 10px;
        margin-bottom: 5px;
        visibility: visible; /* Visible cuando está activo */
    }

    .options-list {
        min-height: 0;
        list-style-type: none;
        display: grid;
        grid-auto-flow: row;
        row-gap: 5px; /* Reducimos un poco el gap para mayor consistencia */
        margin: 0;
        padding-right: 5px;
    }

    .options-list li {
        padding: 20px;
        cursor: pointer;
        min-height: 0;
        transition: all 0.3s ease-in-out;
    }

    .option-active {
        background-color: #1F1F1F;
        color: white;
    }

    .option-disabled {
        pointer-events: none;
        opacity: 0.6;
    }

    .bi-arrow-down {
        transition: all 0.2s ease-in-out;
        display: inline-block;
        width: auto;
    }

    .bi-arrow-active {
        transform: rotate(180deg)
    }

    .images-preview {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        grid-template-rows: auto;
        gap: 20px;
    }

    .images-preview .img-container img{
        width: 100%;
        animation: fadeIn 0.3s ease;
    }

    .images-preview .img-container{
        transition: all 0.3s ease-in-out;
        cursor: pointer;
    }

    .images-preview .img-container:hover{
        transform: scale(1.1);
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
        height: 100px;
        width: 100px;
    }

    .add-variation-btn i::before{
        font-size: 50px;
    }

    .add-variation-btn:hover {
        transform: translateY(-2px);
        background-color: #b1b1b1;
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


    /* Sections */
    .variations-section {
        display: grid;
    }

    .variations-container {
        margin-top: 30px;
        display: grid;
        grid-auto-flow: column;
        overflow: auto;
        gap: 20px;
        padding: 20px;

    }

    .variations-container .variation-container {
        display: grid;
        grid-template-rows: auto auto;
        border-radius: 10px;
        box-shadow: 0px 0px 200px #bebebe;
        z-index: 10;
        width: 200px;
        padding: 20px;
    }

    .variations-container .variation-container .variation-header{
        text-align: center;
    }

    .variations-container .variation-container .variation-header img{
        width: 100px; 
    }

    .variations-container .variation-container .variation-body{
        font-size: 15px;
    }

    .variations-container .variation-container .variation-buttons {
        display:grid;
        grid-template-columns: 1fr 1fr;
        gap: 5px;
        grid-template-rows: auto;
    }

    .variations-container .variation-container .variation-body h3{
        font-size: 20px;
    }


    
    
    
</style>