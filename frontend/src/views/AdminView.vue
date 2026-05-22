<script setup>
    import { useAuthStore } from '../stores/authStore';
    import { useMessageStore } from '../stores/messageStore';
    import { onMounted, ref } from 'vue';
    
    import UsersAdminTab from '../components/UsersAdminTab.vue';
    import RolesAdminTab from '../components/RolesAdminTab.vue';
import ProductsAdminTab from '../components/ProductsAdminTab.vue';

    const currentTab = ref("");
    const authStore = useAuthStore();
    const messageStore = useMessageStore();

    const users = ref([]);
    const roles = ref([]);
    const products = ref([]);
    const productsMetaData = ref(null);
    const loadingUsers = ref(false);
    const loadingRoles = ref(false);
    const loadingProducts = ref(false);

    const fetchedSections = ref({
        users: false,
        roles: false,
        products: false
    })

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
                        fetchedSections.value.users = true;
                        loadingUsers.value = false;
                    } else {
                        fetchedSections.value.users = true;
                        loadingUsers.value = false;
                    }
                }
                
                break;
            case 'products':
                if (fetchedSections.value.products) {
                    currentTab.value = 'products';
                } else {
                    currentTab.value = 'products';
                    loadingProducts.value = true;
                    products.value = [];
                    productsMetaData.value = null;
                    
                    const response = await authStore.getAllProductsAction();

                    if (response.success) {
                        products.value = response.data.data;
                        productsMetaData.value = response.data.meta || null;
                        fetchedSections.value.products = true;
                        loadingProducts.value = false;
                    } else {
                        fetchedSections.value.products = true;
                        loadingProducts.value = false;
                    }
                }

                break;
            case 'roles':
                if (fetchedSections.value.roles) {
                    currentTab.value = 'roles';
                } else {
                    currentTab.value = 'roles';
                    loadingRoles.value = true;
                    roles.value = [];
                    
                    const response = await authStore.getAllRolesAction();

                    if (response.success) {
                        roles.value = response.data.data;
                        fetchedSections.value.roles = true;
                        loadingRoles.value = false;
                    } else {
                        fetchedSections.value.roles = true;
                        loadingRoles.value = false;
                    }
                }
        
            default:
                break;
        }
    }

    //USER RELATED METHODS AND DATA FOR THE CHILD COMPONENT

    const handleFetchUser = async () => {

        users.value = [];
        loadingUsers.value = true;
        fetchedSections.value.users = false;

        const response = await authStore.getUsersAction();

        if (response.success) {
            users.value = response.data;
            fetchedSections.value.users = true;
            loadingUsers.value = true;
        } else {
            users.value = [];
            fetchedSections.value.users = true;
            loadingUsers.value = false;
        }
    }

    const handleFetchRoles = async () => {
        
        roles.value = [];
        loadingRoles.value = true;
        fetchedSections.value.roles = false;

        const response = await authStore.getAllRolesAction();

        if (response.success) {
            roles.value = response.data.data;
            fetchedSections.value.roles = true;
            loadingRoles.value = false;
        } else {
            fetchedSections.value.roles = true;
            loadingRoles.value = false;
        }
    }

    const handleFetchProducts = async () => {
        
        products.value = [];
        productsMetaData.value = null;
        loadingProducts.value = true;
        fetchedSections.value.products = false;

        const response = await authStore.getAllProductsAction();

        if (response.success) {
            products.value = response.data.data;
            productsMetaData.value = response.data.meta || null;
            fetchedSections.value.products = true;
            loadingProducts.value = false;
        } else {
            fetchedSections.value.products = true;
            loadingProducts.value = false;
        }
    }


    onMounted(async () => {
        currentTab.value = 'users';
        loadingUsers.value = true;
        
        const response = await authStore.getUsersAction();
        loadingUsers.value = true;

        if (response.success) {
            users.value = response.data;
            fetchedSections.value.users = true;
            loadingUsers.value = false;
        } else {
            fetchedSections.value.users = true;
            loadingUsers.value = false;
        }

    }) 

    
    
</script>
    
<template>
    <div class="main-container">
        <div class="tabs">
            <div class="tab" @click="handleTabChange('users')" :class="{'tab-active': currentTab === 'users'}">Usuarios</div>
            <div class="tab" @click="handleTabChange('products')" :class="{'tab-active': currentTab === 'products'}">Products</div>
            <div class="tab" @click="handleTabChange('roles')" :class="{'tab-active': currentTab === 'roles'}">Roles</div>
        </div>

        <UsersAdminTab v-if="currentTab === 'users' && users.length" :users="users" @fetchUsers="handleFetchUser"></UsersAdminTab>
        
        <div v-else-if="currentTab === 'users' && loadingUsers" class="spinner-container">     
            <div class="spinner"></div>
            <p>Cargando usuarios...</p>
        </div>

        <div v-else-if="currentTab === 'users' && !loadingUsers && fetchedSections.users">
            <div class="no-users-container">
                <h4>No hay usuarios en el sistema</h4>
            </div>
        </div>

        <ProductsAdminTab v-if="currentTab === 'products' && products.length" :products="products" :metaData="productsMetaData" @fetchProducts="handleFetchProducts"></ProductsAdminTab>

        <div v-else-if="currentTab === 'products' && loadingProducts" class="spinner-container">     
            <div class="spinner"></div>
            <p>Cargando productos...</p>
        </div>

        <div v-else-if="currentTab === 'products' && !loadingProducts && fetchedSections.products">
            <div class="no-users-container">
                <h4>No hay productos en el sistema</h4>
            </div>
        </div>

        <RolesAdminTab v-if="currentTab === 'roles' && roles.length" :roles="roles" @fetchRoles="handleFetchRoles"></RolesAdminTab>

        <div v-else-if="currentTab === 'roles' && loadingRoles" class="spinner-container">     
            <div class="spinner"></div>
            <p>Cargando roles...</p>
        </div>

        <div v-else-if="currentTab === 'roles' && !loadingRoles && fetchedSections.roles">
            <div class="no-users-container">
                <h4>No hay roles en el sistema</h4>
            </div>
        </div>
        

    </div>

</template>

<style scoped>

    .spinner-container {
        justify-self: center;
        align-self: center;
        justify-items: center;
    }

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

    


    

</style>