<script setup>
    import { useAuthStore } from '../stores/authStore';
    import { Form, Field, ErrorMessage } from 'vee-validate';
    import * as yup from 'yup';

    const authStore = useAuthStore();
    
    // 1. Usamos una constante reactiva o directamente el store
    // Importante: El nombre de las llaves debe coincidir con el 'name' de los <Field>
    const initialData = {
        nombre: authStore.user?.name || '',
        email: authStore.user?.email || '',
    };

    const schema = yup.object({
        nombre: yup.string().required('El nombre es obligatorio'),
        email: yup.string().email('Email no válido').required('El email es obligatorio'),
        password: yup.string().min(6, 'Mínimo 6 caracteres').required('Contraseña requerida'),
    });

    const onSubmit = (values) => {
        console.log('Datos finales (mezclados con los iniciales):', values);
    };
</script>

<template>
    <Form v-if="authStore.user" :validation-schema="schema" :initial-values="initialData" @submit="onSubmit">
        <div>
            <label>Nombre:</label>
            <Field name="nombre" type="text" placeholder="Tu nombre"/>
            <ErrorMessage name="nombre" class="error-msg" />
        </div>

        <div>
            <label>Nombre:</label>
            <Field name="nombre_usuario" type="text" placeholder="Tu nombre"/>
            <ErrorMessage name="nombre_usuario" class="error-msg" />
        </div>

        <div>
            <label>Email:</label>
            <Field name="email" type="email" placeholder="tu@email.com"/>
            <ErrorMessage name="email" class="error-msg" />
        </div>


        <button type="submit">Actualizar Perfil</button>
    </Form>
</template>

