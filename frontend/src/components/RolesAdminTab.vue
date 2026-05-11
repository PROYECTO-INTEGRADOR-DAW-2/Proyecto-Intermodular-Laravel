<script setup>
    import { onMounted, defineEmits, ref } from 'vue'
    import { useAuthStore } from '../stores/authStore';
    import { Form, Field, ErrorMessage } from 'vee-validate'
    import * as yup from 'yup';

    const authStore = useAuthStore();

    const props = defineProps({
        roles: Array
    })
    
    //Emit evento para obtencion de usuarios despues de realizar acciones CRUD 
    const emit = defineEmits(['fetchRoles']);

    //Variables para mostrar o no popups
    const updateRolePopUpActive = ref(false);
    const deleteRolePopUpActive = ref(false);
    const addRolePopUpActive = ref(false);
    const importRolesPopUpActive = ref(false);
    const getLogsButtonActive = ref(false);
    const showLogsPopUpActive = ref(false);

    //Informacion sobre usuario seleccionado para eliminar, actualizar, obtencion de logs
    const roleFormData = ref({});
    const roleSelectedToDelete = ref({});

    const logsData = ref(null);
    

    //Schemas de validacion para los diferentes tipos de formularios
    const schemaUpdateAddRole = yup.object({
        rol: yup.string().required('El nombre de rol es obligatorio'),
        descripcion: yup.string().required('Los apellidos del usuarios son obligatorios').max(255),
    })

    const schemaImportRoles = yup.object().shape({
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
    const handleEditRole = (role) => {
        updateRolePopUpActive.value = true;
        roleFormData.value = {...role}
    }

    const handleDeleteRole = (role) => {
        deleteRolePopUpActive.value = true;
        roleSelectedToDelete.value = role;
    }

    const handleAddRole = () => {
        addRolePopUpActive.value = true;
    }

    const handleImportRoles = () => {
        importRolesPopUpActive.value = true;
    }


    //Metodos relacionados con el CRUD de usuarios e importacion
    const onSubmitRoleUpdate = async (data, { setFieldError }) => {

        if (data?.id) {
            const response = await authStore.updateRoleAction(data.id, data);

            if (!response.success && response.info) {

                Object.entries(response.info).forEach(([field, messages]) => {
                    setFieldError(field, messages[0]);
                })

            } else {

                emit('fetchRoles')

                roleFormData.value = {};
                updateRolePopUpActive.value = false;
                

            }

        } else {
            messageStore.addMessage('error', 'No se ha proporcionado del id del rol');
        }
    }

    const onSubmitRoleDelete = async (roleId) => {

        deleteRolePopUpActive.value = false;


        if (roleId) {
            const response = await authStore.deleteRoleAction(roleId);

            if (response.success) {
                deleteRolePopUpActive.value = false;
                emit('fetchRoles')
            }

        } else {
            messageStore.addMessage('error', 'No se ha proporcionado el id del rol');
        }
    }

    const onSubmitAddRole = async (roleData, { setFieldError }) => {
        const response = await authStore.addRoleAction(roleData);

        if (!response.success && response.info) {
            Object.entries(response.info).forEach(([field, messages]) => {
                setFieldError(field, messages[0]);
            })
        } else {
            emit('fetchRoles')

            addRolePopUpActive.value = false;
        }
    }

    const onSubmitImportRoles = async (values, { setFieldError }) => {
        const formData = new FormData();
        formData.append("fichero", values.fichero);

        const response = await authStore.importRolesAction(formData);

        if (!response.success && response.info) {
            Object.entries(response.info).forEach(([field, messages]) => {
                setFieldError(field, messages[0]);
            })
        } else {
            emit('fetchRoles');
            getLogsButtonActive.value = true;
        }
    }


    //Metodos relacionados con logs de importacion
    const onSubmitGetLogs = async (page = 1) => {
        if (typeof page !== 'number') {
            page = 1;
        }

        const response = await authStore.getLogDataFromRoleImports(page);
        console.log(response.data);
        

        if (response.success) {
            logsData.value = response.data;
            importRolesPopUpActive.value = false;
            showLogsPopUpActive.value = true;
        }
    }

    const handlePreviousLogsPage = async () => {
        if (logsData.value?.previous_page) {
            const response = await authStore.getLogDataFromRoleImports(logsData.value.previous_page);

            if (response.success) {
                logsData.value = response.data;
            }
        }
    }

    const handleNextLogsPage = async () => {
        if (logsData.value?.next_page) {
            const response = await authStore.getLogDataFromRoleImports(logsData.value.next_page);

            if (response.success) {
                logsData.value = response.data;
            }
        }
    }

    
</script>

<template>
    <div style="margin-top: 2em;">
        <div class="buttons-container">
            <button class="create-role-button" @click="handleAddRole">Nuevo <i class="bi bi-plus"></i></button>
            <button class="import-roles-button" @click="handleImportRoles">Importar <i class="bi bi-plus"></i></button>
            <button class="show-logs-button" @click="onSubmitGetLogs">Obtener logs</button>
        </div>
        
        <div class="cart-table-wrapper">
            <div v-if="roles.length">
                <div class="updateTableButton"></div>
                <table class="roles-table">
                    
                    <tr>
                        <th>Id</th>
                        <th>Rol</th>
                        <th>Descripcion</th>
                        <th>Acciones</th>
                    </tr>
                    
                    <tr v-for="(role, index) in roles">
                        <td>{{ role.id }}</td>
                        <td>{{ role.rol }}</td>
                        <td>{{ role.descripcion }}</td>

                        <td class="actions">
                            <button @click="handleDeleteRole(role)"><i class="bi bi-trash"></i></button>
                            <button @click="handleEditRole(role)"><i class="bi bi-pencil"></i></button>
                        </td>
                    </tr>

                </table>
            </div>
        </div>

        <div class="popup-backdrop" v-if="updateRolePopUpActive" @click.self="updateRolePopUpActive = false">
            <div class="pop-up-edit-role-container">
                <div class="popup-header">
                    <h3>Editar Rol</h3>
                    <button class="close-btn" @click="updateRolePopUpActive = false">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <Form :validation-schema="schemaUpdateAddRole" :initial-values="roleFormData" @submit="onSubmitRoleUpdate">
                    
                    <div class="form-group">
                        <label>Rol</label>
                        <Field type="text" name="rol" placeholder="Nombre del rol"></Field>
                        <ErrorMessage name="rol" class="error-msg" />
                    </div>

                    <div class="form-group">
                        <label>Descripción</label>
                        <Field type="text" name="descripcion" placeholder="Descripcion"></Field>
                        <ErrorMessage name="descripcion" class="error-msg" />
                    </div>
                    

                    <div class="form-actions">
                        <button type="button" class="cancel-btn" @click="updateRolePopUpActive = false">Cancelar</button>
                        <button type="submit" class="save-btn">Guardar Cambios</button>
                    </div>
                </Form>
            </div>
        </div>

        <div class="popup-backdrop" v-if="deleteRolePopUpActive" @click.self="deleteRolePopUpActive = false">
            <div class="pop-up-edit-role-container">
                <div class="popup-header">
                    <h3>Eliminar Rol</h3>
                    <button class="close-btn" @click="deleteRolePopUpActive = false">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <h2 style="margin-bottom:30px">Estas seguro de querer eliminar el rol siguiente?</h2>

                <div class="role-data">

                    <div style="display: grid;">
                        <strong>Id</strong>
                        <p >{{ roleSelectedToDelete.id }}</p>
                    </div>
                    

                    <div style="display: grid; grid-template-columns: 1fr; grid-template-rows: 1fr 1fr;">
                        <div style="display: grid;">
                            <strong>Rol</strong>
                            <p >{{ roleSelectedToDelete.rol }}</p>
                        </div>

                        <div style="display: grid;">
                            <strong>Descripción</strong>
                            <p>{{ roleSelectedToDelete.descripcion }}</p>
                        </div>
                    </div>


                    <div class="form-actions">
                        <button type="button" class="cancel-btn" @click="deleteRolePopUpActive = false">Cancelar</button>
                        <button type="submit" class="delete-btn" @click="onSubmitRoleDelete(roleSelectedToDelete.id)">Eliminar rol</button>
                    </div>
                    
                </div>

                
            </div>
        </div>

        <div class="popup-backdrop" v-if="addRolePopUpActive" @click.self="addRolePopUpActive = false">
            <div class="pop-up-edit-role-container">
                <div class="popup-header">
                    <h3>Añadir Rol</h3>
                    <button class="close-btn" @click="addRolePopUpActive = false">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <Form :validation-schema="schemaUpdateAddRole" @submit="onSubmitAddRole">
                    
                    <div class="form-group">
                        <label>Rol</label>
                        <Field type="text" name="rol" placeholder="Nombre del rol"></Field>
                        <ErrorMessage name="rol" class="error-msg" />
                    </div>

                    <div class="form-group">
                        <label>Descripción</label>
                        <Field type="text" name="descripcion" placeholder="Descripción"></Field>
                        <ErrorMessage name="descripcion" class="error-msg" />
                    </div>
                    

                    <div class="form-actions">
                        <button type="button" class="cancel-btn" @click="addRolePopUpActive = false">Cancelar</button>
                        <button type="submit" class="add-btn">Añadir rol</button>
                    </div>
                </Form>
            </div>
        </div>

        <div class="popup-backdrop" v-if="importRolesPopUpActive" @click.self="importRolesPopUpActive = false">
            <div class="pop-up-edit-user-container">
                <div class="popup-header">
                    <h3>Importar Roles</h3>
                    <button class="close-btn" @click="importRolesPopUpActive = false">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <Form :validation-schema="schemaImportRoles" @submit="onSubmitImportRoles">
                    
                    <div class="form-group">
                        <label>Fichero (csv,xlsx,xls)</label>
                        <Field name="fichero" placeholder="Selecciona fichero" v-slot="{ handleChange, field }">
                            <input type="file" @change="handleChange"/>
                        </Field>
                        <ErrorMessage name="fichero" class="error-msg" />
                    </div>

                    <div class="form-actions">
                        <button type="button" class="cancel-btn" @click="importRolesPopUpActive = false">Cancelar</button>
                        <button type="submit" class="add-btn">Importar Roles</button>
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

    .create-role-button {
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

    .import-roles-button {
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

    .roles-table {
        width: 100%;
        background-color: #1F1F1F;
        color: white;
        border-collapse: collapse;
        table-layout: fixed;
        display: table;
    }

    .roles-table tr {
        width: 100%;
        display: table-row; 
    }

    .roles-table td, .roles-table th {
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid #333;
    }

    .roles-table th {
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
    .pop-up-edit-role-container {
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

    .user-data {
        display: grid;
        grid-template-rows: 1fr 1fr 1fr;
        row-gap: 20px;
    }

    .user-data p{
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