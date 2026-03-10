<script setup>

    import { useProductsStore } from '../stores/productsStore.js';

    const { importProducts } = useProductsStore();

    





    

</script>

<template>
    <h1>Importar productos</h1>
    <p>En esta sección podrás importar productos desde un archivo CSV. Asegúrate de que el archivo tenga el formato correcto antes de subirlo.</p>
    <form @submit.prevent="importProducts" method="post">
        <div class="mb-3">
            <label for="csv-file" class="form-label">Selecciona un archivo CSV</label>
            <input type="file" class="form-control" name="csv-file" id="csv-file">
        </div>
        <button type="submit" class="btn btn-primary">Importar</button>
    </form>


</template>

<style scoped>

</style>