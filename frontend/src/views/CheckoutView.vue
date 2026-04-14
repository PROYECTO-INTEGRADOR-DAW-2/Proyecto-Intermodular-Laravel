<script setup>
    import { useCartStore } from '../stores/cartStore';
    import { ref, onMounted} from 'vue';
    import { Field, Form, ErrorMessage } from 'vee-validate';
    import * as yup from 'yup'

    const checkoutData = ref(null);
    const useCartStore = useCartStore();

    const cart = computed(() => useCartStore.items)

    const handlePurchase = () => {

    }

    const checkoutDataSchema = yup.object({
        nombre: yup.string().required('El nombre es obligatorio'),
        apellidos: yup.string().max(255,'Maximo 255 caracteres'),
        nombre_usuario: yup.string().required('El nombre de usuario es obligatorio'),
        email: yup.string().email('Email no válido').required('El email es obligatorio'),
    });
    


</script>


<template>
    <div class="main-container">
        <Form v-if="cart.length" :validation-schema="checkoutDataSchema" @submit="handlePurchase">
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

            <legend>Direccion de envio</legend>

            <div class="inline-form">
                <div class="form-group">
                    <label>Calle:</label>
                    <Field name="nombre" type="text" placeholder="Tu nombre"/>
                    <ErrorMessage name="nombre" class="error-msg" />
                </div>
                <div class="form-group">
                    <label>Numero:</label>
                    <Field name="nombre" type="number" placeholder="Tu nombre"/>
                    <ErrorMessage name="nombre" class="error-msg" />
                </div>
                <div class="form-group">
                    <label>Piso y puerta:</label>
                    <Field name="piso-puerta" type="text" placeholder="Piso/puerta"/>
                    <ErrorMessage name="piso-puerta" class="error-msg" />
                </div>
            </div>
            
            <div class="form-group">
                <label>Nombre:</label>
                <Field name="nombre" type="text" placeholder="Tu nombre"/>
                <ErrorMessage name="nombre" class="error-msg" />
            </div>
            

            

        </Form>
    </div>


</template>


<style scoped>

</style>