<script setup>
    import { onMounted, ref, watch } from 'vue';

    const props = defineProps({
        metaData: {
            type: [Object, Array],
            default: () => ({ max_price: 1000 })
        },
        initialQuery: Object
    })


    const query = ref({
        nombre: "",
        categoria: [],
        marca: [],
        deporte: [],
        altura: [],
        sexo: [],
        precio_max: 1000
    })


    const resetFilters = () => {
        const meta = Array.isArray(props.metaData) ? props.metaData[0] : props.metaData;
        query.value = {
            nombre: "",
            categoria: [],
            marca: [],
            deporte: [],
            altura: [],
            sexo: [],
            ofertas: false,
            novedades: false,
            precio_max: meta?.products?.max_price || 1000
        }
    }

    const isPropInQuery = (key, value) => {
        if (!query.value[key] || !Array.isArray(query.value[key])) return false;

        return query.value[key].some(item => item.toLowerCase() === value.toLowerCase());
    }

    // ---- EMITS ----
    const emit = defineEmits(['filter'])



    // ---- HOOKS ----

    watch(() => props.metaData, (newVal) => {
        const meta = Array.isArray(newVal) ? newVal[0] : newVal;

        if (meta?.products?.max_price) {
            query.value.precio_max = meta?.products?.max_price;
        }

    }, { immediate: true });

    watch(() => props.initialQuery, (newQuery) => {

        if (newQuery) {
            query.value = { ...query.value, ...newQuery };
        }

    }, { deep: true , immediate: true});




</script>



