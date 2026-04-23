<script setup>
    import { useAuthStore } from '../stores/authStore';
    import { useMessageStore } from '../stores/messageStore';
    import { onMounted, ref } from 'vue';
    import { Form, Field, ErrorMessage } from 'vee-validate'
    import * as yup from 'yup';
import UsersAdminTab from '../components/UsersAdminTab.vue';

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
                    const response = await authStore.getUsersAction();
                    loadingUsers.value = true;

                    if (response.success) {
                        users.value = response.data
                        fetchedSections.users = true;
                        loadingUsers.value = false;
                    } else {
                        fetchedSections.users = true;
                        loadingUsers.value = false;
                    }
                }
                
                break;
        
            default:
                break;
        }
    }

    //EDIT USER RELATED FUNCTIONS AND DATA

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

    




    onMounted(async () => {
        currentTab.value = 'users';
        
        const response = await authStore.getUsersAction();
        loadingUsers.value = true;

        if (response.success) {
            users.value = response.data;
            fetchedSections.users = true;
            loadingUsers.value = false;
        } else {
            fetchedSections.users = true;
            loadingUsers.value = false;
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

        <UsersAdminTab v-if="currentTab === 'users' && users.length" users="users"></UsersAdminTab>

        

        <div v-else-if="currentTab === 'users' && loadingUsers">     
            <div class="spinner"></div>
            <p>Cargando usuarios...</p>
        </div>

        <div v-else-if="currentTab === 'users' && !loadingUsers">
            <div class="no-users-container">
                <h4>No hay usuarios en el sistema</h4>
            </div>
        </div>
        

        <!-- Backdrop and Popup -->
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

    </div>

</template>

<style scoped>

    .spinner {
        width: 50px;
        height: 50px;
        border: 5px solid #f3f3f3;
        border-top: 5px solid #D72631;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-bottom: 15px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

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
    .pop-up-edit-user-container {
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


    

</style>