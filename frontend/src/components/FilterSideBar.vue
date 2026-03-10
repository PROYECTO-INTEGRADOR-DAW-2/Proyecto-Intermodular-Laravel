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
        sexo: [],
        precio_max: metaData.max_price
    })

    const emit = defineEmits(['filter'])

    const resetFilters = () => {
        query.value = {
            nombre: "",
            categoria: [],
            marca: [],
            deporte: [],
            altura: [],
            sexo: [],
            precio_max: metaData.max_price
        }
    }


</script>



<template>
    <div class="filter-container">
        
            <form @submit.prevent="emit('filter', query)" class="filter-form">
                <div class="filter-field">
                    <label for="nombre" style="font-size: 20px;"><strong>Nombre</strong></label>
                    <input type="text" name="nombre" id="nombre" v-model="query.nombre">
                </div>


                <div class="filter-field">
                    <label for="categoria"><strong style="font-size: 20px;">Categoría</strong></label >
                    <div class="checkbox-group">
                        <label for="zapatillas">Zapatillas <input type="checkbox" name="categoria" id="zapatillas" value="Zapatillas" v-model="query.categoria"></label>
                        <label for="camisetas">Camisetas <input type="checkbox" name="categoria" id="camisetas" value="Camisetas" v-model="query.categoria"></label>
                        <label for="pantalones">Pantalones <input type="checkbox" name="categoria" id="pantalones" value="Pantalones" v-model="query.categoria"></label>
                        <label for="calcetines">Calcetines <input type="checkbox" name="categoria" id="calcetines" value="Calcetines" v-model="query.categoria"></label>
                        <label for="accesorios">Accesorios <input type="checkbox" name="categoria" id="accesorios" value="Accesorios" v-model="query.categoria"></label>
                    </div>
                </div>

                <div class="filter-field">
                    <label for="marca"><strong style="font-size: 20px;">Marca</strong></label >
                    <div class="checkbox-group">
                        <label for="adidas">Adidas <input type="checkbox" name="marca" id="adidas" value="Adidas" v-model="query.marca"></label>
                        <label for="nike">Nike <input type="checkbox" name="marca" id="nike" value="Nike" v-model="query.marca"></label>
                        <label for="asics">Asics <input type="checkbox" name="marca" id="asics" value="Asics" v-model="query.marca"></label>
                        <label for="puma">Puma <input type="checkbox" name="marca" id="puma" value="Puma" v-model="query.marca"></label>
                    </div>
                </div>

                <div class="filter-field">
                    <label for="deporte"><strong style="font-size: 20px;">Deporte</strong></label >
                    <div class="checkbox-group">
                        <label for="futbol">Fútbol <input type="checkbox" name="deporte" id="futbol" value="Fútbol" v-model="query.deporte"></label>
                        <label for="basket">Baloncesto <input type="checkbox" name="deporte" id="basket" value="Baloncesto" v-model="query.deporte"></label>
                        <label for="tenis">Tenis <input type="checkbox" name="deporte" id="tenis" value="Tenis" v-model="query.deporte"></label>
                        <label for="padel">Padel <input type="checkbox" name="deporte" id="padel" value="Padel" v-model="query.deporte"></label>
                        <label for="trail">Trail <input type="checkbox" name="deporte" id="trail" value="Trail" v-model="query.deporte"></label>
                    </div>
                </div>

                <div class="filter-field">
                    <label for="altura"><strong style="font-size: 20px;">Altura</strong></label >
                    <div class="checkbox-group">
                        <label for="alto">Alto <input type="checkbox" name="altura" id="alto" value="Alto" v-model="query.altura"></label>
                        <label for="normal">Normal <input type="checkbox" name="altura" id="normal" value="Normal" v-model="query.altura"></label>
                        <label for="bajo">Bajo <input type="checkbox" name="altura" id="bajo" value="Bajo" v-model="query.altura"></label>
                    </div>
                </div>

                <div class="filter-field">
                    <label for="sexo"><strong style="font-size: 20px;">Sexo</strong></label >
                    <div class="checkbox-group">
                        <label for="hombre">Hombre <input type="checkbox" name="sexo" id="hombre" value="Hombre" v-model="query.sexo"></label>
                        <label for="mujer">Mujer <input type="checkbox" name="sexo" id="mujer" value="Mujer" v-model="query.sexo"></label>
                        <label for="nino">Niño <input type="checkbox" name="sexo" id="nino" value="Niño" v-model="query.sexo"></label>
                        <label for="nina">Nina <input type="checkbox" name="sexo" id="nina" value="Nina" v-model="query.sexo"></label>
                    </div>
                </div>

                <div class="filter-field">
                    <label for="precio_max"><strong style="font-size: 20px;">Precio maximo: <span id="valor-seleccionado"> {{ query.precio_max }} </span></strong> </label >
                    <input type="range" min="0" v-bind:max="metaData.max_price" v-model="query.precio_max">
                    
                </div>

                <button @click="resetFilters" class="button-secondary" style="margin:10px 0 10px 0">Restablecer filtros</button>
                    
                <input type="submit" value="Filtrar" class="button" style="margin:10px 0 10px 0">
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
        margin-left: 15px;
    }

    .filter-field {
        display: grid;
        margin: 5px;
    }
</style>