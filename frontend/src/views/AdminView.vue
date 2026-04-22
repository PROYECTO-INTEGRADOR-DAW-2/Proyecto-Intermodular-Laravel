<script setup>
    import { useAuthStore } from '../stores/authStore';
    import { useMessageStore } from '../stores/messageStore';
    import { onMounted, ref } from 'vue';
    import { Form, Field, ErrorMessage } from 'vee-validate'
    import * as yup from 'yup';

    const currentTab = ref("");
    const authStore = useAuthStore();
    const messageStore = useMessageStore();

    const users = ref([]);

    const fetchedSections = {
        users: false
    }

    const handleTabChange = async (tab) => {
        switch (tab) {
            case 'users':
                if (fetchedSections.users) {
                    currentTab.value = 'users';
                } else {
                    currentTab.value = 'users';
                    const response = await authStore.getUsers();

                    if (response.success) {
                        users.value = response.data
                        fetchedSections.users = true;
                    }
                }
                
                break;
        
            default:
                break;
        }
    }

    //EDIT USER RELATED FUNCTIONS AND DATA

    const handleEditUser = (user) => {
        editUserPopUpActive.value = true;
        userFormData.value = {...user}
    }

    const editUserPopUpActive = ref(false);
    const userFormData = ref({});








    onMounted(async () => {
        currentTab.value = 'users';

        const response = await authStore.getUsers();

        if (response.success) {
            users.value = response.data;
            fetchedSections.users = true;
        }

    }) 

    
    
</script>

<template>
    <div class="main-container">
        <div class="tabs">
            <div class="tab" @click="handleTabChange('users')" :class="{'tab-active': currentTab === 'users'}">Usuarios</div>
            <div class="tab">Products</div>
            <div class="tab">Roles</div>
        </div>

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
        <div class="pop-up-edit-user-container" v-if="editUserPopUpActive === true">
                    <Form :validation-schema="schemaEditUser" :initial-values="userFormData" @submit="onSubmitProfile">
                        <div class="form-group">
                            <label>Nombre</label>
                            <Field type="text" name="nombre"></Field>
                            <ErrorMessage name="nombre" class="error-msg" />
                        </div>

                        <div class="form-group">
                            <label>Apellidos</label>
                            <Field type="text" name="apellidos"></Field>
                            <ErrorMessage name="apellidos" class="error-msg" />
                        </div>

                        <div class="form-group">
                            <label>Nombre usuario</label>
                            <Field type="text" name="nombre_usuario"></Field>
                            <ErrorMessage name="nombre_usuario" class="error-msg" />
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <Field type="text" name="email"></Field>
                            <ErrorMessage name="email" class="error-msg" />
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <Field as="select" name="rol">
                                <option value="admin"></option>
                                <option value="client"></option>
                            </Field>
                            <ErrorMessage name="rol" class="error-msg" />
                        </div>
                    </Form>
            </div>
    </div>

</template>

<style scoped>

    .main-container {
        display: grid;
        padding: 10px 20px;
        grid-template-rows: auto 1fr auto;
        min-height: 100vh;
        width: 100%;
    }

    .tabs {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        height: 50px;
        grid-template-rows: 1fr;
        width: 100%;
        border-bottom: 1px solid #222;
    }

    .tab {
        width: 100%;
        height: 100%;
        text-align: center;
        color: black;
        transition: all 0.3s ease-in-out;
        align-content: center;
        cursor: pointer;
    }

    .tab-active {
        background-color: #D72631;
        color: white;
    }

    .cart-table-wrapper {
        width: 100%;
        justify-self: center;
        max-height: 60vh;
        overflow-y: auto;
        margin: 20px 0;
        align-self: start;
        position: relative;
    }

    .users-table {
        width: 100%;
        background-color: #1F1F1F;
        color: white;
        border-collapse: collapse;
        table-layout: fixed;
        display: table;
    }

    .users-table tr {
        width: 100%;
        display: table-row; 
    }

    .users-table td, .users-table th {
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid #333;
    }

    .users-table th {
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




    .pop-up-edit-user-container {
        border-radius: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        background-color: white;
        padding: 20px;
        height: auto;
        position: fixed;
        top: 50%;
        left: 50%;
        width: 30em;
        z-index: 2;
    }

    .form-group input[type="text"], input[type="password"], input[type="email"] {
        height: 50px;
    }
    

    .form-group {
        display: grid;
        margin: 20px 0 20px 0; 
    }


    

</style>