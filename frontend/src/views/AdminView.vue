<script setup>
    import { useAuthStore } from '../stores/authStore';
    import { onMounted, ref } from 'vue';

    const currentTab = ref("");
    const authStore = useAuthStore();

    const users = ref(null);

    onMounted(async () => {
        const response = await authStore.getUsers();

        if (response.success) users.value = response.data
    })

    const fetchedSections = {
        users: false
    }

    const handleTabChange = async (tab) => {
        switch (tab) {
            case 'users':
                if (fetchedSections.users) {
                    currentTab = 'users';
                } else {
                    currentTab = 'users';
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

    
    
</script>

<template>
    <div class="main-container">
        <div class="tabs-containers">
            <div class="tab" @click="handleTabChange('users')">Usuarios</div>
            <div class="tab">Products</div>
            <div class="tab">Roles</div>
        </div>

        <div class="current-tab-container">
            <div v-if="currentTab === 'users' && users.length">
                <div class="updateTableButton"></div>
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Nombre</th>
                            <th>Apellidos</th>
                            <th>Nombre usuario</th>
                            <th>Email</th>
                            <th>Rol</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(user, index) in users">
                            <td>{{ user.id }}</td>
                            <td>{{ user.nombre }}</td>
                            <td>{{ user.apellidos }}</td>
                            <td>{{ user.nombre_usuario }}</td>
                            <td>{{ user.email }}</td>
                            <td>{{ user.rol }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</template>

<style scoped>

    .main-container {
        display: grid;
        width: 90%;
    }

</style>