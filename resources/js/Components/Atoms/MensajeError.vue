<script setup>
import { onMounted } from 'vue';
import { reproducir } from '../../composables/useSonido.js';

onMounted(() => reproducir('error'));
</script>

<template>
    <p class="mt-2 text-xs leading-normal text-danger" role="alert">
        <slot />
    </p>
</template>
