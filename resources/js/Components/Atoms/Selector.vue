<script setup>
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
