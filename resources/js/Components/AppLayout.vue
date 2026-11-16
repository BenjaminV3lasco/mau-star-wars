<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const logoUrl = '/images/star-wars-logo.svg';
const rutaActual = computed(() => page.url.split('?')[0]);
const enlaces = [
    { nombre: 'Inicio', href: '/' },
    { nombre: 'Personajes', href: '/personajes' },
];
</script>

<template>
    <header class="site-header">
        <nav class="site-nav" aria-label="Navegación principal">
            <Link href="/" class="brand" aria-label="Star Wars, inicio">
                <img :src="logoUrl" alt="Star Wars" width="512" height="244">
            </Link>
            <span class="nav-divider" aria-hidden="true"></span>
            <span class="site-label">Archivo de personajes</span>
            <div class="nav-links">
                <template v-for="enlace in enlaces" :key="enlace.href">
                    <span v-if="rutaActual === enlace.href" class="nav-link" aria-current="page">{{ enlace.nombre }}</span>
                    <Link v-else :href="enlace.href" class="nav-link">{{ enlace.nombre }}</Link>
                </template>
            </div>
        </nav>
    </header>
    <slot />
</template>
