<script setup>
import { CLASES_CONTROL } from './control.js';

const valor = defineModel();
</script>

<template>
    <select
        v-model="valor"
        :class="[
            CLASES_CONTROL,
            'flecha-select required:invalid:text-[#9299a4]',
            '[&_option]:bg-[#101318] [&_option]:text-[#f4f4f5] light:[&_option]:bg-white light:[&_option]:text-[#252830]',
        ]"
    >
        <slot />
    </select>
</template>
