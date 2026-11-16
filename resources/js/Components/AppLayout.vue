<script setup>
import { computed, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const logoUrl = '/images/star-wars-logo.svg';
const claveTema = 'mau-star-wars-theme';
const rutaActual = computed(() => page.url.split('?')[0]);
const tema = ref(typeof document !== 'undefined' ? document.documentElement.dataset.theme || 'dark' : 'dark');
const enlaces = [
    { nombre: 'Inicio', href: '/' },
    { nombre: 'Personajes', href: '/personajes' },
];

function alternarTema() {
    tema.value = tema.value === 'dark' ? 'light' : 'dark';

    if (typeof document !== 'undefined') {
        document.documentElement.dataset.theme = tema.value;
        document.querySelector('meta[name="theme-color"]')?.setAttribute('content', tema.value === 'dark' ? '#090a0c' : '#f5f3ed');

        try {
            window.localStorage.setItem(claveTema, tema.value);
        } catch {
            // El modo sigue funcionando durante esta visita si el almacenamiento está bloqueado.
        }
    }
}
</script>

<template>
    <header class="site-header">
        <nav class="site-nav" aria-label="Navegación principal">
            <Link href="/" class="brand" aria-label="Star Wars, inicio">
                <img :src="logoUrl" alt="Star Wars" width="512" height="244">
            </Link>
            <span class="nav-divider" aria-hidden="true"></span>
            <span class="site-label">Archivo de personajes</span>
            <div class="nav-actions">
                <div class="nav-links">
                    <template v-for="enlace in enlaces" :key="enlace.href">
                        <span v-if="rutaActual === enlace.href" class="nav-link" aria-current="page">{{ enlace.nombre }}</span>
                        <Link v-else :href="enlace.href" class="nav-link">{{ enlace.nombre }}</Link>
                    </template>
                </div>
                <button
                    type="button"
                    class="theme-toggle"
                    :aria-pressed="tema === 'light'"
                    :aria-label="tema === 'dark' ? 'Activar modo claro' : 'Activar modo oscuro'"
                    @click="alternarTema"
                >
                    <span class="theme-toggle-icon" aria-hidden="true">{{ tema === 'dark' ? '☼' : '◐' }}</span>
                    <span>{{ tema === 'dark' ? 'Modo claro' : 'Modo oscuro' }}</span>
                </button>
            </div>
        </nav>
    </header>
    <slot />
</template>
