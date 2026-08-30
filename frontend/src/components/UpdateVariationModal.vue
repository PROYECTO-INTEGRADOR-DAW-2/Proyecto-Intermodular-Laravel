<template>
  <!-- El modal solo se renderiza si está abierto -->
  <div v-if="isOpen" class="pop-up-add-variation-container">
    <div class="popup-header">
        <h3>Editar Variación</h3>
        <button class="close-btn" @click="closeModal">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    
    <form @submit.prevent="onSubmit">
        <div class="form-group">
            <label>Nombre</label>
            <Field type="text" name="nombre" placeholder="Nombre de variación" />
            <ErrorMessage name="nombre" class="error-msg" />
        </div>

        <div class="form-group">
            <label>Stock</label>
            <Field type="number" min="0" name="stock" placeholder="Stock del producto" />
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
                        <p> {{ cleanFormValue(values.categoria_talla  || 'Selecciona la categoria de talla') }}</p> 
                        <i :class="['bi bi-arrow-down', {'bi-arrow-active': formDesplegables.categoria_talla.isOpen}]"></i> 
                    </div>
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

        <div class="form-grid">
            <div>
                <label>Tallas disponibles</label>
                                
                <div class="custom-select">
                    <div class="selected-option" @click="formDesplegables.talla.isOpen = !formDesplegables.talla.isOpen">
                        <p> {{ values.size?.nombre  || 'Selecciona la talla' }}</p> 
                        <i :class="['bi bi-arrow-down', {'bi-arrow-active': formDesplegables.talla.isOpen}]"></i> 
                    </div>
                                            
                    <div :class="['options-container', {'active': formDesplegables.talla.isOpen}]">
                        <ul class="options-list">
                            <li v-for="talla in tallasDisponibles" 
                            @click="setFieldValue('size', talla); formDesplegables.talla.isOpen = false" 
                            :class="{'option-active': values.size?.nombre === talla.nombre}">
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
                    ref="imagenMainVariationRef"
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
                    <img :src="fakeVariationImgPreview" @click="handleOpenImage">
                </div>
            </div>
            <button class="add-btn" type="button" @click="handleDeleteVariationImg()">Eliminar imagen</button>

        </div>

        <div class="form-group">
            <h2>Imagenes</h2>
            <Field name="imagenes-secundarias-variacion" v-slot="{ handleChange, handleBlur }">
                <input
                    ref="imagenesVariationRef"
                    type="file" 
                    multiple 
                    @change="(e) => { 
                        handleChange(e.target.files);
                        handleAddVariationImage(e, true);
                    }"
                    @blur="handleBlur"
                />
            </Field>
            <ErrorMessage name="imagenes-secundarias-variacion" class="error-msg"></ErrorMessage>
            <div v-if="variationImageGallery" class="images-preview">          
                <div v-for="img in variationImageGallery" class="img-container">
                    <img :src="generateFakeImgUrl(img)" @click="handleOpenImage">
                </div>         
            </div>
            <button class="add-btn" type="button" @click="handleDeleteVariationGallery()">Eliminar imagen</button>

        </div>        

        <!-- Aquí metes el resto de tus campos (Color, Tallas, etc.) usando los <Field> normales -->

        <div class="form-actions">
            <button type="button" class="cancel-btn" @click="closeModal">Cancelar</button>
            <button type="submit" class="add-btn">Actualizar variación</button>
        </div>
    </form>

    
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

