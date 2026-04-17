<script setup>
    import { useCartStore } from '../stores/cartStore';
    import { ref, onMounted, computed} from 'vue';
    import { Field, Form, ErrorMessage } from 'vee-validate';
    import * as yup from 'yup'
    //import places from 'places.js';

    const checkoutData = ref({
        customer: {
            name: "",
            email: "",
            phone: ""
        },
        shipping_address: {
            street: "",
            number: 0,
            piso: "",
            door: "",
            city: "",
            province: "",
            zip_code: 0,
            country: ""
        },
        payment_method: "",
        shipping_method: ""
    });
    const cartStore = useCartStore();

    const cart = computed(() => cartStore.items)
    let currentStep = ref(1);
    const formMethods = {
        metodo_envio: {
            isOpen: false
        },
        metodo_pago: {
            isOpen: false
        }
    }



    const contactDataSchema = yup.object({
        nombre: yup.string().required('El nombre es obligatorio'),
        apellidos: yup.string().max(255,'Maximo 255 caracteres'),
        telefono: yup.string().required().matches(/^[0-9+]{9,15}$/,'El número no es válido (debe tener entre 9 y 15 dígitos)')
    });

    const shippingDataSchema = yup.object({
        calle: yup.string().required('La calle es obligatoria'),
        numero: yup.string().required('El numero de edificio es obligatorio'),
        piso: yup.string().required('El piso es obligatorio'),
        puerta: yup.string().required('El numero de puerta es obligatorio'),
        ciudad: yup.string().required('La ciudad es obligatoria'),
        codigo_postal: yup.string().required('El codigo postal es obligatorio').matches(/^[0-9]{5}$/, 'El código postal debe tener exactamente 5 dígitos'),
        provincia: yup.string().required('La provincia es obligatoria'),
        pais: yup.string().required('El pais es obligatorio')
    });

    const purchaseTypeDataSchema = yup.object({
        nombre: yup.string().required('El nombre es obligatorio'),
        apellidos: yup.string().max(255,'Maximo 255 caracteres'),
        nombre_usuario: yup.string().required('El nombre de usuario es obligatorio'),
        email: yup.string().email('Email no válido').required('El email es obligatorio'),
    });

    //onMounted(() => {
        /*
        const placesAutocomplete = places({
            appId: 'LWBBIBKB29', 
            apiKey: 'fbbc093854a1a6afb53a41149bf85a48',
            container: document.querySelector('#address-input'),
            templates: {
                value: (suggestion) => suggestion.name 
            }
        });

       
        placesAutocomplete.on('change', (e) => {
            console.log('Datos completos:', e.suggestion);
            
            // Aquí tienes todo verificado:
            const cp = e.suggestion.postcode;
            const ciudad = e.suggestion.city;
            const provincia = e.suggestion.county;
            
           
        });*/
    //});

    const handleNextStep = (values, actions) => {
        console.log(values)
        switch (currentStep.value) {
            case 1:
                checkoutData.customer = {...values}
                currentStep.value++;
                break;
            case 2: 
                checkoutData.shipping_address = {...values}
                currentStep.value++;
                break;
            case 3:
                checkoutData.payment_method = values.payment_method
                checkoutData.shipping_method = values.shipping_method
                break;
        
            default:
                break;
        }
        console.log(currentStep)

        
    } 
    
    


</script>




