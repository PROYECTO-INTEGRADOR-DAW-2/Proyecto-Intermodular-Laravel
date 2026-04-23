<script setup>
    import { defineProps, onMounted } from 'vue'

    const props = defineProps({
        users: Object
    })

    const updateUserPopUpActive = ref(false);
    const userFormData = ref({});
    const loadingUsers = ref(false);

    const schemaUpdateUser = yup.object({
        nombre: yup.string().required('El nombre de usuario es obligatorio'),
        apellidos: yup.string().required('Los apellidos del usuarios son obligatorios'),
        nombre_usuario: yup.string().required('El nombre de usuario es obligatorio'),
        email: yup.string().email("Debes introducir un email valido").required("Email obligatorio"),
        rol: yup.string().required('Debes de seleccionar un rol')
    })

    const handleEditUser = (user) => {
        updateUserPopUpActive.value = true;
        userFormData.value = {...user}
    }

    const onSubmitUserUpdate = async (data, { setFieldError }) => {

        if (data?.id) {
            const response = await authStore.updateUserAction(data.id, data);

            if (!response.success && response.info) {
                Object.entries(response.info).forEach(([field, messages]) => {
                    setFieldError(field, messages[0]);
                })
            } else {
                const fetchedUsers = await authStore.getUsersAction()
                loadingUsers.value = true;

                if (fetchedUsers.success) {
                    users.value = fetchedUsers.data
                    fetchedSections.users = true;
                    loadingUsers.value = false;

                    userFormData.value = {};
                    updateUserPopUpActive.value = false;
                }
            }

        } else {
            messageStore.addMessage('error', 'No se ha proporcionado del id del usuario');
        }

       
    }

    


</script>

<template>
        <div class="cart-table-wrapper">
            <div v-if="currentTab === 'users' && users.length">
                <div class="updateTableButton"></div>
                <table class="users-table">
                    
                    <tr>
                        <th>Id</th>
                        <th>Nombre</th>
                        <th>Apellidos</th>
                        <th>Nombre usuario</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                    
                    
                    <tr v-for="(user, index) in users">
                        <td>{{ user.id }}</td>
                        <td>{{ user.nombre }}</td>
                        <td>{{ user.apellidos }}</td>
                        <td>{{ user.nombre_usuario }}</td>
                        <td>{{ user.email }}</td>
                        <td>{{ user.rol }}</td>
                        <td class="actions">
                            <button><i class="bi bi-trash"></i></button>
                            <button><i class="bi bi-pencil" @click="handleEditUser(user)"></i></button>
                        </td>
                    </tr>

                </table>
            </div>
        </div>

        <div class="popup-backdrop" v-if="updateUserPopUpActive" @click.self="updateUserPopUpActive = false">
            <div class="pop-up-edit-user-container">
                <div class="popup-header">
                    <h3>Editar Usuario</h3>
                    <button class="close-btn" @click="updateUserPopUpActive = false">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <Form :validation-schema="schemaUpdateUser" :initial-values="userFormData" @submit="onSubmitUserUpdate">
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
                        <button type="button" class="cancel-btn" @click="updateUserPopUpActive = false">Cancelar</button>
                        <button type="submit" class="save-btn">Guardar Cambios</button>
                    </div>
                </Form>
            </div>
        </div>

</template>

<style scoped>

</style>