<script setup>
    import { defineProps, defineEmits, watch, ref, computed } from 'vue';
    import { Form, Field, ErrorMessage, useForm } from 'vee-validate';
    import * as yup from 'yup';
    import { useAuthStore } from '../stores/authStore';

    const tallasDisponibles = ref([]);
    const authStore = useAuthStore();

    const imagenMainVariationRef = ref(null);
    const imagenesVariationRef = ref(null);

    const variationImageFile = ref(null);
    const variationImageGallery = ref(null);

    const currentImagePopUp = ref(null);
    const imagePopUpActive = ref(false);

    const categorias = ref(["Zapatillas Infantil", "Prendas Infantil", "Zapatillas Adulto", "Prendas Adulto"]);


    const formDesplegables = ref({
        color: {isOpen: false},
        talla: {isOpen: false},
        categoria_talla: {isOpen: false}
    })

    // 1. Recibimos los datos del padre
    const props = defineProps({
        isOpen: Boolean,
        variationData: Object, // Pasamos la variación seleccionada
        productContext: Object, // Pasamos sexo y categoria del padre si los necesitas para las tallas
        metaData: Object
    });

    const emit = defineEmits(['close', 'submit']);

    // 2. Definimos el esquema de Yup exclusivo para la variación
    const schema = yup.object().shape({
        nombre: yup.string().required("Debes de añadir un nombre a la variacion"),
        color: yup.mixed().required("Debes de seleccionar el color de la variacion"),
        size: yup.mixed().required("Debes de seleccionar la talla de la variacion"),
        stock: yup.number().required("Debes de introduci rel stock de la variacion"),
        'imagenes-secundarias-variscion': yup.mixed().nullable().notRequired(),
        'imagen-main-variacion': yup.mixed().nullable().notRequired()
    });

    // 3. Inicializamos Vee-Validate TOTALMENTE AISLADO
    const { resetForm, setFieldValue, setFieldError, values, handleSubmit } = useForm({
        validationSchema: schema,
        enableReinitialize: true
    });

    // 4. Escuchamos cuándo cambia la variación seleccionada para cargar sus datos limpios
    watch(() => props.variationData, async (newVariation) => {
        if (newVariation) {

            // 1. Reseteamos Vee-Validate
            resetForm({ values: { ...newVariation } });
            setFieldValue('imagen-main-variacion', null);
            setFieldValue('imagenes-secundarias-variacion', null);

            // 2. Limpiamos el valor HTML nativo de los inputs file
            if (imagenMainVariationRef.value) imagenMainVariationRef.value.value = '';
            if (imagenesVariationRef.value) imagenesVariationRef.value.value = '';

            // 3. Limpiamos las variables de previsualización local
            variationImageFile.value = null;
            variationImageGallery.value = null;

            const mainImage = newVariation['imagen-main-variacion'];
            if (mainImage instanceof FileList && mainImage.length > 0) {
                const file = mainImage[0];

                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                const fileList = dataTransfer.files;

                setFieldValue('imagen-main-variacion', fileList);

                // Corregido el tipo a HTMLInputElement y acceso a .value.files
                if (imagenMainVariationRef.value instanceof HTMLInputElement) imagenMainVariationRef.value.files = fileList;
                
            }

            const imageGallery = newVariation['imagenes-secundarias-variacion'];
            if (imageGallery instanceof FileList && imageGallery.length > 0) {
                //Tenemos el filelist tenemos que asignarselo a el ref si el ref es una instancia de HTMLInputElement
                setFieldValue('imagenes-secundarias-variacion', imageGallery);
                if (imagenesVariationRef.value instanceof HTMLInputElement) imagenesVariationRef.value.files = imageGallery;
            } 

            
        
            const productBrand = props.productContext?.marca?.charAt(0).toUpperCase() + props.productContext?.marca?.slice(1);
            const productCategory = props.productContext?.categoria;
            const variationImg = newVariation.img !== null && newVariation.img !== "null" ? newVariation.img : null;

            // 3. Procesamos las imágenes en segundo plano (esto no debería tocar los textos del padre)
            if (productBrand && productCategory && variationImg) {
                const finalUrl = `/api/productos/imagen/${productBrand}/${productCategory}/${variationImg}`;
                try {
                    const respuesta = await fetch(finalUrl);
                    const blob = await respuesta.blob();
                    const file = new File([blob], variationImg, { type: blob.type });

                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    const fileList = dataTransfer.files;

                    if (imagenMainVariationRef.value) {
                        imagenMainVariationRef.value.files = fileList;
                        setFieldValue('imagen-main-variacion', fileList);
                        //imagenMainVariationRef.value.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                } catch(e) {
                    console.log("No se pudo procesar la imagen principal de la variacion");
                }
            }
            
            if (productBrand && productCategory && newVariation.img_gallery && newVariation.img_gallery.length > 0) {
                const dataTransferGaleria = new DataTransfer();

                for (const img of newVariation.img_gallery) {
                    const imgName = img.nombre;
                    const finalUrlGaleria = `/api/productos/imagen/${productBrand}/${productCategory}/${imgName}`;
                    try {
                        const respuestaGaleria = await fetch(finalUrlGaleria);
                        const blobGaleria = await respuestaGaleria.blob();
                        const fileGaleria = new File([blobGaleria], imgName, { type: blobGaleria.type });
                        dataTransferGaleria.items.add(fileGaleria);
                    } catch(e) {
                        console.log(`No se pudo procesar la imagen de la galeria: ${imgName}`);
                    }
                }

                const fileListGaleria = dataTransferGaleria.files;
                if (imagenesVariationRef.value) {
                    imagenesVariationRef.value.files = fileListGaleria;
                    setFieldValue('imagenes-secundarias-variacion', fileListGaleria);
                    //imagenesVariationRef.value.dispatchEvent(new Event('change', { bubbles: true }));
                }
            }

            

            if (imagenMainVariationRef.value instanceof HTMLInputElement) imagenMainVariationRef.value.dispatchEvent(new Event('change', { bubbles: true }));

            if (imagenesVariationRef.value instanceof HTMLInputElement) imagenesVariationRef.value.dispatchEvent(new Event('change', {bubbles: true}));


            // 4. Cargamos la categoría de talla de forma totalmente aislada usando setUpdateVariationFieldValue
            if (props.productContext?.sexo && props.productContext?.categoria) {
                const category = await determineSizeCategory(props.productContext.sexo, props.productContext.categoria, true);
                
                if (category) {
                    setFieldValue('categoria_talla', category);
                }

            }

    }
    }, { immediate: true, deep: true, flush: 'post'});

    const closeModal = () => {
        emit('close');
    };

    const onSubmit = handleSubmit((formValues) => {
        emit('submit', formValues);
    });

    const handleSelectVariationCategory = async (category, editing = false) => {

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
        
        cleanedGenderValue = props.productContext?.sexo.charAt(0).toUpperCase() + props.productContext?.sexo.slice(1);
        cleanedCategoryProductValue = props.productContext?.categoria.charAt(0).toUpperCase() + props.productContext?.categoria.slice(1);
        

        const response = await authStore.getSizesAction(category, cleanedGenderValue, cleanedCategoryProductValue);

        if (response.success) {
                tallasDisponibles.value = response.data;
                return true;
        }
        else return false;

    }

    const determineSizeCategory = async (sexo, categoriaProducto) => {
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

            if (gallery) variationImageGallery.value = null;
            else variationImageFile.value = null;
            console.log("No se ha subido ninguna imagen");  
        }
    }

    const cleanFormValue = (value) => {
        return (value.charAt(0).toUpperCase() + value.slice(1)).replace('_', ' ');
    }

    const fakeVariationImgPreview = computed( () => {

        if (!variationImageFile.value) return '';

        // Como mainImageFile es un archivo real, creamos la URL directo
        return URL.createObjectURL(variationImageFile.value);

    })

    const generateFakeImgUrl = (img) => {
        if (!img) {
            return;
        }

        const fakeUrl = URL.createObjectURL(img);

        return fakeUrl;
    }

    const handleOpenImage = (event) => {
        if (!event) {
            return;
        }

        currentImagePopUp.value = event.target.src;

        imagePopUpActive.value = true;

    }
    
    const handleDeleteVariationGallery = () => {
        if (imagenesVariationRef.value.files) {
            setFieldValue('imagenes-secundarias-variacion', null);
            imagenesVariationRef.value.value = '';
            variationImageGallery.value = null;
        }

        imagenesVariationRef.value.dispatchEvent(new Event('change', {bubbles: true}));
    }

    const handleDeleteVariationImg = () => {
        if (imagenMainVariationRef.value.files) {
            setFieldValue('imagen-main-variacion', null);
            imagenMainVariationRef.value.value = '';
            variationImageFile.value = null;
        }

        imagenMainVariationRef.value.dispatchEvent(new Event('change', {bubbles: true}));
    }
</script>

<style scoped>
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
        row-gap: 10px;
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

</style>