<template>
    <div class="main-container">
        <Form v-if="cart.length && currentStep === 1" :validation-schema="contactDataSchema" @submit="handleNextStep">
            <legend>Datos de contacto</legend>
            <div class="form-group">
                <label>Nombre:</label>
                <Field name="nombre" type="text" placeholder="Tu nombre"/>
                <ErrorMessage name="nombre" class="error-msg" />
            </div>

            <div class="form-group">
                <label>Apellidos:</label>
                <Field name="apellidos" type="text" placeholder="Tus apellidos"/>
                <ErrorMessage name="apellidos" class="error-msg" />
            </div>

            <div class="form-group">
                <label>Teléfono:</label>
                <Field name="telefono" type="tel" placeholder="Tu telefono"/>
                <ErrorMessage name="telefono" class="error-msg" />
            </div>

            <div class="form-groud">
                <input type="submit" value="Siguiente paso" class="button">
            </div>
        </Form>

        <Form v-if="cart.length && currentStep === 2" :validation-schema="shippingDataSchema" @submit="handleNextStep">
            <legend>Direccion de envio</legend>

            <div>
                <label>Busca tu dirección:</label>
                <input type="search" id="address-input" placeholder="Empieza a escribir tu calle..." />
            </div>

            <div class="inline-form">
                <div class="form-group">
                    <label>Calle:</label>
                    <Field name="calle" type="text" placeholder="Tu calle"/>
                    <ErrorMessage name="calle" class="error-msg" />
                </div>
                <div class="form-group">
                    <label>Numero:</label>
                    <Field name="numero" type="number"/>
                    <ErrorMessage name="numero" class="error-msg" />
                </div>
                <div class="form-group">
                    <label>Piso:</label>
                    <Field name="piso" type="text" placeholder="Piso"/>
                    <ErrorMessage name="piso" class="error-msg" />
                </div>
                <div class="form-group">
                    <label>Puerta:</label>
                    <Field name="puerta" type="text" placeholder="Puerta"/>
                    <ErrorMessage name="puerta" class="error-msg" />
                </div>
            </div>
            
            <div class="form-group">
                <label>Ciudad:</label>
                <Field name="ciudad" type="text" placeholder="Tu ciudad"/>
                <ErrorMessage name="ciudad" class="error-msg" />
            </div>

            <div class="form-group">
                <label>Codigo postal:</label>
                <Field name="codigo_postal" type="text" placeholder="Codigo postal de tu ciudad"/>
                <ErrorMessage name="codigo_postal" class="error-msg" />
            </div>

            <div class="form-group">
                <label>Provincia:</label>
                <Field name="provincia" type="text" placeholder="Tu provincia"/>
                <ErrorMessage name="provincia" class="error-msg" />
            </div>

            <div class="form-group">
                <label>Pais:</label>
                <Field name="pais" type="text" placeholder="Tu pais"/>
                <ErrorMessage name="pais" class="error-msg" />
            </div>

            <div class="form-group">
                <input type="submit" value="Siguiente paso" class="button"></input>
            </div>


        </Form>

        <Form v-if="cart.length && currentStep === 3" :validation-schema="purchaseTypeDataSchema" @submit="handleNextStep" v-slot="{ values, setFieldValue }">
            <legend>Metodo de envio y pago</legend>

            <div class="custom-select">
                <div class="selected-option" @click="formMethods.metodo_envio.isOpen = !isOpen">
                    {{ values.metodo_envio  || 'Selecciona tu talla'}}
                </div>
                
                <ul v-if="formMethods.metodo_envio.isOpen" class="options-list">
                    <li @click="setFieldValue('metodo_envio', 'estandar')" :class="{'option-active': values.metodo_envio === 'estandar'}">
                        Envío Estándar (3-5 días)
                    </li>

                    <li @click="setFieldValue('metodo_envio', 'express')" :class="{'option-active': values.metodo_envio === 'express'}">
                        Envío Express (24h)
                    </li>

                    <li @click="setFieldValue('metodo_envio', 'punto_recogida')" :class="{'option-active': values.metodo_envio === 'punto_recogida'}">
                        Punto de recogida
                    </li>
                </ul>
            </div>

            <div class="custom-select">
                <div class="selected-option" @click="formMethods.metodo_pago.isOpen = !isOpen">
                    {{ values.metodo_envio  || 'Selecciona tu talla'}}
                </div>
                
                <ul v-if="formMethods.metodo_pago.isOpen" class="options-list">
                    <li @click="setFieldValue('metodo_pago', 'paypal')" :class="{'option-active': values.metodo_envio === 'paypal'}">
                        Envío Estándar (3-5 días)
                    </li>

                    <li @click="setFieldValue('metodo_pago', 'bizum')" :class="{'option-active': values.metodo_envio === 'bizum'}">
                        Envío Express (24h)
                    </li>

                    <li @click="setFieldValue('metodo_pago', 'tarjeta')" :class="{'option-active': values.metodo_envio === 'tarjeta'}">
                        Punto de recogida
                    </li>
                </ul>
            </div>
           
            <div class="form-group">
                <input type="submit" value="Siguiente paso" class="button"></input>
            </div>    
        </Form>
    </div>


</template>


<style scoped>

.main-container {
    display: grid;
    grid-template-columns: 1fr;
    grid-template-rows: 1fr;
    width: 100%;
    padding: 20px;
}

.form-group input[type="text"], input[type="password"], input[type="email"] {
        height: 50px;
    }
    

.form-group {
    display: grid;
    margin: 20px 0 20px 0; 
}





</style>