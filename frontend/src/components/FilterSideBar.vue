<script setup>
import { ref } from 'vue';

    const metaData = defineProps({
        max_price: Number
    })


    const query = ref({
        nombre: "",
        categoria: [],
        marca: [],
        deporte: [],
        altura: [],
        sexo: "",
        precio_max: metaData.max_price
    })

    const emit = defineEmits(['filter'])


</script>



<template>
    <div class="filter-container">
        
            <form @submit.prevent="emit('filter', query)" class="filter-form">
                <div class="input-group">
                    <label for="nombre" style="font-size: 20px;"><strong>Nombre</strong></label>
                    <input type="text" name="nombre" id="nombre" v-model="query.nombre">
                </div>


                <div class="input-group">
                    <label for="marca"><strong style="font-size: 20px;">Marca</strong></label >
                    <div class="checkbox-group">
                        <label for="adidas">Adidas <input type="checkbox" name="marca" id="adidas" value="Adidas" v-model="query.marca"></label>
                        <label for="nike">Nike <input type="checkbox" name="marca" id="nike" value="Nike" v-model="query.marca"></label>
                        <label for="asics">Asics <input type="checkbox" name="marca" id="asics" value="Asics" v-model="query.marca"></label>
                        <label for="puma">Puma <input type="checkbox" name="marca" id="puma" value="Puma" v-model="query.marca"></label>
                    </div>
                </div>
                

                <input type="submit" value="Filtrar" class="button">
            </form>
        
    </div>
</template>

<style scoped>
    .filter-form {
        display: grid;
    }

    .filter-container {
        grid-area: filter;
    }

    .checkbox-group {
        display: grid;
    }

    .input-group {
        display: grid;
        margin: 5px;
    }
</style>