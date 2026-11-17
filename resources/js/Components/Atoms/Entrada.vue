<script setup>
// Input con el estilo común del sitio. defineModel permite usarlo con v-model
// igual que un <input> nativo; el resto de los atributos pasa directo al elemento.
import { CLASES_CONTROL } from './control.js';

const valor = defineModel();
</script>

<template>
    <input v-model="valor" :class="CLASES_CONTROL">
</template>
