<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    metadata: {
        type: Object,
        default: () => ({ marcas: [], categories: [], sexos: [], tallas: [], maxPrice: 1000 })
    },
    initialFilters: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['filter-change']);

const buildFilters = (f, maxPrice) => ({
    search: f.search || '',
    marca: f.marca ? (Array.isArray(f.marca) ? f.marca : [f.marca]) : [],
    categoria: f.categoria ? (Array.isArray(f.categoria) ? f.categoria : [f.categoria]) : [],
    talla: f.talla ? (Array.isArray(f.talla) ? f.talla : [f.talla]) : [],
    sexo: f.sexo ? (Array.isArray(f.sexo) ? f.sexo : [f.sexo]) : [],
    min_price: f.min_price || 0,
    max_price: f.max_price || maxPrice,
    oferta: f.oferta === 'true' || f.oferta === true
});

const filters = ref(buildFilters(props.initialFilters, props.metadata.maxPrice));

// ── Watcher 1: Sync filters when the route query changes (e.g. clicking Hombre/Mujer in navbar)
watch(() => props.initialFilters, (newFilters) => {
    filters.value = buildFilters(newFilters, props.metadata.maxPrice);
}, { deep: true });

// ── Watcher 2 (C3 - RA3.g): Debounce watcher on search field
// Auto-applies the search filter 300ms after the user stops typing
let searchDebounceTimer = null;
watch(() => filters.value.search, (newSearch) => {
    clearTimeout(searchDebounceTimer);
    searchDebounceTimer = setTimeout(() => {
        emit('filter-change', { ...filters.value });
    }, 300);
});

const applyFilters = () => {
    emit('filter-change', { ...filters.value });
};

const resetFilters = () => {
    filters.value = {
        search: '',
        marca: [],
        categoria: [],
        talla: [],
        sexo: [],
        min_price: 0,
        max_price: props.metadata.maxPrice,
        oferta: false
    };
    applyFilters();
};
</script>

<template>
    <div class="filter-sidebar">
        <h5 class="mb-3 fw-bold">Filtros</h5>
        
        <!-- Search -->
        <div class="mb-3">
             <label class="form-label">Buscar</label>
             <input v-model.lazy="filters.search" type="text" class="form-control" placeholder="Nombre...">
        </div>

        <!-- Gender -->
        <div class="mb-3">
            <label class="form-label fw-bold">Género</label>
            <div v-for="sexo in metadata.sexos" :key="sexo" class="form-check">
                <input class="form-check-input" type="checkbox" :value="sexo" v-model="filters.sexo" :id="'sexo-'+sexo">
                <label class="form-check-label" :for="'sexo-'+sexo">{{ sexo }}</label>
            </div>
        </div>

        <!-- Brands -->
        <div class="mb-3">
            <label class="form-label fw-bold">Marca</label>
            <div v-for="marca in metadata.marcas" :key="marca" class="form-check">
                <input class="form-check-input" type="checkbox" :value="marca" v-model="filters.marca" :id="'marca-'+marca">
                <label class="form-check-label" :for="'marca-'+marca">{{ marca }}</label>
            </div>
        </div>
        
        <!-- Categories -->
        <div class="mb-3">
            <label class="form-label fw-bold">Categoría</label>
            <div v-for="cat in metadata.categorias" :key="cat" class="form-check">
                <input class="form-check-input" type="checkbox" :value="cat" v-model="filters.categoria" :id="'cat-'+cat">
                <label class="form-check-label" :for="'cat-'+cat">{{ cat }}</label>
            </div>
        </div>

        <!-- Price Range -->
        <div class="mb-3">
            <label class="form-label fw-bold">Precio Máximo: {{ filters.max_price }} €</label>
            <input type="range" class="form-range" min="0" :max="metadata.maxPrice" step="5" v-model.number="filters.max_price">
        </div>

        <!-- Offer -->
        <div class="mb-3 form-check form-switch">
             <input class="form-check-input" type="checkbox" id="offerSwitch" v-model="filters.oferta">
             <label class="form-check-label" for="offerSwitch">Solo Ofertas</label>
        </div>

        <div class="d-grid gap-2">
            <button class="btn btn-primary" @click="applyFilters">Aplicar Filtros</button>
            <button class="btn btn-outline-dark" @click="resetFilters">Limpiar Filtros</button>
        </div>
    </div>
</template>
