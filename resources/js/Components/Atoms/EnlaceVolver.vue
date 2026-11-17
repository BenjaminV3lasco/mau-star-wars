<script setup>
// Enlace para volver al listado. Recibe href para conservar filtros y página.
import { Link } from '@inertiajs/vue3';

defineProps({ href: { type: String, default: '/personajes' } });
</script>

<template>
    <Link :href="href" class="inline-flex min-h-11 items-center gap-3 text-ui text-[#aeb2bb] hover:text-gold light:text-[#525966]">
        <span aria-hidden="true">←</span> Volver al archivo
    </Link>
</template>
