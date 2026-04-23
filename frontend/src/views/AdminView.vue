<script setup>
    import { useAuthStore } from '../stores/authStore';
    import { useMessageStore } from '../stores/messageStore';
    import { onMounted, ref } from 'vue';
    
    import UsersAdminTab from '../components/UsersAdminTab.vue';

    const currentTab = ref("");
    const authStore = useAuthStore();
    const messageStore = useMessageStore();

    const users = ref([]);
    const loadingUsers = ref(false);

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

    //USER RELATED METHODS AND DATA FOR THE CHILD COMPONENT

    const handleFetchUser = async () => {
        const response = await authStore.getUsersAction();
        loadingUsers.value = true;
        
        if (response.success) {
            users.value = response.data;
            fetchedSections.user = true;
            loadingUsers.value = true;
        } else {
            users.value = [];
            fetchedSections.users = true;
            loadingUsers.value = false;
        }
    }


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

        <UsersAdminTab v-if="currentTab === 'users' && users.length" :users="users" @fetchUsers="handleFetchUser"></UsersAdminTab>

        <div v-else-if="currentTab === 'users' && loadingUsers">     
            <div class="spinner"></div>
            <p>Cargando usuarios...</p>
        </div>

        <div v-else-if="currentTab === 'users' && !loadingUsers">
            <div class="no-users-container">
                <h4>No hay usuarios en el sistema</h4>
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

    


    

</style>