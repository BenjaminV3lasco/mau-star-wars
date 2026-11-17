<script setup>
// Error de un campo. Lo enlaza CampoFormulario con aria-describedby.
import { onMounted } from 'vue';
import { reproducir } from '../../composables/useSonido.js';

onMounted(() => reproducir('error'));
</script>

<template>
    <p class="mt-2 text-xs leading-normal text-danger" role="alert">
        <slot />
    </p>
</template>
