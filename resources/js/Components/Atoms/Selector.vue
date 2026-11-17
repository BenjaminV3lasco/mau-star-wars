<script setup>
// <select> nativo con el estilo del sitio. Se mantiene nativo por accesibilidad
// y teclado; la lista desplegable se estiliza con CSS (clase selector-galactico).
import { CLASES_CONTROL } from './control.js';

const valor = defineModel();
</script>

<template>
    <select
        v-model="valor"
        :class="[CLASES_CONTROL, 'selector-galactico required:invalid:text-[#9299a4]']"
    >
        <slot />
    </select>
</template>
