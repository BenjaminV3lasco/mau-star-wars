<script setup>
import { CLASES_CONTROL } from './control.js';

const valor = defineModel();
</script>

<template>
    <input v-model="valor" :class="CLASES_CONTROL">
</template>