<template>
    <div class="filter-container">
        
            <form @submit.prevent="emit('filter', query)" class="filter-form">
                <div class="filter-field">
                    <label for="nombre" style="font-size: 20px;"><strong>Nombre</strong></label>
                    <input type="text" name="nombre" id="nombre" v-model="query.nombre">
                </div>


                <div class="filter-field">
                    <label><strong style="font-size: 20px;">Categoría</strong><input type="checkbox" class="dropdown-toggler"> <span class="dropdown-icon"></span></label>
                    <div class="checkbox-group">
                        <div>
                            <label for="zapatillas" class="checkbox-item">Zapatillas <input type="checkbox" name="categoria" id="zapatillas" value="Zapatillas" v-model="query.categoria"><span class="checkmark" :class="{ 'checkmark-active' : isPropInQuery('categoria', 'Zapatillas') }"></span></label>
                            <label for="camisetas" class="checkbox-item">Camisetas <input type="checkbox" name="categoria" id="camisetas" value="Camisetas" v-model="query.categoria"><span class="checkmark" :class="{ 'checkmark-active' : isPropInQuery('categoria', 'Camisetas') }"></span></label>
                            <label for="pantalones" class="checkbox-item">Pantalones <input type="checkbox" name="categoria" id="pantalones" value="Pantalones" v-model="query.categoria"><span class="checkmark" :class="{ 'checkmark-active' : isPropInQuery('categoria', 'Pantalones') }"></span></label>
                            <label for="calcetines" class="checkbox-item">Calcetines <input type="checkbox" name="categoria" id="calcetines" value="Calcetines" v-model="query.categoria"><span class="checkmark" :class="{ 'checkmark-active' : isPropInQuery('categoria', 'Calcetines') }"></span></label>
                            <label for="accesorios" class="checkbox-item">Accesorios <input type="checkbox" name="categoria" id="accesorios" value="Accesorios" v-model="query.categoria"><span class="checkmark" :class="{ 'checkmark-active' : isPropInQuery('categoria', 'Accesorios') }"></span></label>
                        </div>
                    </div>
                </div>

                <div class="filter-field">
                    <label><strong style="font-size: 20px;">Marca</strong><input type="checkbox" class="dropdown-toggler"> <span class="dropdown-icon"></span></label >
                    <div class="checkbox-group">
                        <div>
                            <label for="adidas" class="checkbox-item">Adidas <input type="checkbox" name="marca" id="adidas" value="Adidas" v-model="query.marca"><span class="checkmark" :class="{ 'checkmark-active' : isPropInQuery('marca', 'Adidas') }"></span></label>
                            <label for="nike" class="checkbox-item">Nike <input type="checkbox" name="marca" id="nike" value="Nike" v-model="query.marca"><span class="checkmark" :class="{ 'checkmark-active' : isPropInQuery('marca', 'Nike') }"></span></label>
                            <label for="asics" class="checkbox-item">Asics <input type="checkbox" name="marca" id="asics" value="Asics" v-model="query.marca"><span class="checkmark" :class="{ 'checkmark-active' : isPropInQuery('marca', 'Asics') }"></span></label>
                            <label for="puma" class="checkbox-item">Puma <input type="checkbox" name="marca" id="puma" value="Puma" v-model="query.marca"><span class="checkmark" :class="{ 'checkmark-active' : isPropInQuery('marca', 'Puma') }"></span></label>
                        </div>
                    </div>
                </div>

                <div class="filter-field">
                    <label><strong style="font-size: 20px;">Deporte</strong><input type="checkbox" class="dropdown-toggler"> <span class="dropdown-icon"></span></label >
                    <div class="checkbox-group">
                        <div>
                            <label for="futbol" class="checkbox-item">Fútbol <input type="checkbox" name="deporte" id="futbol" value="Fútbol" v-model="query.deporte"><span class="checkmark" :class="{ 'checkmark-active' : isPropInQuery('deporte', 'Fútbol') }"></span></label>
                            <label for="basket" class="checkbox-item">Baloncesto <input type="checkbox" name="deporte" id="basket" value="Baloncesto" v-model="query.deporte"><span class="checkmark" :class="{ 'checkmark-active' : isPropInQuery('deporte', 'Baloncesto') }"></span></label>
                            <label for="tenis" class="checkbox-item">Tenis <input type="checkbox" name="deporte" id="tenis" value="Tenis" v-model="query.deporte"><span class="checkmark" :class="{ 'checkmark-active' : isPropInQuery('deporte', 'Tenis') }"></span></label>
                            <label for="padel" class="checkbox-item">Padel <input type="checkbox" name="deporte" id="padel" value="Padel" v-model="query.deporte"><span class="checkmark" :class="{ 'checkmark-active' : isPropInQuery('deporte', 'Padel') }"></span></label>
                            <label for="trail" class="checkbox-item">Trail <input type="checkbox" name="deporte" id="trail" value="Trail" v-model="query.deporte"><span class="checkmark" :class="{ 'checkmark-active' : isPropInQuery('deporte', 'Trail') }"></span></label>
                        </div>
                    </div>
                </div>

                <div class="filter-field">
                    <label><strong style="font-size: 20px;">Altura</strong><input type="checkbox" class="dropdown-toggler"> <span class="dropdown-icon"></span></label >
                    <div class="checkbox-group">
                        <div>
                            <label for="alto" class="checkbox-item">Alto <input type="checkbox" name="altura" id="alto" value="Alto" v-model="query.altura"><span class="checkmark" :class="{ 'checkmark-active' : isPropInQuery('altura', 'Alto') }"></span></label>
                            <label for="normal" class="checkbox-item">Normal <input type="checkbox" name="altura" id="normal" value="Normal" v-model="query.altura"><span class="checkmark" :class="{ 'checkmark-active' : isPropInQuery('altura', 'Normal') }"></span></label>
                            <label for="bajo" class="checkbox-item">Bajo <input type="checkbox" name="altura" id="bajo" value="Bajo" v-model="query.altura"><span class="checkmark" :class="{ 'checkmark-active' : isPropInQuery('altura', 'Bajo') }"></span></label>
                        </div>
                    </div>
                </div>

                <div class="filter-field">
                    <label><strong style="font-size: 20px;">Sexo</strong><input type="checkbox" class="dropdown-toggler"> <span class="dropdown-icon"></span></label >
                    <div class="checkbox-group">
                        <div>
                            <label for="hombre" class="checkbox-item">Hombre<input type="checkbox" name="sexo" id="hombre" value="Hombre" v-model="query.sexo"><span class="checkmark" :class="{ 'checkmark-active' : isPropInQuery('sexo', 'Hombre') }"></span></label>
                            <label for="mujer" class="checkbox-item">Mujer <input type="checkbox" name="sexo" id="mujer" value="Mujer" v-model="query.sexo" ><span class="checkmark" :class="{ 'checkmark-active' : isPropInQuery('sexo', 'Mujer') }"></span></label>
                            <label for="nino" class="checkbox-item">Niño <input type="checkbox" name="sexo" id="nino" value="Niño" v-model="query.sexo"><span class="checkmark" :class="{ 'checkmark-active' : isPropInQuery('sexo', 'Niño') }"></span></label>
                            <label for="nina" class="checkbox-item">Nina <input type="checkbox" name="sexo" id="nina" value="Nina" v-model="query.sexo"><span class="checkmark" :class="{ 'checkmark-active' : isPropInQuery('sexo', 'Niña') }"></span></label>
                        </div>
                    </div>
                </div>

                <div class="filter-field">
                    <label for="precio_max"><strong style="font-size: 20px;">Precio maximo: <span id="valor-seleccionado"> {{ query.precio_max }} </span></strong> </label >
                    <input type="range" min="0" v-bind:max="props.metaData?.products?.max_price || 1000" v-model="query.precio_max">
                </div>

                <div class="filter-field">
                    <label for="novedades" class="checkbox-item-toggler"><strong style="font-size: 20px;">Novedades </strong><input type="checkbox" name="novedades" id="novedades" v-model="query.novedades"><span class="custom-toggler"></span></label >
                </div>

                <div class="filter-field">
                    <label for="ofertas" class="checkbox-item-toggler"><strong style="font-size: 20px;">Ofertas </strong><input type="checkbox" name="ofertas" id="ofertas" v-model="query.ofertas"><span class="custom-toggler"></span></label >
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
        grid-template-rows: 0fr;
        overflow: hidden;
        transition: grid-template-rows 0.5s ease;
    }

    .checkbox-group > div {
        min-height: 0;
        display: grid;
    }

    .filter-field {
        display: grid;
        margin: 5px;
    }

    .filter-field > label {
        display: grid;
        align-items: center;
        grid-template-columns: 1fr auto;
        cursor: pointer;
        padding: 5px 0;
    }

    .filter-field > label .dropdown-toggler {
        display: none;
    }

    .filter-field > label .dropdown-icon {
        justify-self: center;
        border-left: 10px solid transparent;
        border-right: 10px solid transparent;
        border-top: 10px solid #1F1F1F;
        transition: transform 0.3s ease;
        margin-left: 10px;
    }

    /* Selector corregido y animación de altura */
    .filter-field:has(.dropdown-toggler:checked) .checkbox-group {
        grid-template-rows: 1fr;
    }

    .filter-field:has(.dropdown-toggler:checked) .dropdown-icon {
        transform: rotate(180deg);
    }


    .checkbox-item {
        cursor: pointer;
        padding: 10px;
        display: grid;
        align-items: center;
        grid-template-columns: 1fr 1fr;
    }

    .checkbox-item input{
        display: none;
    }

    .checkmark {
        width: 20px;
        height: 20px;
        border: 2px solid #D72631;
        border-radius: 4px;
        display: inline-block;
        position: relative;
        transition: all 0.3s ease;
    }

    .checkmark::after {
        content: "";
        position: absolute;
        display: none;
        left: 6px;
        top: 2px;
        width: 5px;
        height: 10px;
        border: solid white;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }

    .checkbox-item input:checked + .checkmark {
        background-color: #1F1F1F;
    }

    .checkbox-item input:checked + .checkmark::after {
        display: block;
    }

    .checkmark-active {
        background-color: #1F1F1F;
    }

    .checkmark-active::after {
        display: block;
    }
    




    .checkbox-item-toggler {
        cursor: pointer;
        padding: 10px;
        display: grid;
        align-items: center;
        grid-template-columns: 1fr 1fr;
    }

    .checkbox-item-toggler input{
        display: none;
    }

    .checkbox-item-toggler input:checked + .custom-toggler {
        background-color: #ffdcdc;
    }

    .checkbox-item-toggler input:checked + .custom-toggler::after {
        transform: translateX(100%);
    }

    .custom-toggler {
        width: 40px;
        height: 20px;
        border: 2px solid #D72631;
        border-radius: 20px;
        display: inline-block;
        position: relative;
        justify-self: center;
        transition: all 0.5s ease;
    }

    .custom-toggler::after {
        content: "";
        left: 3px;
        top: 1px;
        position: absolute;
        width: 15px;
        height: 90%;
        background-color: black;
        border-radius: 100%;
        transition: all 0.5s ease;  
    }


    .custom-toggler-active {
        background-color: #cf8489;
    }









    

</style>